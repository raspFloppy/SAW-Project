import { defineStore } from "pinia";
import axios from "axios";

const API_BASE = import.meta.env.VITE_API_BASE;

export const useAdminStore = defineStore("admin", {
  state: () => ({
    users: [],
    user: null,
    totalUsers: 0,
    isLoggedIn: false,
    isAdmin: false,
    loading: false,
    error: null,
  }),

  actions: {
    async getAllUsers() {
      this.loading = true;

      const user = JSON.parse(localStorage.getItem("user"));
      const userId = user ? user.id : null;

      if (!userId) {
        throw new Error("User must be logged in");
      }

      try {
        const response = await axios.get(API_BASE, {
          params: {
            action: "get_all_users",
          },
          headers: {
            "Content-Type": "application/json",
          },
          withCredentials: true,
        });

        console.log(response);
        if (response.data.success) {
          this.users = response.data.users;
          this.totalUsers = this.users.length;
          return response.data.users;
        } else {
          throw new Error(response.data.message || "Failed to fetch users");
        }
      } catch (error) {
        this.error = error.message || "Failed to fetch users";
        throw error;
      } finally {
        this.loading = false;
      }
    },

    async updateArticle(article_id, title, content, author) {
      this.loading = true;
      const user = JSON.parse(localStorage.getItem("user"));
      const userId = user ? user.id : null;
      const userType = JSON.parse(localStorage.getItem("user")).type;

      if (!userId) {
        throw new Error("User must be logged in");
      }

      if (userType !== "admin") {
        throw new Error("User must be an admin");
      }

      try {
        const response = await axios.post(
          API_BASE,
          {
            article_id,
            admin_id: userId,
            title,
            content,
            author,
          },
          {
            params: {
              action: "update_article",
            },

            headers: {
              "Content-Type": "application/json",
            },
            withCredentials: true,
          }
        );

        console.log(response);
        if (response.data.success) {
          return response.data;
        } else {
          throw new Error(response.data.message || "Failed to update article");
        }
      } catch (error) {
        this.error = error.message || "Failed to update article";
        throw error;
      } finally {
        this.loading = false;
      }
    },

    async deleteArticle(article_id) {
      this.loading = true;
      const user = JSON.parse(localStorage.getItem("user"));
      const userId = user ? user.id : null;
      const userType = JSON.parse(localStorage.getItem("user")).type;

      if (!userId) {
        throw new Error("User must be logged in");
      }

      if (userType !== "admin") {
        throw new Error("User must be an admin");
      }

      try {
        const response = await axios.post(
          API_BASE,
          {
            article_id,
          },
          {
            params: {
              action: "delete_article",
            },

            headers: {
              "Content-Type": "application/json",
            },
            withCredentials: true,
          }
        );

        if (response.data.success) {
          return response.data;
        } else {
          throw new Error(response.data.message || "Failed to delete article");
        }
      } catch (error) {
        this.error = error.message || "Failed to delete article";
        throw error;
      } finally {
        this.loading = false;
      }
    },
  },
});

export default useAdminStore;
