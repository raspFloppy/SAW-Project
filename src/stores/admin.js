import { defineStore } from "pinia";
import axios from "axios";

const API_BASE = import.meta.env.VITE_API_BASE;

export const useAdminStore = defineStore("admin", {
  state: () => ({
    users: [],
    user: null,
    isLoggedIn: false,
    isAdmin: false,
    loading: false,
    error: null,
    totalUsers: 0,
    totalLikes: 0,
    totalDislikes: 0,
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

    async getAllLikes() {
      this.loading = true;

      const user = JSON.parse(localStorage.getItem("user"));
      const userId = user ? user.id : null;

      if (!userId) {
        throw new Error("User must be logged in");
      }

      try {
        const response = await axios.get(API_BASE, {
          params: {
            action: "get_all_favorites",
          },
          headers: {
            "Content-Type": "application/json",
          },
          withCredentials: true,
        });

        if (response.data.success) {
          this.totalLikes = response.data.count;
          return response.data.count;
        } else {
          throw new Error(response.data.message || "Failed to fetch likes");
        }
      } catch (error) {
        this.error = error.message || "Failed to fetch likes";
        throw error;
      } finally {
        this.loading = false;
      }
    },

    async getAllDislikes() {
      this.loading = true;

      const user = JSON.parse(localStorage.getItem("user"));
      const userId = user ? user.id : null;

      if (!userId) {
        throw new Error("User must be logged in");
      }

      try {
        const response = await axios.get(API_BASE, {
          params: {
            action: "get_all_dislikes",
          },
          headers: {
            "Content-Type": "application/json",
          },
          withCredentials: true,
        });

        if (response.data.success) {
          this.totalDislikes = response.data.count;
          return response.data.count;
        } else {
          throw new Error(response.data.message || "Failed to fetch dislikes");
        }
      } catch (error) {
        this.error = error.message || "Failed to fetch dislikes";
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

    async deleteUser(userId) {
      this.loading = true;
      const user = JSON.parse(localStorage.getItem("user"));
      const adminId = user ? user.id : null;
      const userType = JSON.parse(localStorage.getItem("user")).type;

      if (!adminId) {
        throw new Error("User must be logged in");
      }

      if (userType !== "admin") {
        throw new Error("User must be an admin");
      }

      try {
        const response = await axios.post(
          API_BASE,
          {
            user_id: userId,
            admin_id: adminId,
          },
          {
            params: {
              action: "delete_user",
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
          throw new Error(response.data.message || "Failed to delete user");
        }
      } catch (error) {
        this.error = error.message || "Failed to delete user";
        throw error;
      } finally {
        this.loading = false;
      }
    },

    async changeUserRole(userId, newRole) {
      this.loading = true;
      const user = JSON.parse(localStorage.getItem("user"));
      const adminId = user ? user.id : null;
      const userType = user ? user.type : null;

      if (!adminId) {
        throw new Error("User must be logged in");
      }

      if (userType !== "admin") {
        throw new Error("User must be an admin");
      }

      try {
        const response = await axios.post(
          API_BASE,
          {
            user_id: userId,
            admin_id: adminId,
            type: newRole,
          },
          {
            params: {
              action: "change_user_type",
            },
            headers: {
              "Content-Type": "application/json",
            },
            withCredentials: true,
          }
        );

        if (response.data.success) {
          this.users = this.users.map((user) =>
            user.id === userId ? { ...user, type: newRole } : user
          );
          await this.getAllUsers();
          return response.data;
        } else {
          throw new Error(
            response.data.message || "Failed to change user role"
          );
        }
      } catch (error) {
        this.error = error.message || "Failed to change user role";
        throw error;
      } finally {
        this.loading = false;
      }
    },
  },
});

export default useAdminStore;
