import { defineStore } from "pinia";
import axios from "axios";

const API_BASE = import.meta.env.VITE_API_BASE;

export const useAuthStore = defineStore("auth", {
  state: () => ({
    user: null,
    isLoggedIn: false,
    isAdmin: false,
    loading: false,
    error: null,
  }),

  actions: {
    async register(formData) {
      this.loading = true;
      try {
        const response = await axios.post(API_BASE, formData, {
          params: {
            action: "register",
          },
          headers: {
            "Content-Type": "application/json",
          },
          withCredentials: true,
        });

        if (response.data.success) {
          return {
            success: response.data.success,
            message: response.data.message,
          };
        } else {
          return {
            success: response.data.success,
            message: response.data.message,
          };
        }
      } catch (error) {
        this.error = error.message || "Login error, login failed";
        throw error;
      } finally {
        this.loading = false;
      }
    },

    async login(email, password) {
      this.loading = true;
      try {
        const response = await axios.post(
          API_BASE,
          {
            email,
            password,
          },
          {
            params: {
              action: "login",
            },
            headers: {
              "Content-Type": "application/json",
            },
            withCredentials: true,
          }
        );

        if (response.data.success) {
          this.isLoggedIn = true;
          this.isAdmin = response.data.user === "Admin";
          this.user = response.data.user;
          localStorage.setItem("user", JSON.stringify(response.data.user));
          return {
            success: response.data.success,
            message: response.data.message,
          };
        }

        return {
          success: response.data.success,
          message: response.data.message,
        };
      } catch (error) {
        this.error = error.message || "Login failed";
        throw error;
      } finally {
        this.loading = false;
      }
    },

    async logout() {
      this.loading = true;
      try {
        const response = await axios.post(
          API_BASE,
          {},
          {
            params: {
              action: "logout",
            },
            headers: {
              "Content-Type": "application/json",
            },
            withCredentials: true,
          }
        );

        if (response.data.success) {
          this.clearAuth();
          return { success: true, message: response.data.message };
        }

        throw new Error(response.data.message || "Logout failed");
      } catch (error) {
        this.error = error.message || "Logout failed";
        throw error;
      } finally {
        this.loading = false;
      }
    },

    clearAuth() {
      this.isLoggedIn = false;
      this.user = null;
      localStorage.removeItem("user");
    },

    async updateProfile(firstname, lastname, email) {
      this.loading = true;
      try {
        const response = await axios.post(
          API_BASE,
          {
            firstname,
            lastname,
            email,
          },
          {
            params: {
              action: "update_profile",
            },
            headers: {
              "Content-Type": "application/json",
            },
            withCredentials: true,
          }
        );

        if (response.data.success) {
          this.user = {
            ...this.user,
            firstname,
            lastname,
            email,
          };
          localStorage.setItem("user", JSON.stringify(this.user));
          return { success: true, message: response.data.message };
        }

        throw new Error(response.data.message || "Update failed");
      } catch (error) {
        this.error = error.message || "Update failed";
        throw error;
      } finally {
        this.loading = false;
      }
    },

    async validateSession() {
      this.loading = true;
      try {
        const response = await axios.get(API_BASE, {
          params: {
            action: "show_profile",
          },
          headers: {
            "Content-Type": "application/json",
          },
          withCredentials: true,
        });

        if (response.data.success) {
          this.isLoggedIn = true;
          this.isAdmin = response.data.user.type === "admin";
          this.user = response.data.user;
          localStorage.setItem("user", JSON.stringify(response.data.user));
        } else {
          this.clearAuth();
        }
      } catch (error) {
        this.clearAuth();
        this.error = error.message || "Session validation failed";
      } finally {
        this.loading = false;
      }
    },

    async initializeStore() {
      const storedUser = localStorage.getItem("user");
      if (storedUser) {
        this.user = JSON.parse(storedUser);
        this.isLoggedIn = true;
      }
      await this.validateSession();
    },

    async deleteAccount() {
      this.loading = true;

      this.validateSession();
      user = JSON.parse(localStorage.getItem("user"));
      userId = user ? user.id : null;

      if (!userId) {
        throw new Error("User must be logged in");
      }

      try {
        const response = await axios.post(
          API_BASE,
          { user_id },
          {
            params: {
              action: "delete_profile",
            },
            headers: {
              "Content-Type": "application/json",
            },
            withCredentials: true,
          }
        );

        console.log(response.data);
        if (response.data.success) {
          this.clearAuth();
          return { success: true, message: response.data.message };
        }

        throw new Error(response.data.message || "Failed to delete account");
      } catch (error) {
        this.error = error.message || "Failed to delete account";
        throw error;
      } finally {
        this.loading = false;
      }
    },
  },
});

export default useAuthStore;
