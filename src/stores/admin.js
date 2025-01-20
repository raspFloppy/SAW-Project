import { defineStore } from 'pinia';
import axios from 'axios';

const API_BASE = import.meta.env.VITE_API_BASE;

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    isLoggedIn: false,
    isAdmin: false,
    loading: false,
    error: null
  }),
  
  actions: {
    async get_all_users() {
        this.loading = true;
        try {
            const response = await axios.get(API_BASE, {
            params: {
                action: 'get_all_users'
            },
            headers: {
                'Content-Type': 'application/json',
            },
            withCredentials: true
            });
    
            if (response.data.success) {
            return response.data.users;
            } else {
            throw new Error(response.data.message || 'Failed to fetch users');
            }
        } catch (error) {
            this.error = error.message || 'Failed to fetch users';
            throw error;
        } finally {
            this.loading = false;
        }
        }
  }
});