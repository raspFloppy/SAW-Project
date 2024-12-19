<script>
import { useAuthStore } from '@/stores/auth'
import { useRouter } from 'vue-router'

export default {
  name: 'LogoutButton',

  setup() {
    const authStore = useAuthStore()
    const router = useRouter()

    const handleLogout = async () => {
      try {
        const isConfirmed = confirm('Are you sure you want to log out?')
        if (!isConfirmed) {
          return
        }
        authStore.logout()
        router.push('/')
      } catch (error) {
        console.error('Logout failed:', error)
      }
    }

    return { handleLogout }
  }
}
</script>

<template>
  <button @click.prevent="handleLogout" class="btn btn-error btn-sm">
    Logout
  </button>
</template>
