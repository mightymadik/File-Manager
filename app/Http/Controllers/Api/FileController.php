<?php

namespace App\Http\Controllers\Api;
use App\Models\File;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use DB;

class FileController extends Controller
{
    // Метод для получения информации о файле по его ID
    public function edit($id) {
        $file = DB::table('files')->where('id', $id)->first();
        return $file;
    }   
    
    // Метод для обновления информации о файле
    public function update(Request $request, $id) {
        // Проверка входных данных от пользователя
        $request->validate([
            'name' => 'sometimes',
            'file' => 'required|mimes:png,jpg,pdf,html,3gp,7z,ae,ai,apk,asf,avi,bak,bmp,cdr,css,csv,divx,dll,doc,docx,dw,dwg,eps,exe,flv,fw,gif,gz,ico,iso,jar,js,mov,mp3,mp4,mpeg,php,png,ppt,ps,psd,rar,svg,swf,sys,tar,tiff,txt,wav,zip|max:8024',
        ]);
    
        $file = DB::table('files')->where('id', $id)->first();
    
        // Обновление имени и информации о файле
        $fileUpdate = ['name' => $request->input('name')];
        
        // Проверка, предоставлен ли новый файл
        if ($request->hasFile('file')) {
            // Обработка загрузки файла
            $uploadedFile = $request->file('file');
            $fileName = time() . '_' . $uploadedFile->getClientOriginalName();
            $fileExtension = $uploadedFile->getClientOriginalExtension();
            
            // Угадывание расширения, если оно не предоставлено в валидации
            if (!$request->has('file_extension')) {
                $fileUpdate['format'] = $fileExtension;
            }
    
            $filePath = $uploadedFile->storeAs('public/uploads', $fileName);
            $fileUrl = Storage::url($filePath);
    
            // Обновление информации о файле
            $fileUpdate['address'] = $fileUrl; // Замените 'address' на фактический столбец пути к файлу
        }
    
        // Обновление записи в базе данных
        DB::table('files')->where('id', $id)->update($fileUpdate);
    
        return response()->json(['message' => 'Файл успешно обновлен']);
    }
    
    // Метод для удаления файла по его ID
    public function delete($id) {
        // Поиск файла по его ID
        $file = File::find($id);
    
        // Проверка существования файла
        if (!$file) {
            return response()->json(['message' => 'Файл не найден.'], 404);
        }
    
        // Получение адреса файла из базы данных
        $filePath = 'public/uploads/' . $file->name; // Предполагается, что 'name' - это столбец, хранящий полное имя файла с меткой времени
    
        // Удаление файла из хранилища Laravel
        if (Storage::exists($filePath)) {
            Storage::delete($filePath);
        }
    
        // Удаление записи о файле из базы данных
        $file->delete();
    
        return response()->json(['message' => 'Файл успешно удален']);
    }

    // Метод для загрузки файла
    public function upload($id, Request $request) {
        // Проверка входных данных от пользователя
        $request->validate([
            'name' => 'nullable', // Измените 'sometimes|required' на 'nullable'
            'file' => 'required|mimes:png,jpg,pdf,html,3gp,7z,ae,ai,apk,asf,avi,bak,bmp,cdr,css,csv,divx,dll,doc,docx,dw,dwg,eps,exe,flv,fw,gif,gz,ico,iso,jar,js,mov,mp3,mp4,mpeg,php,png,ppt,ps,psd,rar,svg,swf,sys,tar,tiff,txt,wav,zip|max:8024',
        ]);
    
        $file = $request->file('file');
        $fileName = time() . '_' . $file->getClientOriginalName();
    
        // Сохранение файла в каталог 'app/public/uploads'
        $filePath = Storage::putFileAs('public/uploads', $file, $fileName);
    
        $sizeInMB = ceil($file->getSize() / (1024 * 1024));
    
        $fileUrl = Storage::url($filePath);
    
        // Если 'name' не предоставлено, используйте оригинальное имя файла
        $name = $request->filled('name') ? $request->input('name') : $file->getClientOriginalName();
    
        // Вставка информации о файле в базу данных
        DB::table('files')->insert([
            'name' => $name,
            'format' => $file->guessExtension(),
            'size' => $sizeInMB,
            'address' => $fileUrl, // Сохранение URL вместо пути
        ]);
        
        return response()->json(['message' => 'Файл успешно загружен']);
    }
}
