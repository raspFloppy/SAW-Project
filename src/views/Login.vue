<script>
import { useAuthStore } from '@/stores/auth'
import { useRouter } from 'vue-router';

export default {
  setup() {
    const authStore = useAuthStore();
    const router = useRouter();

    return { authStore, router };
  },
  data() {
    return {
      formData: {
        email: '',
        password: '',
      },
      responseMessage: '',
    };
  },
  methods: {
    async loginUser() {
      try {
        const result = await this.authStore.login(
          this.formData.email, 
          this.formData.password
        );

        if (result.success) {
          this.responseMessage = result.message;
          this.router.push('/dashboard');
        } else {
          this.responseMessage = `Error: ${result.message}`;
        }
      } catch (err) {
        this.responseMessage = `Error: ${err.message}`;
      }
    },
  },
};
</script>

<template>
  <div class="hero bg-base-200 min-h-screen">
    <div class="hero-content flex-col lg:flex-row-reverse">
      <div class="text-center lg:text-left">
        <h1 class="text-5xl font-bold">Login now!</h1>
      </div>
      <div class=" card bg-base-100 w-full max-w-sm shrink-0 shadow-2xl">
        <form class="card-body" @submit.prevent="loginUser">
          <div class="form-control">
            <label class="label">
              <span class="label-text">Email</span>
            </label>
            <input v-model="formData.email" type="email" id="email" placeholder="email" class="input input-bordered" required />
          </div>
          <div class="form-control">
            <label class="label">
              <span class="label-text">Password</span>
            </label>
            <input v-model="formData.password" type="password" name="password" id="password" autocomplete="on" placeholder="password" class="input input-bordered" required />
            <label class="label">
              <a href="#" class="label-text-alt link link-hover">Forgot password?</a>
            </label>
          </div>
          <div class="form-control mt-6">
            <button class="btn btn-primary" type="submit">Login</button>
          </div>
        </form>
        <p v-if="responseMessage">{{ responseMessage }}</p>
      </div>
    </div>
  </div>
</template>
