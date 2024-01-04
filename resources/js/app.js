import { createApp } from 'vue';
import axios from 'axios';
import App from '/Users/macbook/file-manager/resources/js/src/App.vue';
import Router from '/Users/macbook/file-manager/resources/js/src/router/router.js';

// Set the default Accept header for axios
axios.defaults.headers.common['Accept'] = 'application/json';

createApp(App).use(Router).mount('#app');
