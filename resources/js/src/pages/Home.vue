<template>
  <!-- Поисковая строка и таблица с файлами -->
  <div class="row height d-flex justify-content-center align-items-center mb-5">
    <div class="col-md-8">
      <div class="search">
        <!-- Поле для ввода поискового запроса -->
        <input type="text" class="form-control" placeholder="" v-model="searchQuery">
        <!-- Кнопка для запуска поиска -->
        <button class="btn btn-primary" @click="searchFiles">Поиск</button>
      </div>
    </div>
  </div>

  <!-- Таблица с информацией о файлах -->
  <table class="table">
    <thead>
      <tr>
        <th scope="col"></th>
        <th scope="col">Название файла</th>
        <th scope="col">Размер файла</th>
        <th scope="col">Расширение</th>
        <th scope="col"></th>
      </tr>
    </thead>
    <tbody>
      <!-- Цикл для отображения файлов в таблице -->
      <tr v-for="file in filteredFiles">
        <td>
          <!-- Вывод изображения или иконки в зависимости от формата файла -->
          <img v-if="isImage(file.format)" class="cropped-preview" :src="file.address" alt="Cropped Preview" />
          <img v-else class="icons" :src="`/images/Icons/${file.format}.png`" alt="File Icon" />
        </td>
        <td>{{ file.name }}</td>
        <td>{{ file.size }} MB</td>
        <td>.{{ file.format }}</td>
        <td>
          <!-- Кнопки для скачивания, редактирования и удаления файла -->
          <ul class="list-inline m-0">
            <li class="list-inline-item">
              <button @click="downloadFile(file.address)" class="btn download">
                <i class="fa fa-download"></i>
              </button>
            </li>
            <li class="list-inline-item">
              <router-link :to="{ name: 'FileEdit', params: { id: file.id } }">
                <button class="btn edit"><i class="fa fa-edit"></i></button>
              </router-link>
            </li>
            <li class="list-inline-item">
              <router-link :to="{ name: 'FileDelete', params: { id: file.id } }">
                <button class="btn delete"><i class="fa fa-trash"></i></button>
              </router-link>
            </li>
          </ul>
        </td>
      </tr>
    </tbody>
  </table>

  <!-- Отображение общего количества записей и количества записей на текущей странице -->
  <h1 class="total-record-count">Общее количество записей: {{ files.length }}</h1>
  <h1 class="current-page-record-count">В данной странице: {{ currentRecords.length }}</h1>

  <!-- Пагинация для навигации по страницам -->
  <nav aria-label="Page navigation example">
    <ul class="pagination justify-content-center">
      <!-- Кнопка для перехода на предыдущую страницу -->
      <li class="page-item" :class="{ disabled: currentPage === 1 }">
        <a class="page-link" href="#" aria-label="Previous" @click="prevPage">
          <span aria-hidden="true">&laquo;</span>
        </a>
      </li>
      <!-- Кнопки для отображения номеров страниц -->
      <li class="page-item" v-for="page in totalPages" :key="page" :class="{ active: page === currentPage }">
        <a class="page-link" href="#" @click="changePage(page)">{{ page }}</a>
      </li>
      <!-- Кнопка для перехода на следующую страницу -->
      <li class="page-item" :class="{ disabled: currentPage === totalPages }">
        <a class="page-link" href="#" aria-label="Next" @click="nextPage">
          <span aria-hidden="true">&raquo;</span>
        </a>
      </li>
    </ul>
  </nav>
</template>

<script lang="ts">
// Импорт необходимых модулей и библиотек
import { defineComponent, ref, onMounted, computed, watch } from 'vue';
import axios from 'axios';

// Интерфейс для описания структуры объекта файла
interface File {
  name: string;
  size: string;
  format: string;
  address: string;
  id: BigInt;
}

