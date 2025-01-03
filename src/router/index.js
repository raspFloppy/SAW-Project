import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore} from '@/stores/auth'
import Registration from '@/views/Registration.vue';
import Login from '@/views/Login.vue';
import Home from '@/views/Home.vue';
import NotFound from '@/views/NotFound.vue';
import Dashboard from '@/views/Dashboard.vue';
import '@fortawesome/fontawesome-free/css/all.css';
import CerHtml from '@/views/cer_html.vue';
import CerJava from '@/views/cer_java.vue';
import CerCSS from '@/views/cer_CSS.vue';
import CourseList from '@/views/CoursesList.vue';
import CourseDetails from '@/views/CourseDetails.vue';


const routes = [
  {path: '/', component: Home},
  {
    path: '/registration', 
    component: Registration, 
    meta: {requiresGuest: true}
  },
  {
    path: '/login', 
    component: Login, 
    meta: {requiresGuest: true}
  },
  {
    path: '/dashboard',
    component: Dashboard,
    meta: {requiresAuth: true}
  },
  {
    path: '/notfound', 
    component: NotFound
  },
  {
    path: '/courses',
    name: 'CourseList',
    component: CourseList,
  },
  {
    path: '/course/:id',
    component: CourseDetails,
  },
  {
    path: '/cer_html',
    name: 'CerHtml',
    component: CerHtml,
  },
  {
    path: '/cer_java',
    name: 'Cerjava',
    component: CerJava,
  },
  {
    path: '/cer_CSS',
    name: 'CerCSS',
    component: CerCSS,
  },
]

const router = createRouter({
  history: createWebHistory(),
  routes
})


router.beforeEach((to, from, next) => {
  const authStore = useAuthStore();

  if(to.meta.requiresGuest) {
    if(authStore.isLoggedIn) {
      next('/dashboard');
      return;
    }
  }

  if(to.meta.requiresAuth) {
    if(!authStore.isLoggedIn) {
      next('/login');
      return;
    }
  }

  next();
})

export default router