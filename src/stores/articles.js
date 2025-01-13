import { defineStore } from 'pinia';
import axios from 'axios';

const API_BASE = 'https://saw.dibris.unige.it/~s5145768/backend/index.php';

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
        const response = await axios.get(API_BASE, {
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
        } else {
          throw new Error(response.data.message || 'Failed to fetch articles');
        }
      } catch (error) {
        this.error = error.message || 'Failed to fetch articles';
        throw error;
      } finally {
        this.loading = false;
      }
    },

    async fetchArticleById(article_id) {
      this.loading = true;
      try {
        const user = JSON.parse(localStorage.getItem('user'));
        const userId = user ? user.id : null;

        const response = await axios.get(API_BASE, {
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
        } else {
          throw new Error(response.data.message || 'Failed to fetch article');
        }
      } catch (error) {
        this.error = error.message || 'Failed to fetch article';
        throw error;
      } finally {
        this.loading = false;
      }
    },

    async toggleFavorite() {
      const user = JSON.parse(localStorage.getItem('user'));
      const userId = user ? user.id : null;

      if (!userId || !this.currentArticle) {
        throw new Error('User must be logged in and article must be selected');
      }

      this.loading = true;
      try {
        const response = await axios.post(API_BASE, 
          { 
            action: 'set_favorite',
            article_id: this.currentArticle.id, 
            user_id: userId
          },
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
        throw new Error(response.data.message || 'Failed to toggle favorite');
      } catch (error) {
        this.error = error.message || 'Failed to toggle favorite';
        throw error;
      } finally {
        this.loading = false;
      }
    },

    async getFavorites() {
      const user = JSON.parse(localStorage.getItem('user'));
      const userId = user ? user.id : null;

      if (!userId) {
        throw new Error('User must be logged in');
      }

      try {
        const response = await axios.get(API_BASE, {
          withCredentials: true,
          params: {
            action: 'get_favorites',
            user_id: userId
          }
        });

        if (response.data.success) {
          return response.data.favorites;
        }
        throw new Error(response.data.message || 'Failed to fetch favorites');
      } catch (error) {
        this.error = error.message || 'Failed to fetch favorites';
        throw error;
      }
    },

    async getFavoritesCount() {
      const user = JSON.parse(localStorage.getItem('user'));
      const userId = user ? user.id : null;

      if (!userId) return 0;

      try {
        const response = await axios.get(API_BASE, {
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
        this.error = error.message || 'Failed to fetch favorites count';
        return 0;
      }
    },

    async updateArticleComments() {
      if (!this.currentArticle) {
        throw new Error('No article selected');
      }

      try {
        const response = await axios.get(API_BASE, {
          withCredentials: true,
          params: {
            action: 'get_comments',
            article_id: this.currentArticle.id
          }
        });

        if (response.data.success) {
          this.currentArticle = {
            ...this.currentArticle,
            comments: response.data.comments
          };
        } else {
          throw new Error(response.data.message || 'Failed to fetch comments');
        }
      } catch (error) {
        this.error = error.message || 'Failed to fetch comments';
        throw error;
      }
    },

    async writeArticleComment(comment) {
      const user = JSON.parse(localStorage.getItem('user'));
      const userId = user ? user.id : null;

      if (!userId || !this.currentArticle) {
        throw new Error('User must be logged in and article must be selected');
      }

      this.loading = true;
      try {
        const response = await axios.post(API_BASE, 
          { 
            action: 'write_comment',
            article_id: this.currentArticle.id, 
            user_id: userId, 
            comment 
          },
          { 
            withCredentials: true,
            headers: {
              'Content-Type': 'application/json'
            }
          }
        );

        if (response.data.success) {
          await this.updateArticleComments();
          return true;
        }
        throw new Error(response.data.message || 'Failed to write comment');
      } catch (error) {
        this.error = error.message || 'Failed to write comment';
        throw error;
      } finally {
        this.loading = false;
      }
    }
  }
});