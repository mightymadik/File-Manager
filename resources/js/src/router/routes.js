import Home from '/Users/macbook/file-manager/resources/js/src/pages/Home.vue';
import FileEdit from '/Users/macbook/file-manager/resources/js/src/pages/Edit.vue';
import FileDelete from '/Users/macbook/file-manager/resources/js/src/pages/Edit.vue';
import FileCreate from '/Users/macbook/file-manager/resources/js/src/pages/About.vue';

const routes = [
  {
    path: '/',
    name: 'Home',
    component: Home,
    meta: { title: 'File Manage' }
  },
  {
    path: '/about',
    name: 'FileCreate',
    component: FileCreate,
  },
  {
    path: '/file/edit/:id',
    name: 'FileEdit',
    component: FileEdit,
    meta: { title: 'File Edit' }
  },
  {
    path: '/file/delete/:id',
    name: 'FileDelete',
    component: FileDelete,
    meta: { title: 'File Delete' }
  }
];

export default routes;
