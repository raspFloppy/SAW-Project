import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore} from '@/stores/auth'
import Registration from '@/views/Registration.vue';
import Login from '@/views/Login.vue';
import Home from '@/views/Home.vue';
import NotFound from '@/views/NotFound.vue';
import Dashboard from '@/views/Dashboard.vue';

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
]

const router = createRouter({
  history: createWebHistory(),
  routes
})


router.beforeEach((to, from, next) => {
  const authStore = useAuthStore();

  if(to.meta.requiresGuest) {
    try {
      authStore.validateSession();
      
      if(authStore.isLoggedIn) {
        next('/dashboard');
        return;
      }

    } catch (error) {
      console.log(error);
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