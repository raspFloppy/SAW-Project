import { defineStore } from 'pinia';
import axios from 'axios';

export const useArticleStore = defineStore('article', {
  state: () => ({
    articles: [],
    currentArticle: null,
    loading: false,
    error: null,
    currentPage: 1,
    perPage: 3,
    totalPages: 1,
    total: 0,
  }),

  actions: {
    async fetchArticles(page = 1) {
      this.loading = true;
      try {
        const response = await axios.get('http://localhost:8000/index.php', {
          params: {
            action: 'get_articles',
            page,
            per_page: this.perPage
          },
          withCredentials: true
        });

        if (response.data.success) {
          this.articles = response.data.articles;
          this.currentPage = response.data.current_page;
          this.totalPages = response.data.last_page;
          this.total = response.data.total;
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
        const user = JSON.parse(localStorage.getItem('user'));
        const userId = user ? user.id : null;

        const response = await axios.get('http://localhost:8000/index.php', {
          withCredentials: true,
          params: {
            action: 'get_article',
            id: article_id,
            user_id: userId
          }
        });
        
        if (response.data.success) {
          this.currentArticle = {
            ...response.data.article,
            is_favorite: response.data.article.is_favorite || false
          };
        }
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to fetch article';
      } finally {
        this.loading = false;
      }
    },

    async toggleFavorite() {
      const user = JSON.parse(localStorage.getItem('user'));
      const userId = user ? user.id : null;

      this.loading = true;
      try {
        const response = await axios.post('http://localhost:8000/index.php?action=set_favorite', 
          { article_id: this.currentArticle.id, user_id: userId},
          { 
            withCredentials: true,
            headers: {
              'Content-Type': 'application/json'
            }
          }
        );

        if (response.data.success) {
          this.currentArticle = {
            ...this.currentArticle,
            is_favorite: !this.currentArticle.is_favorite
          };
          return true;
        }
        return false;
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to toggle favorite';
        return false;
      } finally {
        this.loading = false;
      }
    },

    async getFavorites() {
      try {
        const user = JSON.parse(localStorage.getItem('user'));
        const userId = user ? user.id : null;

        const response = await axios.get('http://localhost:8000/index.php', {
          withCredentials: true,
          params: {
            action: 'get_favorites',
            user_id: userId
          }
        });

        if (response.data.success) {
          return response.data.favorites;
        }
        return null;
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to fetch favorites';
        return null;
      } finally {
        this.loading = false
      }
    },

    async getFavoritesCount() {
      try {
        const user = JSON.parse(localStorage.getItem('user'));
        const userId = user ? user.id : null;

        const response = await axios.get('http://localhost:8000/index.php', {
          withCredentials: true,
          params: {
            action: 'get_favorites_count',
            user_id: userId
          }
        });

        if (response.data.success) {
          return response.data.count;
        }
        return 0;
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to fetch favorites count';
        return 0;
      } finally {
        this.loading = false;
      }
    },

    async getArticleComments() {
      try {
        const response = await axios.get('http://localhost:8000/index.php', {
          withCredentials: true,
          params: {
            action: 'get_comments',
            article_id: this.currentArticle.id
          }
        });

        if (response.data.success) {
          this.currentArticle.comments = response.data.comments;
        }
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to fetch comments';
      } finally {
        this.loading = false;
      }
    },

    async writeArticleComment(comment) {
      const user = JSON.parse(localStorage.getItem('user'));
      const userId = user ? user.id : null;

      this.loading = true;
      try {
        const response = await axios.post('http://localhost:8000/index.php?action=write_comment', 
          { article_id: this.currentArticle.id, user_id: userId, comment },
          { 
            withCredentials: true,
            headers: {
              'Content-Type': 'application/json'
            }
          }
        );

        console.log(response)
        if (response.data.success) {
          this.currentArticle.comments = [
            ...this.currentArticle.comments,
            response.data.comment
          ];
          return true;
        }
        return false;
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to write comment';
        return false;
      } finally {
        this.loading = false;
      }
    }
  }
});