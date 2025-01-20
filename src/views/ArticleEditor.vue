<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/auth';
import { useArticleStore } from '@/stores/articles'

const router = useRouter();
const authStore = useAuthStore();
const articleStore = useArticleStore();

const title = ref('');
const content = ref('');
const error = ref('');
const loading = ref(false);

const authorName = computed(() => {
    if (!authStore.user) return '';
    return `${authStore.user.firstname} ${authStore.user.lastname}`.trim();
});

onMounted(() => {
    if (!authStore.isLoggedIn || !authStore.isAdmin) {
        router.push('/login');
        return;
    }
});

async function handleSubmit() {
    if (!title.value || !content.value) {
        error.value = 'Please fill in all fields';
        return;
    }

    error.value = '';
    loading.value = true;

    try {
        const articleId = await articleStore.writeArticle(title.value, content.value, `${authStore.user.firstname} ${authStore.user.lastname}`.trim());

        if (articleId) {
            router.push(`/article/${articleId}`);
        }
    } catch (e) {
        error.value = e.message || 'Failed to publish article';
    } finally {
        loading.value = false;
    }
}
</script>

<template>
    <div class="max-w-4xl mx-auto p-6">
        <form @submit.prevent="handleSubmit" class="space-y-6">
            <div class="space-y-2">
                <label for="title" class="block text-sm font-medium">Title</label>
                <input id="title" v-model="title" type="text" required class="block w-full rounded-md bg-gray-800"
                    :disabled="loading" />
            </div>

            <div class="space-y-2">
                <label for="content" class="block text-sm font-medium">Content</label>
                <textarea id="content" v-model="content" rows="10" required
                    class="block w-full rounded-md border shadow-sm bg-gray-800" :disabled="loading"></textarea>
            </div>

            <div class="space-y-2">
                <label class="block text-sm font-medium">Author</label>
                <p>{{ authorName }}</p>
            </div>

            <div class="flex items-center justify-between">
                <button type="submit"
                    class="rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600"
                    :disabled="loading">
                    {{ loading ? 'Publishing...' : 'Publish Article' }}
                </button>

                <router-link to="/articles" class="text-sm font-semibold text-red-600 hover:text-red-500">
                    Cancel
                </router-link>
            </div>

            <div v-if="error" class="mt-4 text-red-600 text-sm">
                {{ error }}
            </div>
        </form>
    </div>
</template>