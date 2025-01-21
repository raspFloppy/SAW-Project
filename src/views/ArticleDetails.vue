<script setup>
import { onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useArticleStore } from '@/stores/articles';
import { formatDate } from '@/utils/utils';
import FavoriteButton from '@/components/FavoriteButton.vue';
import DislikeButton from '@/components/DislikeButton.vue';
import ArticleComments from './ArticleComments.vue';

const route = useRoute();
const router = useRouter();
const articleStore = useArticleStore();

function scrollToCommentSection() {
    const commentSection = document.getElementById('comment_section');
    if (commentSection) {
        commentSection.scrollIntoView({ behavior: 'smooth' });
    }
}

onMounted(async () => {
    await articleStore.fetchArticleById(route.params.id);
    await articleStore.updateArticleComments();
    await articleStore.updateArticleLikesCount();
    await articleStore.updateArticleDislikesCount();
    await articleStore.updateArticleCommentsCount();
});

</script>

<template>
    <div class="container mx-auto p-4">
        <button @click="router.push('/articles')"
            class="mb-4 px-4 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-900 transition-colors">
            ← Back to Articles
        </button>

        <div v-if="articleStore.loading">
            <span class="loading loading-spinner loading-xs"></span>
        </div>
        <div v-else-if="articleStore.error">{{ articleStore.error }}</div>
        <div v-else-if="articleStore.currentArticle" class="max-w-3xl mx-auto">
            <h1 class="text-3xl font-bold mb-4">{{ articleStore.currentArticle.title }}</h1>
            <div class="text-gray-600 mb-2">By {{ articleStore.currentArticle.author }}</div>
            <div class="text-gray-500 mb-6 flex items-center justify-between">
                <div>
                    {{ formatDate(articleStore.currentArticle.created_at) }}
                </div>

                <div class="flex items-center gap-1">
                    <div class="flex items-center gap-0">
                        {{ articleStore.articleLikesCount }}
                        <FavoriteButton :articleId="articleStore.currentArticle.id" />
                    </div>
                    <div class="flex items-center gap-0">
                        {{ articleStore.articleDislikesCount }}
                        <DislikeButton :articleId="articleStore.currentArticle.id" />
                    </div>

                    <div class="flex items-center gap-1">
                        {{ articleStore.articleCommentsCount }}
                        <button @click="scrollToCommentSection">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" viewBox="0 0 24 24"
                                fill="currentColor">
                                <path
                                    d="M12 3C6.486 3 2 6.582 2 11c0 2.221 1.057 4.216 2.818 5.651L4 21l5.154-1.864C10.006 20.688 11 21 12 21c5.514 0 10-3.582 10-8s-4.486-8-10-8zm0 14c-1.119 0-2.177-.314-3.09-.854l-.598-.35-3.447 1.248.93-3.435-.345-.598C4.506 12.205 4 11.129 4 11c0-3.309 3.589-6 8-6s8 2.691 8 6-3.589 6-8 6z" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
            <div class="prose max-w-none">
                {{ articleStore.currentArticle.content }}
            </div>
            <div class="mt-36" id="comment_section">
                <ArticleComments :articleId="articleStore.currentArticle.id" />
            </div>
        </div>

        <div v-else>
            <router-view name="not-found" />
        </div>
    </div>
</template>