<template>
  <!-- Заголовок формы для добавления записи -->
  <h1 class="justify-content-center align-items-center mb-5">Добавление записи</h1>
  <!-- Форма событий при отправке и перетаскивании файла -->
  <form @submit.prevent="uploadFile" @drop.prevent="handleDrop">
    <!-- Поле для ввода названия файла -->
    <div class="mb-3">
      <label for="exampleInputEmail1" class="form-label">Название файла</label>
      <input v-model="fileName" type="text" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp">
    </div>
    <!-- Поле для загрузки файла с обработчиками перетаскивания -->
    <div class="mb-3" @drop.prevent="handleDrop" @dragover.prevent="handleDragOver">
      <label for="formFile" class="form-label">Загрузить Файл</label>
      <div class="drop-area" :class="{ 'dragging': isDragging }">
        <input ref="fileInput" type="file" name="file" class="form-control" @change="handleFileChange">
        <p v-if="isDragging">Перетащите файл сюда</p>
        <p v-else>Перетащите файл или выберите его кликом</p>
      </div>
      <!-- Отображение прогресса загрузки -->
      <div v-if="selectedFile && loadingPercentage !== null">
        <div class="progress">
          <div class="progress-bar" :style="{ width: loadingPercentage + '%' }"></div>
        </div>
        <p>{{ loadingPercentage }}% загружено</p>
      </div>
    </div>
    <!-- Кнопка отправки формы с блокировкой при загрузке -->
    <button type="submit" class="btn btn-primary" :disabled="loadingPercentage !== null">Отправить</button>
  </form>
</template>

<script>
// Импорт необходимых библиотек
import axios from 'axios';
import Swal from 'sweetalert2';

export default {
  // Инициализация данных формы
  data() {
    return {
      fileName: '',
      selectedFile: null,
      isDragging: false,
      loadingPercentage: null,
    };
  },
  // Методы для обработки событий формы
  methods: {
    // Обработчик изменения выбранного файла
    handleFileChange(event) {
      this.selectedFile = event.target.files[0];
    },
    // Асинхронная функция загрузки файла
    async uploadFile() {
      const formData = new FormData();
      formData.append('name', this.fileName);
      formData.append('file', this.selectedFile);

      try {
        const { data } = await axios.post('api/file/upload/' + this.$route.params.id, formData, {
          onUploadProgress: progressEvent => {
            this.loadingPercentage = Math.round((progressEvent.loaded / progressEvent.total) * 100);
          },
        });
        this.getData();
      } catch (error) {
        console.log(error);
        Swal.fire('Ошибка', 'Не удалось загрузить файл', 'error');
      } finally {
        // Сброс процента загрузки после завершения загрузки (успешной или с ошибкой)
        this.loadingPercentage = null;
      }
    },
    // Обработчик события броска файла в область формы
    handleDrop(event) {
      event.preventDefault();
      this.isDragging = false;

      if (event.dataTransfer.files.length > 0) {
        // Задержка перед установкой выбранного файла (имитация задержки)
        setTimeout(() => {
          this.selectedFile = event.dataTransfer.files[0];
        }, 8000);
      }
    },
    // Обработчик события наведения на область формы
    handleDragOver(event) {
      event.preventDefault();
      this.isDragging = true;
    },
    // Метод для обработки успешной загрузки файла
    getData() {
      Swal.fire('Успех', 'Файл успешно загружен', 'success');
    },
  },
};
</script>

<style scoped>
  .drop-area {
    border: 2px dashed #aaa;
    padding: 20px;
    text-align: center;
    cursor: pointer;
  }
  .dragging {
    border-color: #0d6efd;
  }
  .progress {
    height: 20px;
    margin-top: 10px;
    background-color: #f8f9fa;
    border-radius: 4px;
    overflow: hidden;
  }
  .progress-bar {
    height: 100%;
    width: 0;
    background-color: #0d6efd;
    transition: width 0.3s ease;
  }
</style>
