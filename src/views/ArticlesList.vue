<script setup>
import { onMounted, computed } from 'vue';
import { useRouter } from 'vue-router';
import { useArticleStore } from '@/stores/articles';
import { formatDate } from '@/utils/utils';
import Navbar from '@/components/Navbar.vue';

const router = useRouter();
const articleStore = useArticleStore();

// Funzioni per ricerca e paginazione
const searchArticles = () => {
  articleStore.searchArticles();
};

const clearSearch = () => {
  articleStore.clearSearch();
};

async function changePage(page) {
  articleStore.changePage(page);
}

// Carica gli articoli al montaggio del componente
onMounted(async () => {
  await articleStore.fetchArticles();
});
</script>

<template>
  <div class="min-h-screen bg-base-200">
    <Navbar>
      <template #left>
        <div class="flex-1">
          <a class="btn btn-ghost normal-case text-xl">Articles</a>
        </div>
      </template>
    </Navbar>
    <div class="container mx-auto p-8">
      <!-- Barra di Ricerca -->
      <div class="flex justify-center mb-6">
        <div class="relative w-full max-w-md">
          <input
            v-model="articleStore.searchQuery"
            type="text"
            class="input input-bordered w-full pr-10"
            placeholder="Search articles..."
            @input="searchArticles"
          />
          <button
            v-if="articleStore.searchQuery"
            @click="clearSearch"
            class="absolute right-3 top-1/2 -translate-y-1/2 btn btn-ghost btn-sm"
          >
            ×
          </button>
        </div>
      </div>

      <!-- Stato di Caricamento -->
      <div v-if="articleStore.loading" class="flex justify-center">
        <span class="loading loading-spinner loading-lg"></span>
      </div>

      <!-- Stato di Errore -->
      <div v-else-if="articleStore.error" class="alert alert-error">
        <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <span>{{ articleStore.error }}</span>
      </div>

      <!-- Nessun Risultato -->
      <div v-else-if="articleStore.hasNoResults" class="text-center my-4">
        No articles found for your search.
      </div>

      <!-- Lista degli Articoli -->
      <div v-else>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div
            v-for="article in articleStore.currentPageArticles"
            :key="article.id"
            class="card bg-base-100 shadow-xl hover:shadow-2xl transition-shadow cursor-pointer"
            @click="router.push(`/article/${article.id}`)"
          >
            <div class="card-body">
              <h2 class="card-title">{{ article.title }}</h2>
              <p class="text-base-content/70">By {{ article.author }}</p>
              <div class="card-actions justify-between items-center mt-4">
                <div class="badge badge-outline">{{ formatDate(article.created_at) }}</div>
                <div>
                  {{ article.favorite_count }}
                </div>
                <button class="btn btn-ghost btn-sm">Read more →</button>
              </div>
            </div>
          </div>
        </div>

        <!-- Paginazione -->
        <div class="flex flex-col items-center mt-8">
          <div class="join grid grid-cols-2 w-full max-w-md">
            <button
              class="join-item btn btn-outline"
              :disabled="articleStore.currentPage === 1"
              @click="changePage(articleStore.currentPage - 1)"
            >
              Previous page
            </button>
            <button
              class="join-item btn btn-outline"
              :disabled="articleStore.currentPage === articleStore.totalPages"
              @click="changePage(articleStore.currentPage + 1)"
            >
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
