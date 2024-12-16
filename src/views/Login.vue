<template>
  <div>
    <form @submit.prevent="loginUser">
      <div>
        <label for="email">Email:</label>
        <input v-model="formData.email" type="email" id="email" required />
      </div>
      <div>
        <label for="password">Password:</label>
        <input v-model="formData.password" type="password" id="password" required />
      </div>
      <button type="submit">Login</button>
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
        email: '',
        password: '',
      },
      responseMessage: '',
    };
  },
  methods: {
    async loginUser() {
      try {
        const response = await axios.post('http://localhost:8000/index.php?action=login', this.formData, {
          headers: {
            'Content-Type': 'application/json',
          },
        });

        if (response.status === 200) {
          this.responseMessage = 'Login successful!';
        } else {
          this.responseMessage = `Error: ${response.data.message || 'Login failed'}`;
        }
      } catch (err) {
        if (err.response && err.response.data && err.response.data.message) {
          this.responseMessage = `Error: ${err.response.data.message}`;
        } else {
          this.responseMessage = `Error: ${err.message}`;
        }
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
