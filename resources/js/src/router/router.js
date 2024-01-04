import { createRouter, createWebHistory } from 'vue-router';
import routes from '/Users/macbook/file-manager/resources/js/src/router/routes.js';

const router = createRouter({
	history: createWebHistory(),
	linkActiveClass: 'active',
	routes,
});

export default router;