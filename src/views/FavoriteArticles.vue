<script setup>
import { onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useArticleStore } from '@/stores/articles';
import { formatDate } from '@/utils/utils';

const router = useRouter();
const articleStore = useArticleStore();

onMounted(() => {
    articleStore.getFavorites();
});

</script>

<template>
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
                    <div v-for="article in articleStore.articles" :key="article.id"
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