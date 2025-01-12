import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore} from '@/stores/auth'
import Registration from '@/views/Registration.vue';
import Login from '@/views/Login.vue';
import Home from '@/views/Home.vue';
import PageNotFound from '@/views/PageNotFound.vue';
import Dashboard from '@/views/Dashboard.vue';
import '@fortawesome/fontawesome-free/css/all.css';
import CerHtml from '@/views/cer_html.vue';
import CourseList from '@/views/CoursesList.vue';
import CourseDetails from '@/views/CourseDetails.vue';
import ArticlesList from '@/views/ArticlesList.vue';
import ArticleDetails from '@/views/ArticleDetails.vue';
import FavoriteArticles from '@/views/FavoriteArticles.vue';

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
    meta: {requiresAuth: true},
    children: [
      {
        path: 'favorites',
        component: FavoriteArticles
      }
    ]
  },
  {
    path: '/articles',
    component: ArticlesList,
  },
  {
    path: '/article/:id',
    component: ArticleDetails,
  },
  {
    path: '/:pathMatch(.*)*',
    name: 'PageNotFound', 
    component: PageNotFound
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
      console.error(error);
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