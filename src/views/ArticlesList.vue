<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { useRouter } from 'vue-router';
import { useArticleStore } from '@/stores/articles';
import { useAuthStore } from '@/stores/auth';
import { formatDate } from '@/utils/utils';
import Navbar from '@/components/Navbar.vue';

const router = useRouter();
const articleStore = useArticleStore();
const authStore = useAuthStore();
const isAdmin = computed(() => authStore.isAdmin);
const searchTerm = ref('');
const showSearch = ref(false);

const filteredArticles = computed(() => {
  if (!searchTerm.value.trim()) {
    return articleStore.articles;
  }
  return articleStore.allArticles.filter(article =>
    article.title.toLowerCase().includes(searchTerm.value.toLowerCase())
  );
});

const showPagination = computed(() => !searchTerm.value.trim());

watch(
  () => authStore.$state,
  (newState) => {
    isAdmin.value;
    if (!searchTerm.value) {
      articleStore.fetchArticles(articleStore.currentPage);
    }
  },
  { immediate: true }
);

watch(searchTerm, async (newValue) => {
  if (newValue.trim()) {
    if (!articleStore.allArticles.length) {
      await articleStore.fetchAllArticles();
    }
  }
});

async function changePage(page) {
  if (!searchTerm.value) {
    await articleStore.fetchArticles(page);
  }
}

onMounted(async () => {
  await articleStore.fetchArticles(1);
});

const toggleSearch = () => {
  showSearch.value = !showSearch.value;
  if (!showSearch.value) {
    searchTerm.value = '';
  }
};
</script>

<template>
  <div class="min-h-screen bg-base-200">
    <Navbar>
      <template #left>
        <div class="flex-1 flex items-center gap-4">
          <a class="btn btn-ghost normal-case text-xl">Articles</a>
          <button @click="toggleSearch" class="btn btn-ghost btn-circle">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
              stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
          </button>
        </div>
      </template>
    </Navbar>

    <div class="container mx-auto p-8">
      <div v-if="showSearch" class="mb-6">
        <div class="relative">
          <input type="text" v-model="searchTerm" placeholder="Search articles by title..."
            class="input input-bordered w-full pr-10" />
          <button v-if="searchTerm" @click="searchTerm = ''"
            class="absolute right-3 top-1/2 -translate-y-1/2 btn btn-ghost btn-circle btn-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
              stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
      </div>

      <div v-if="articleStore.loading" class="flex justify-center">
        <span class="loading loading-spinner loading-lg"></span>
      </div>

      <div v-else-if="articleStore.error" class="alert alert-error">
        <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <span>{{ articleStore.error }}</span>
      </div>

      <div v-else>
        <div v-if="filteredArticles.length" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div v-for="article in filteredArticles" :key="article.id"
            class="card bg-base-100 shadow-xl hover:shadow-2xl transition-shadow cursor-pointer"
            @click="router.push(`/article/${article.id}`)">
            <div class="card-body">
              <h2 class="card-title">{{ article.title }}</h2>
              <p class="text-base-content/70">By {{ article.author }}</p>
              <div class="card-actions justify-between items-center mt-4">
                <div class="badge badge-outline">{{ formatDate(article.created_at) }}</div>
                <div class="flex items-center gap-4">
                  <span>{{ article.favorite_count }} ❤️</span>
                  <span>{{ article.comments_count }} 💬</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div v-else-if="searchTerm" class="text-center py-8">
          <h3 class="text-lg font-semibold">No articles found</h3>
          <p class="text-base-content/70">Try adjusting your search terms</p>
        </div>

        <div v-if="showPagination && filteredArticles.length" class="flex flex-col items-center mt-8">
          <div class="join grid grid-cols-2 w-full max-w-md">
            <button class="join-item btn btn-outline" :disabled="articleStore.currentPage === 1"
              @click="changePage(articleStore.currentPage - 1)">
              Previous page
            </button>
            <button class="join-item btn btn-outline" :disabled="articleStore.currentPage === articleStore.totalPages"
              @click="changePage(articleStore.currentPage + 1)">
              Next page
            </button>
          </div>
          <div class="text-center mt-4">
            Page {{ articleStore.currentPage }} of {{ articleStore.totalPages }}
          </div>
        </div>
      </div>
    </div>
  </div>
</template>