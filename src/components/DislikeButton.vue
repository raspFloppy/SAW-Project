<script setup>
import { ref, watch } from 'vue';
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
const isDisliked = ref(false);

watch(
    () => articleStore.currentArticle,
    (newArticle) => {
        if (newArticle) {
            isDisliked.value = newArticle.is_disliked;
        }
    },
    { immediate: true }
);

async function toggleDislike() {
    if (!authStore.isLoggedIn) {
        return;
    }

    try {
        await articleStore.toggleDislike();
        isDisliked.value = articleStore.currentArticle.is_disliked;
    } catch (error) {
        console.error('Failed to toggle dislike:', error);
    }
};
</script>

<template>
    <button :disabled="!authStore.isLoggedIn" @click="toggleDislike"
        class="btn btn-circle btn-ghost transition-colors duration-200 hover:bg-gray-100">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 transition-colors duration-200"
            :class="{ 'text-white fill-white': isDisliked, 'text-gray-400': !isDisliked }" viewBox="0 0 24 24"
            stroke="currentColor" :fill="isDisliked ? 'currentColor' : 'none'">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M19.682 17.682a4.5 4.5 0 01-6.364 0L12 16.364l-1.318 1.318a4.5 4.5 0 01-6.364-6.364L12 3.636l7.682 7.682a4.5 4.5 0 010 6.364z" />
        </svg>
    </button>
</template>