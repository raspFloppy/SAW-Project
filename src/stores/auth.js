import { defineStore } from 'pinia';
import axios from 'axios';
import { useRouter } from 'vue-router';

const API_BASE = 'https://saw.dibris.unige.it/~s5145768/backend/index.php';

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    isLoggedIn: false,
    loading: false,
    error: null
  }),

  actions: {
    async login(email, password) {
      this.loading = true;
      try {
        const response = await axios.post(API_BASE, 
          { 
            action: 'login',
            email, 
            password 
          },
          {
            headers: {
              'Content-Type': 'application/json',
            },
            withCredentials: true,
          }
        );

        if (response.data.success) {
          this.isLoggedIn = true;
          this.user = response.data.user;
          localStorage.setItem('user', JSON.stringify(response.data.user));
          return { success: true, message: response.data.message };
        }
        
        throw new Error(response.data.message || 'Login failed');
      } catch (error) {
        this.error = error.message || 'Login failed';
        throw error;
      } finally {
        this.loading = false;
      }
    },

    async logout() {
      this.loading = true;
      try {
        const response = await axios.post(API_BASE,
          { action: 'logout' },
          { withCredentials: true }
        );

        if (response.data.success) {
          this.clearAuth();
          return { success: true, message: response.data.message };
        }
        
        throw new Error(response.data.message || 'Logout failed');
      } catch (error) {
        this.error = error.message || 'Logout failed';
        throw error;
      } finally {
        this.loading = false;
      }
    },

    clearAuth() {
      this.isLoggedIn = false;
      this.user = null;
      localStorage.removeItem('user');
    },

    async updateProfile(firstname, lastname, email) {
      this.loading = true;
      try {
        const response = await axios.post(API_BASE,
          { 
            action: 'update_profile',
            firstname, 
            lastname, 
            email 
          },
          {
            headers: {
              'Content-Type': 'application/json',
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
          localStorage.setItem('user', JSON.stringify(this.user));
          return { success: true, message: response.data.message };
        }
        
        throw new Error(response.data.message || 'Update failed');
      } catch (error) {
        this.error = error.message || 'Update failed';
        throw error;
      } finally {
        this.loading = false;
      }
    },

    async validateSession() {
      this.loading = true;
      try {
        const response = await axios.get(API_BASE, {
          params: { action: 'show_profile' },
          withCredentials: true,
        });

        if (response.data.success) {
          this.isLoggedIn = true;
          this.user = response.data.user;
          localStorage.setItem('user', JSON.stringify(response.data.user));
        } else {
          this.clearAuth();
        }
      } catch (error) {
        this.clearAuth();
        this.error = error.message || 'Session validation failed';
      } finally {
        this.loading = false;
      }
    },

    async initializeStore() {
      const storedUser = localStorage.getItem('user');
      if (storedUser) {
        this.user = JSON.parse(storedUser);
        this.isLoggedIn = true;
      }
      await this.validateSession();
    },
  },
});

export default useAuthStore;