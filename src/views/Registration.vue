<script>
import { ref } from 'vue'
import { useAuthStore } from '@/stores/auth';
import { showAlert } from '@/utils/utils';
import { useRouter } from 'vue-router';

export default {
  setup() {
    const authStore = useAuthStore();
    const router = useRouter();
    const alert = ref({ show: false, type: '', message: ''});

    return { authStore, router, alert };
  },

  data() {
    return {
      formData: {
        firstname: '',
        lastname: '',
        email: '',
        password: '',
      },
    };
  },

  methods: {
    async registerUser() {
      try {
        const result = await this.authStore.register(this.formData);

        if(result.success) {
          showAlert(this.alert, result.success, result.message);
          this.router.push('/login');
        } else {
          showAlert(this.alert, result.success, result.message);
        }
      } catch(err) {
        showAlert(this.alert, false, 'Failed to register');
      }
    }
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
        <h1 class="text-5xl font-bold">Register now!</h1>
      </div>
      <div class="card bg-base-100 w-full max-w-sm shrink-0 shadow-2xl">
        <form class="card-body" @submit.prevent="registerUser">
          <div class="form-control">
            <label class="label">
              <span class="label-text">First Name</span>
            </label>
            <input v-model="formData.firstname" type="text" placeholder="firstname" class="input input-bordered"
              required />
          </div>
          <div class="form-control">
            <label class="label">
              <span class="label-text">Last Name</span>
            </label>
            <input v-model="formData.lastname" type="text" placeholder="lastname" class="input input-bordered"
              required />
          </div>
          <div class="form-control">
            <label class="label">
              <span class="label-text">Email</span>
            </label>
            <input v-model="formData.email" type="email" placeholder="email" class="input input-bordered" required />
          </div>
          <div class="form-control">
            <label class="label">
              <span class="label-text">Password</span>
            </label>
            <input v-model="formData.password" type="password" placeholder="password" class="input input-bordered"
              minlength="8" required />
          </div>
          <div class="form-control mt-6">
            <button type="submit" class="btn btn-primary">Register</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>
