<script>
import axios from 'axios';

export default {
  data() {
    return {
      formData: {
        firstname: '',
        lastname: '',
        email: '',
        password: '',
      },
      responseMessage: '',
    };
  },
  methods: {
    async registerUser() {
      try {
        const response = await axios.post('http://localhost:8000/index.php?action=register', this.formData, {
          headers: {
            'Content-Type': 'application/json',
          },
        });

        if (response.data.success) {
          this.responseMessage = response.data.message;
          this.$router.push('/login')
        } else {
          this.responseMessage = `Server Error: ${response.data.message || 'Registration failed'}`;
        }
      } catch (err) {
        this.responseMessage = `Response Error: ${err.message}`;
      }
    },
  },
};
</script>


<template>
  <div class="hero bg-base-200 min-h-screen">
    <div class="hero-content flex-col lg:flex-row-reverse">
      <div class="text-center lg:text-left">
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
            <label class="label">
              <a href="#" class="label-text-alt link link-hover">Forgot password?</a>
            </label>
          </div>
          <div class="form-control mt-6">
            <button type="submit" class="btn btn-primary">Register</button>
          </div>
        </form>
        <p v-if="responseMessage">{{ responseMessage }}</p>
      </div>
    </div>
  </div>
</template>
