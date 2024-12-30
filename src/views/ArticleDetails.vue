<script setup>
import { onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useArticleStore } from '@/stores/articles';

const route = useRoute();
const router = useRouter();
const articleStore = useArticleStore();

onMounted(() => {
    articleStore.fetchArticleById(route.params.id);
});
</script>

<template>
    <div class="container mx-auto p-4">
        <button @click="router.push('/articles')"
            class="mb-4 px-4 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-900 transition-colors">
            ← Back to Articles
        </button>

        <div v-if="articleStore.loading">Loading article...</div>
        <div v-else-if="articleStore.error">{{ articleStore.error }}</div>
        <div v-else-if="articleStore.currentArticle" class="max-w-3xl mx-auto">
            <h1 class="text-3xl font-bold mb-4">{{ articleStore.currentArticle.title }}</h1>
            <div class="text-gray-600 mb-2">By {{ articleStore.currentArticle.author }}</div>
            <div class="text-gray-500 mb-6">
                {{ new Date(articleStore.currentArticle.created_at).toLocaleDateString() }}
            </div>
            <div class="prose max-w-none">
                {{ articleStore.currentArticle.content }}
            </div>
        </div>
        <div v-else>
            <router-view name="not-found" />
        </div>
    </div>
</template>