<script>
import { useAuthStore } from '@/stores/auth'
import { useRouter } from 'vue-router'

export default {
  name: 'LogoutButton',

  setup() {
    const authStore = useAuthStore()
    const router = useRouter()

    async function handleLogout() {
      try {
        const isConfirmed = confirm('Are you sure you want to log out?')
        if (!isConfirmed) {
          return
        }
        authStore.logout()
        router.push('/')
      } catch (error) {
        console.error('Logout button failed:', error)
      }
    }

    return { handleLogout }
  }
}
</script>

<template>
  <a @click.prevent="handleLogout" class="dropdown-item text-red-500">
    Logout
  </a>
</template>
