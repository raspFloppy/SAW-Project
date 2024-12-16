<template>
  <div>
    <form @submit.prevent="registerUser">
      <div>
        <label for="username">Username:</label>
        <input v-model="formData.username" type="text" id="username" required />
      </div>
      <div>
        <label for="email">Email:</label>
        <input v-model="formData.email" type="email" id="email" required />
      </div>
      <div>
        <label for="password">Password:</label>
        <input v-model="formData.password" type="password" name="password" id="password" autocomplete="on" required />
      </div>
      <button type="submit">Register</button>
    </form>
    <p v-if="responseMessage">{{ responseMessage }}</p>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      formData: {
        username: '',
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

<style scoped>
form {
  max-width: 400px;
  margin: auto;
  display: flex;
  flex-direction: column;
}
div {
  margin-bottom: 1rem;
}
button {
  padding: 0.5rem;
}
</style>
