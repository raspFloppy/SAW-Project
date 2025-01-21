<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useArticleStore } from '@/stores/articles';
import { useAuthStore } from '@/stores/auth';
import { formatDate } from '@/utils/utils';
import Navbar from '@/components/Navbar.vue';

const router = useRouter();
const articleStore = useArticleStore();
const auth = useAuthStore();
const favoritesArticles = ref([]);

onMounted(async () => {
    try {
        const favorites = await articleStore.getFavorites();
        favoritesArticles.value = favorites.map(article => ({
            id: article.id,
            title: article.title,
            author: article.author,
            created_at: article.created_at
        }));
    } catch (error) {
        console.error('Error fetching favorites:', error);
    }
});
</script>

<template>
    <Navbar>
        <template #left>
            <div class="flex-1">
                <RouterLink :class="{ 'underline': $route.path === '/dashboard' }" to="/dashboard"
                    class="btn btn-ghost normal-case text-xl">
                    User Dashboard
                </RouterLink>
                <RouterLink :class="{ 'underline': $route.path === '/favorite_articles' }" to="/favorite_articles"
                    class="btn btn-ghost normal-case text-xl">
                    Favorites Dashboard
                </RouterLink>
                <RouterLink v-if="auth.isAdmin" :class="{ 'underline': $route.path === '/admin_dashboard' }"
                    to='/admin_dashboard' class="btn btn-ghost normal-case text-xl">
                    Admin Dashboard
                </RouterLink>
            </div>
        </template>
    </Navbar>

    <div class="min-h-screen bg-base-200">
        <div class="container mx-auto p-8">
            <div v-if="articleStore.loading" class="flex justify-center">
                <span class="loading loading-spinner loading-lg"></span>
            </div>
            <div v-else-if="articleStore.error" class="alert alert-error">
                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ articleStore.error }}</span>
            </div>
            <div v-else>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div v-for="article in favoritesArticles" :key="article.id"
                        class="card bg-base-100 shadow-xl hover:shadow-2xl transition-shadow cursor-pointer"
                        @click="router.push(`/article/${article.id}`)">
                        <div class="card-body">
                            <h2 class="card-title">{{ article.title }}</h2>
                            <p class="text-base-content/70">By {{ article.author }}</p>
                            <div class="card-actions justify-between items-center mt-4">
                                <div class="badge badge-outline">{{ formatDate(article.created_at) }}</div>
                                <button class="btn btn-ghost btn-sm">Read more →</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>