import { defineStore } from 'pinia';
import axios from 'axios';

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    isLoggedIn: false,
  }),

  actions: {
    async login(email, password) {
      try {
        const response = await axios.post(
          'http://localhost:8000/index.php?action=login',
          { email, password },
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
          localStorage.setItem('isLoggedIn', 'true');

          return { success: true, message: response.data.message };
        } else {
          return { success: false, message: response.data.message };
        }
      } catch (error) {
        return {
          success: false,
          message: error.response?.data?.message || 'Login failed',
        };
      }
    },

    async logout() {
      try {
        const response = await axios.get('http://localhost:8000/index.php?action=logout', {
          withCredentials: true,
        });

        if (response.data.success) {
          console.log('Logout successful');
          this.isLoggedIn = false;
          this.user = null;

          localStorage.removeItem('user');
          localStorage.removeItem('isLoggedIn');
        }

        return response.data;
      } catch (error) {
        console.log('Logout error', error);
        console.error('Logout error', error);
        return { success: false, message: 'Logout failed' };
      }
    },

    async updateProfile(firstname, lastname, email) {
      try {
        const response = await axios.post('http://localhost:8000/index.php?action=update_profile',
          { firstname, lastname, email },
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
        } else {
          return { success: false, message: response.data.message };
        }
      } catch (error) {
        return {
          success: false,
          message: error.response?.data?.message || 'Update failed',
        };
      }
    },

    async validateSession() {
      try {
        const response = await axios.get('http://localhost:8000/index.php?action=show_profile', {
          withCredentials: true,
        });

        if (response.data.success) {
          console.log('Session validated:', response.data.user);
          this.isLoggedIn = true;
          this.user = response.data.user;

          localStorage.setItem('user', JSON.stringify(response.data.user));
          localStorage.setItem('isLoggedIn', 'true');
        } else {
          console.log('Session validation failed');
          this.isLoggedIn = false;
          this.user = null;

          localStorage.removeItem('user');
          localStorage.removeItem('isLoggedIn');
        }
      } catch (error) {
        console.error('Session validation error:', error);

        this.isLoggedIn = false;
        this.user = null;
        localStorage.removeItem('user');
        localStorage.removeItem('isLoggedIn');
      }
    },

    initializeStore() {
      this.validateSession();

      const storedUser = localStorage.getItem('user');
      const storedLoginStatus = localStorage.getItem('isLoggedIn');

      if (storedUser && storedLoginStatus === 'true') {
        this.user = JSON.parse(storedUser);
        this.isLoggedIn = true;
      }
    },
  },
});

export default useAuthStore;
