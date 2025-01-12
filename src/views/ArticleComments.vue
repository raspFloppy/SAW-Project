<script setup>
import { ref } from 'vue';
import { useArticleStore } from '@/stores/articles';
import { useAuthStore } from '@/stores/auth';
import { formatDate } from '@/utils/utils';

const articleStore = useArticleStore();
const authStore = useAuthStore();
const newComment = ref('');
const isLoggedIn = ref(authStore.isLoggedIn);

async function submitComment() {
    if (!newComment.value.trim()) return;

    const success = await articleStore.writeArticleComment(newComment.value);
    if (success) {
        newComment.value = '';
    }
};
</script>

<template>
    <div class="mt-8">
        <div v-if="isLoggedIn">
            <div class="card bg-base-200 shadow-xl mb-6">
                <div class="card-body">
                    <h2 class="card-title text-xl mb-4">Write a Comment</h2>
                    <textarea v-model="newComment" class="textarea textarea-bordered w-full h-24"
                        placeholder="Share your thoughts..."></textarea>
                    <div class="card-actions justify-end mt-4">
                        <button @click.prevent="submitComment" class="btn btn-primary" :disabled="!newComment.trim()">
                            Post Comment
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-4">
            <h2 class="text-2xl font-bold mb-6">Comments</h2>

            <div v-if="articleStore.loading" class="alert">
                <span class="loading loading-spinner"></span>
                Loading comments...
            </div>

            <div v-else-if="articleStore.error" class="alert alert-error">
                {{ articleStore.error }}
            </div>

            <div v-else-if="!articleStore.currentArticle || !articleStore.currentArticle.comments?.length"
                class="alert">
                No comments yet. Be the first to comment!
            </div>

            <div v-else class="space-y-4">
                <div v-for="comment in articleStore.currentArticle.comments" :key="comment.id"
                    class="card bg-base-100 shadow-lg">
                    <div class="card-body">
                        <div class="flex justify-between items-start mb-2">
                            <div class="flex items-center">
                                <div class="avatar placeholder mr-3">
                                    <div class="bg-neutral text-neutral-content rounded-full w-8">
                                        <span>{{ comment.firstname[0] }}{{ comment.lastname[0] }}</span>
                                    </div>
                                </div>
                                <div class="font-medium">
                                    {{ comment.firstname }} {{ comment.lastname }}
                                </div>
                            </div>
                            <div class="text-sm text-base-content/60">
                                {{ formatDate(comment.created_at) }}
                            </div>
                        </div>
                        <p class="whitespace-pre-wrap">{{ comment.comment }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>