export default defineComponent({
  // Инициализация компонента
  setup() {
    // Инициализация реактивных переменных
    const files = ref<File[]>([]);
    const searchQuery = ref<string>('');
    const currentPage = ref<number>(1);
    const recordsPerPage = 50;

    // Функция для получения данных о файлах
    const getData = async () => {
      try {
        const { data } = await axios.get('api/home/get-data');
        files.value = data.files;
      } catch (error) {
        console.error(error);
      }
    };

    // Функция для скачивания файла
    const downloadFile = async (fileAddress: string) => {
      try {
        const response = await axios.get(fileAddress, { responseType: 'blob' });

        // Создание объекта Blob из данных ответа
        const blob = new Blob([response.data], { type: response.headers['content-type'] });

        // Создание ссылки для скачивания файла
        const link = document.createElement('a');
        link.href = window.URL.createObjectURL(blob);
        link.download = 'downloaded_file'; // Можно установить желаемое имя файла
        document.body.appendChild(link);

        // Инициирование скачивания
        link.click();

        // Удаление ссылки из DOM
        document.body.removeChild(link);
      } catch (error) {
        console.error(error);
      }
    };

    // Функция для проверки, является ли файл изображением
    const isImage = (format: string) => {
      return format === 'png' || format === 'jpg';
    };

    // Функция для поиска файлов в соответствии с поисковым запросом
    const searchFiles = () => {
      const filtered = files.value.filter(file =>
        file.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
        file.size.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
        file.format.toLowerCase().includes(searchQuery.value.toLowerCase())
      );

      // Обновление строки запроса
      const queryString = searchQuery.value.trim() !== '' ? `?search=${encodeURIComponent(searchQuery.value)}` : '';
      history.pushState({}, '', window.location.pathname + queryString);

      return filtered;
    };

    // Слежение за изменениями поискового запроса
    watch(searchQuery, () => {
      searchFiles();
      currentPage.value = 1; // Сброс текущей страницы при изменении запроса
    });

    // Вычисляемое свойство для отфильтрованных файлов на текущей странице
    const filteredFiles = computed(() => {
      const start = (currentPage.value - 1) * recordsPerPage;
      const end = start + recordsPerPage;
      return searchFiles().slice(start, end);
    });

    // Вычисляемое свойство для общего количества страниц
    const totalPages = computed(() => Math.ceil(searchFiles().length / recordsPerPage));

    // Вычисляемое свойство для текущих записей на странице
    const currentRecords = computed(() => {
      const start = (currentPage.value - 1) * recordsPerPage;
      const end = start + recordsPerPage;
      return searchFiles().slice(start, end);
    });

    // Функция для изменения текущей страницы
    const changePage = (page: number) => {
      if (page >= 1 && page <= totalPages.value) {
        currentPage.value = page;
      }
    };

    // Функция для перехода на предыдущую страницу
    const prevPage = () => {
      changePage(currentPage.value - 1);
    };

    // Функция для перехода на следующую страницу
    const nextPage = () => {
      changePage(currentPage.value + 1);
    };

    // Выполняется после монтирования компонента
    onMounted(() => {
      const params = new URLSearchParams(window.location.search);
      const initialSearchQuery = params.get('search');
      if (initialSearchQuery !== null) {
        searchQuery.value = decodeURIComponent(initialSearchQuery);
      }

      getData();
    });

    // Возврат объекта с переменными и методами
    return {
      files,
      searchQuery,
      currentPage,
      recordsPerPage,
      getData,
      downloadFile,
      searchFiles,
      filteredFiles,
      isImage,
      totalPages,
      currentRecords,
      changePage,
      prevPage,
      nextPage,
    };
  },
});
</script>

<style lang="less" scoped>
/* Стили компонента в Less с областью видимости */
body {
  background-color: #eee;
  font-family: "Poppins", sans-serif;
  font-weight: 300;
}

.search {
  position: relative;
  box-shadow: 0 0 40px rgba(51, 51, 51, .1);
}

.search input {
  height: 45px;
  text-indent: 25px;
  border: 2px solid #d6d4d4;
}

.search .fa-search {
  position: absolute;
  left: 16px;
}

.search button {
  position: absolute;
  top: 2px;
  right: 0px;
  height: 43px;
  width: 110px;
  background: #0d6efd;
}

.icons {
  height: 80px;
  width: 80px;
}

.cropped-preview {
  height: 100px;
  width: 100px;
}

/* Стили кнопок скачивания, редактирования и удаления файла */
.download {
  background-color: DodgerBlue;
  border: none;
  color: white;
  padding: 2px 10px;
  cursor: pointer;
  font-size: 20px;
}

.edit {
  background-color: #28a745;
  border: none;
  color: white;
  padding: 2px 10px;
  cursor: pointer;
  font-size: 20px;
}

.delete {
  background-color: #dc3545;
  border: none;
  color: white;
  padding: 2px 10px;
  cursor: pointer;
  font-size: 20px;
}

/* Изменение стилей кнопок при наведении */
.download:hover {
  background-color: RoyalBlue;
}

.edit:hover {
  background-color: green;
}

.delete:hover {
  background-color: darkred;
}

/* Стили для отображения общего количества и текущего количества записей */
.total-record-count {
  font-size: 12px;
}

.current-page-record-count {
  font-size: 12px;
}
</style>
