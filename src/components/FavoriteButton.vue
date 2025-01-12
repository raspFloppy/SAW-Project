<script setup>
import { ref, onMounted, watch } from 'vue';
import { useArticleStore } from '@/stores/articles';
import { useAuthStore } from '@/stores/auth';

const props = defineProps({
    articleId: {
        type: Number,
        required: true
    }
});

const articleStore = useArticleStore();
const authStore = useAuthStore();
const isFavorite = ref(false);

watch(
    () => articleStore.currentArticle,
    (newArticle) => {
        if (newArticle) {
            isFavorite.value = newArticle.is_favorite;
        }
    },
    { immediate: true }
);

async function toggleFavorite() {
    if (!authStore.isLoggedIn) {
        return;
    }

    try {
        await articleStore.toggleFavorite();
        isFavorite.value = articleStore.currentArticle.is_favorite;
    } catch (error) {
        console.error('Failed to toggle favorite:', error);
    }
};
</script>

<template>
    <button v-if="authStore.isLoggedIn" @click="toggleFavorite"
        class="btn btn-circle btn-ghost transition-colors duration-200 hover:bg-gray-100">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 transition-colors duration-200"
            :class="{ 'text-red-500 fill-red-500': isFavorite, 'text-gray-400': !isFavorite }" viewBox="0 0 24 24"
            stroke="currentColor" :fill="isFavorite ? 'currentColor' : 'none'">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
        </svg>
    </button>
</template>