import { createRouter, createWebHistory } from 'vue-router'
import Registration from '@/views/Registration.vue';
import Login from '@/views/Login.vue';
import Home from '@/views/Home.vue';
import NotFound from '@/views/NotFound.vue';

const routes = [
  {path: '/', component: Home},
  {path: '/registration', component: Registration},
  {path: '/login', component: Login},
  {path: '/notfound', component: NotFound},
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

export default router