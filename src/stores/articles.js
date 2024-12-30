import { defineStore } from 'pinia';
import axios from 'axios';

export const useArticleStore = defineStore('article', {
  state: () => ({
    articles: [],
    currentArticle: null,
    loading: false,
    error: null
  }),

  actions: {
    async fetchArticles() {
      this.loading = true;
      try {
        const response = await axios.get('http://localhost:8000/index.php?action=get_articles', {
          withCredentials: true
        });

        if (response.data.success) {
          this.articles = response.data.articles;
        }
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to fetch articles';
      } finally {
        this.loading = false;
      }
    },

    async fetchArticleById(article_id) {
      this.loading = true;
      try {
        const response = await axios.get('http://localhost:8000/index.php', {
          withCredentials: true,
          params: {
            action: 'get_article',
            id: article_id
          }
        });
        if (response.data.success) {
          this.currentArticle = response.data.article;
        }
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to fetch article';
      } finally {
        this.loading = false;
      }
    }
  }
});