<script>
import { useAuthStore } from '@/stores/auth'
import { useRouter } from 'vue-router'

export default {
    name: 'DashboardButton',

    setup() {
        const authStore = useAuthStore()
        const router = useRouter()

        async function handleDashboard() {
            try {
                if (!authStore.isLoggedIn) {
                    router.push('/login')
                } else {
                    router.push('/dashboard')
                }
            } catch (error) {
                console.error('Dashboard button failed:', error)
            }
        }

        return { handleDashboard }
    }
}
</script>

<template>
    <a @click.prevent="handleDashboard" class="dropdown-item red-text">
        Dashboard
    </a>
</template>
