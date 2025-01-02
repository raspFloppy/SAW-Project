<script>
import { useAuthStore } from '@/stores/auth'
import LogoutButton from './components/LogoutButton.vue'
import { computed } from 'vue'

export default {
  components: {
    LogoutButton
  },
  setup() {
    const authStore = useAuthStore()

    const isLoggedIn = computed(() => authStore.isLoggedIn)

    return {
      isLoggedIn
    }
  }
}
</script>

<template>
  <nav class="p-2 bg-base-200">
    <RouterLink to="/" class="mr-4">Home</RouterLink>
    <RouterLink to="/articles" class="mx-4">Articles</RouterLink>
    <template v-if="!isLoggedIn">
      <div class="ml-auto flex space-x-2">
        <RouterLink to="/login" class="btn btn-ghost">Login</RouterLink>
        <RouterLink to="/registration" class="btn btn-outline btn-secondary">Sign Up</RouterLink>
      </div>
    </template>
    <template v-else>
      <RouterLink to="/dashboard" class="mx-4">Dashboard</RouterLink>
      <div style="margin-left: auto;">
        <LogoutButton class="ml-2" />
      </div>
    </template>
  </nav>
  <main>
    <RouterView />
  </main>
</template>

<style scoped>
nav {
  display: flex;
  align-items: center;
}

a {
  text-decoration: none;
  color: inherit;
}

a:hover {
  color: #666;
}

.router-link-active {
  font-weight: bold;
}
</style>