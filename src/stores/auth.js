import { defineStore } from 'pinia'
import axios from 'axios'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    isLoggedIn: false
  }),

  actions: {
    async login(email, password) {
      try {
        const response = await axios.post('http://localhost:8000/index.php?action=login', 
          { email, password },
          {
            headers: {
              'Content-Type': 'application/json',
            },
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
          message: error.response?.data?.message || 'Login failed'
        };
      }
    },

    async logout() {
      try {
        axios.get('http://localhost:8000/index.php?action=logout');
      } catch (error) {
        console.error('Logout error', error);
      }

      this.isLoggedIn = false;
      this.user = null;

      localStorage.removeItem('user');
      localStorage.removeItem('isLoggedIn');
    },

    async updateProfile(firstname, lastname, email) {
      try {
        const response = await axios.post('http://localhost:8000/index.php?action=update_profile', 
          { firstname, lastname, email },
          {
            headers: {
              'Content-Type': 'application/json',
            },
          }
        );

        if (response.data.success) {
          this.user = {
            ...this.user,
            firstname,
            lastname,
            email
          };
          localStorage.setItem('user', JSON.stringify(response.data.user));

          return { success: true, message: response.data.message };
        } else {
          return { success: false, message: response.data.message };
        }
      } catch (error) {
        return { 
          success: false, 
          message: error.response?.data?.message || 'Update failed'
        };
      }
    },

    initializeStore() {
      const storedUser = localStorage.getItem('user');
      const storedLoginStatus = localStorage.getItem('isLoggedIn');

      if (storedUser && storedLoginStatus === 'true') {
        this.user = JSON.parse(storedUser);
        this.isLoggedIn = true;
      }
    }
  }
});

export default useAuthStore;