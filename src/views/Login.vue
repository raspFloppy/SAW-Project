<script>
import { ref } from 'vue';
import { useAuthStore } from '@/stores/auth'
import { useRouter } from 'vue-router';
import { showAlert } from '@/utils/utils'

export default {
  setup() {
    const authStore = useAuthStore();
    const router = useRouter();
    const alert = ref({ show: false, type: '', message: '' });

    return { authStore, router, alert };
  },
  data() {
    return {
      formData: {
        email: '',
        password: '',
      },
    };
  },
  methods: {
    //TODO: Alert does not work
    async loginUser() {
      try {
        const result = await this.authStore.login(
          this.formData.email,
          this.formData.password
        );

        if (result.success) {
          showAlert(this.alert, result.success, 'Login successful');
          this.router.push('/dashboard');
        } else {
          showAlert(this.alert, result.success, result.message);
        }
      } catch (err) {
        showAlert(this.alert, false, 'Failed to login');
      }
    },
  },
};
</script>

<template>
  <div class="hero bg-base-200 min-h-screen" style="padding-bottom: 100px;">
    <div class="hero-content flex-col items-center">
      <div v-if="alert.show" :class="`alert ${alert.type} mb-4`">
        <span>{{ alert.message }}</span>
      </div>
      <div class="text-center mb-6">
        <h1 class="text-5xl font-bold">Login now!</h1>
      </div>
      <div class="card bg-base-100 w-full max-w-sm shrink-0 shadow-2xl">
        <form class="card-body" @submit.prevent="loginUser">
          <div class="form-control">
            <label class="label">
              <span class="label-text">
                Email
              </span>
            </label>
            <input v-model="formData.email" type="email" id="email" placeholder="email" class="input input-bordered"
              required />
          </div>
          <div class="form-control">
            <label class="label">
              <span class="label-text">Password</span>
            </label>
            <input v-model="formData.password" type="password" name="password" id="password" autocomplete="on"
              placeholder="password" class="input input-bordered" required />
            <label class="label">
              <a href="#" class="label-text-alt link link-hover">Forgot password?</a>
            </label>
          </div>
          <div class="form-control mt-6">
            <button class="btn btn-primary" type="submit">Login</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>
