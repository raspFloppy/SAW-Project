import { defineStore } from "pinia";
import axios from "axios";

const API_BASE = import.meta.env.VITE_API_BASE;

export const useArticleStore = defineStore("article", {
  state: () => ({
    articles: [],
    currentArticle: null,
    loading: false,
    error: null,
    currentPage: 1,
    perPage: 3,
    totalPages: 1,
    total: 0,
    articleLikesCount: 0,
    articleCommentsCount: 0,
  }),

  actions: {
    async fetchArticles(page = 1) {
      this.loading = true;
      try {
        const response = await axios.get(API_BASE, {
          params: {
            action: "get_articles",
            page,
            per_page: this.perPage,
          },
          withCredentials: true,
        });

        if (response.data.success) {
          this.articles = response.data.articles;
          this.currentPage = response.data.current_page;
          this.totalPages = response.data.last_page;
          this.total = response.data.total;
        } else {
          throw new Error(response.data.message || "Failed to fetch articles");
        }
      } catch (error) {
        this.error = error.message || "Failed to fetch articles";
        throw error;
      } finally {
        this.loading = false;
      }
    },

    async fetchArticleById(article_id) {
      this.loading = true;
      try {
        const user = JSON.parse(localStorage.getItem("user"));
        const userId = user ? user.id : null;

        const response = await axios.get(API_BASE, {
          withCredentials: true,
          params: {
            action: "get_article",
            id: article_id,
            user_id: userId,
          },
        });

        if (response.data.success) {
          this.currentArticle = {
            ...response.data.article,
            is_favorite: response.data.article.is_favorite || false,
          };
          this.updateArticleCommentsCount();
          this.updateArticleLikesCount();
        } else {
          throw new Error(response.data.message || "Failed to fetch article");
        }
      } catch (error) {
        this.error = error.message || "Failed to fetch article";
        throw error;
      } finally {
        this.loading = false;
      }
    },

    async toggleFavorite() {
      const user = JSON.parse(localStorage.getItem("user"));
      const userId = user ? user.id : null;

      if (!userId || !this.currentArticle) {
        throw new Error("User must be logged in and article must be selected");
      }

      this.loading = true;
      try {
        const response = await axios.post(
          API_BASE,
          {
            article_id: this.currentArticle.id,
            user_id: userId,
          },
          {
            params: {
              action: "set_favorite",
            },
            headers: {
              "Content-Type": "application/json",
            },
            withCredentials: true,
          }
        );

        if (response.data.success) {
          this.currentArticle = {
            ...this.currentArticle,
            is_favorite: !this.currentArticle.is_favorite,
            is_disliked: false,
          };
          await this.updateArticleLikesCount();
          await this.updateArticleDislikesCount();
          return true;
        }
        throw new Error(response.data.message || "Failed to toggle favorite");
      } catch (error) {
        this.error = error.message || "Failed to toggle favorite";
        throw error;
      } finally {
        this.loading = false;
      }
    },

    async getFavorites() {
      const user = JSON.parse(localStorage.getItem("user"));
      const userId = user ? user.id : null;

      if (!userId) {
        throw new Error("User must be logged in");
      }

      try {
        const response = await axios.get(API_BASE, {
          params: {
            action: "get_favorites",
            user_id: userId,
          },
          withCredentials: true,
        });

        if (response.data.success) {
          return response.data.favorites;
        }
        throw new Error(response.data.message || "Failed to fetch favorites");
      } catch (error) {
        this.error = error.message || "Failed to fetch favorites";
        throw error;
      }
    },

    async getFavoritesCount() {
      const user = JSON.parse(localStorage.getItem("user"));
      const userId = user ? user.id : null;

      if (!userId) return 0;

      try {
        const response = await axios.get(API_BASE, {
          params: {
            action: "get_favorites_count",
            user_id: userId,
          },
          headers: {
            "Content-Type": "application/json",
          },
          withCredentials: true,
        });

        if (response.data.success) {
          return response.data.count;
        }
        return 0;
      } catch (error) {
        this.error = error.message || "Failed to fetch favorites count";
        return 0;
      }
    },

    async toggleDislike() {
      const user = JSON.parse(localStorage.getItem("user"));
      const userId = user ? user.id : null;

      if (!userId || !this.currentArticle) {
        throw new Error("User must be logged in and article must be selected");
      }

      this.loading = true;
      try {
        const response = await axios.post(
          API_BASE,
          {
            article_id: this.currentArticle.id,
            user_id: userId,
          },
          {
            params: {
              action: "set_dislike",
            },
            headers: {
              "Content-Type": "application/json",
            },
            withCredentials: true,
          }
        );

        if (response.data.success) {
          this.currentArticle = {
            ...this.currentArticle,
            is_disliked: !this.currentArticle.is_disliked,
            is_favorite: false,
          };
          await this.updateArticleLikesCount();
          await this.updateArticleDislikesCount();
          return true;
        }
        throw new Error(response.data.message || "Failed to toggle dislike");
      } catch (error) {
        this.error = error.message || "Failed to toggle dislike";
        throw error;
      } finally {
        this.loading = false;
      }
    },

    async updateArticleComments() {
      if (!this.currentArticle) {
        throw new Error("No article selected");
      }

      try {
        const response = await axios.get(API_BASE, {
          withCredentials: true,
          params: {
            action: "get_comments",
            article_id: this.currentArticle.id,
          },
        });

        if (response.data.success) {
          this.currentArticle = {
            ...this.currentArticle,
            comments: response.data.comments,
          };
        } else {
          throw new Error(response.data.message || "Failed to fetch comments");
        }
      } catch (error) {
        this.error = error.message || "Failed to fetch comments";
        throw error;
      }
    },

    async writeArticleComment(comment) {
      const user = JSON.parse(localStorage.getItem("user"));
      const userId = user ? user.id : null;

      if (!userId || !this.currentArticle) {
        throw new Error("User must be logged in and article must be selected");
      }

      this.loading = true;
      try {
        const response = await axios.post(
          API_BASE,
          {
            article_id: this.currentArticle.id,
            user_id: userId,
            comment,
          },
          {
            params: {
              action: "write_comment",
            },
            headers: {
              "Content-Type": "application/json",
            },
            withCredentials: true,
          }
        );

        if (response.data.success) {
          await this.updateArticleComments();
          await this.updateArticleCommentsCount();
          return true;
        }
        throw new Error(response.data.message || "Failed to write comment");
      } catch (error) {
        this.error = error.message || "Failed to write comment";
        throw error;
      } finally {
        this.loading = false;
      }
    },

    async updateArticleLikesCount() {
      if (!this.currentArticle) return 0;

      try {
        const response = await axios.get(API_BASE, {
          params: {
            action: "get_article_favorites_count",
            article_id: this.currentArticle.id,
          },
          headers: {
            "Content-Type": "application/json",
          },
          withCredentials: true,
        });

        if (response.data.success) {
          this.articleLikesCount = response.data.count;
        }
      } catch (error) {
        this.error = error.message || "Failed to fetch likes count";
        return 0;
      }
    },

    async updateArticleDislikesCount() {
      if (!this.currentArticle) return 0;

      try {
        const response = await axios.get(API_BASE, {
          params: {
            action: "get_article_dislikes_count",
            article_id: this.currentArticle.id,
          },
          headers: {
            "Content-Type": "application/json",
          },
          withCredentials: true,
        });

        if (response.data.success) {
          this.articleDislikesCount = response.data.count;
        }
      } catch (error) {
        this.error = error.message || "Failed to fetch dislikes count";
        return;
      }
    },

    async updateArticleCommentsCount() {
      if (!this.currentArticle) return 0;

      try {
        const response = await axios.get(API_BASE, {
          params: {
            action: "get_article_comments_count",
            article_id: this.currentArticle.id,
          },
          headers: {
            "Content-Type": "application/json",
          },
          withCredentials: true,
        });

        if (response.data.success) {
          this.articleCommentsCount = response.data.count;
        }
      } catch (error) {
        this.error = error.message || "Failed to fetch comments count";
        return 0;
      }
    },

    async writeArticle(title, content, author) {
      const user = JSON.parse(localStorage.getItem("user"));
      const userId = user ? user.id : null;
      const userType = JSON.parse(localStorage.getItem("user")).type;

      if (!userId) {
        throw new Error("User must be logged in");
      }

      if (userType !== "admin") {
        throw new Error("User must be an admin");
      }

      this.loading = true;
      try {
        const response = await axios.post(
          API_BASE,
          {
            title,
            content,
            author,
            author_id: userId,
          },
          {
            params: {
              action: "write_article",
            },
            headers: {
              "Content-Type": "application/json",
            },
            withCredentials: true,
          }
        );

        if (response.data.success) {
          return response.data.article_id;
        }
        throw new Error(response.data.message || "Failed to write article");
      } catch (error) {
        this.error = error.message || "Failed to write article";
        throw error;
      } finally {
        this.loading = false;
      }
    },
  },
});
