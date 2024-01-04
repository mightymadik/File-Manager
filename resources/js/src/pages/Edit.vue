<template>
    <!-- Компонент для редактирования и удаления записи -->
    <div>
      <!-- Заголовок формы редактирования -->
      <h1 class="justify-content-center align-items-center mb-5">Редактирование и Удаление записи</h1>
      <!-- Форма событиями при отправке, выборе файла и удалении -->
      <form @submit.prevent="editFile">
        <!-- Поле ввода названия файла -->
        <div class="mb-3">
          <label for="exampleInputEmail1" class="form-label">Название файла</label>
          <input v-model="formData.name" type="text" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp">
        </div>
        <!-- Поле выбора файла -->
        <div class="mb-3">
          <label for="formFile" class="form-label">Загрузить Файл</label>
          <input ref="fileInput" type="file" class="form-control" @change="handleFileChange">
        </div>
        <!-- Кнопки для сохранения и удаления записи -->
        <button type="submit" class="btn btn-primary mr-2 submit">Сохранить</button>
        <button type="button" class="btn btn-danger ml-2" @click="deleteFile">Удалить</button>
      </form>
    </div>
  </template>
  
  <script>
  // Импорт необходимых библиотек
  import axios from 'axios';
  import Swal from 'sweetalert2';
  
  export default {
    // Инициализация данных формы
    data() {
      return {
        formData: {
          name: '', // Название файла
          file: null, // Файл
        },
        fileLink: null, // Ссылка на файл (добавлено для хранения ссылки на файл)
      };
    },
    // Хук жизненного цикла - вызывается при создании компонента
    created() {
      this.getFileName(); // Получение данных о файле при создании компонента
    },
    // Методы для обработки событий
    methods: {
      // Получение данных о файле для предварительного заполнения формы
      async getFileName() {
        try {
          const response = await axios.get('/api/file/edit/' + this.$route.params.id);
          this.formData.name = response.data.name; // Установка названия файла
          this.fileLink = response.data.address; // Установка ссылки на файл (предполагается, что ссылка возвращается в ответе)
          console.log(response.data);
        } catch (error) {
          console.error(error);
        }
      },
      // Обработчик изменения выбранного файла
      handleFileChange(event) {
        this.formData.file = event.target.files[0];
      },
      // Метод редактирования файла
      async editFile() {
        try {
          const result = await Swal.fire({
            title: 'Обновить файл',
            text: 'Вы хотите обновить файл?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Да',
            cancelButtonText: 'Нет',
            reverseButtons: true,
          });
  
          if (result.isConfirmed) {
            // Если пользователь нажал "Да, обновить"
            const formData = new FormData();
            formData.append('name', this.formData.name);
            formData.append('file', this.formData.file);
            formData.append('address', this.fileLink); // Добавление ссылки на файл
  
            const response = await axios.post('/api/file/edit/' + this.$route.params.id, formData, {
              headers: {
                'Content-Type': 'multipart/form-data',
              },
            });
  
            this.fileLink = response.data.address; // Обновление ссылки на файл
  
            console.log(response.data);
            Swal.fire('Успешно', 'Файл обновился успешно', 'success');
            this.$router.push({ name: 'Home' }); // Перенаправление на главную страницу
          } else if (result.dismiss === Swal.DismissReason.cancel) {
            // Если пользователь нажал "Нет, отменить"
            Swal.fire('Отменено', 'Обновление файла отменено', 'info');
          }
        } catch (error) {
          console.error(error);
          Swal.fire('Ошибка', 'Не получилось обновить файл (Нужно выбрать новый файл или формат файла не поддерживается)', 'error');
        }
      },
      // Метод удаления файла
      async deleteFile() {
        try {
          const result = await Swal.fire({
            title: 'Удалить файл',
            text: 'Вы хотите удалить файл?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Да',
            cancelButtonText: 'Нет',
            reverseButtons: true,
          });
  
          if (result.isConfirmed) {
            // Если пользователь нажал "Да, удалить"
            const response = await axios.delete('/api/file/delete/' + this.$route.params.id);
            console.log(response.data);
            Swal.fire('Успешно', 'Файл удалился успешно', 'success');
            this.$router.push({ name: 'Home' }); // Перенаправление на главную страницу
          } else if (result.dismiss === Swal.DismissReason.cancel) {
            // Если пользователь нажал "Нет, отменить"
            Swal.fire('Отменено', 'Удаление файла отменено', 'info');
          }
        } catch (error) {
          console.error(error);
          Swal.fire('Ошибка', 'Произошла ошибка!', 'error');
        }
      },
    },
  };
  </script>
  
  <style>
  .submit {
    margin-right: 8px;
  }
  </style>
  