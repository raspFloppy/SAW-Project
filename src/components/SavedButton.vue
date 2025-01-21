<script>
import { useAuthStore } from '@/stores/auth'
import { useRouter } from 'vue-router'

export default {
    name: 'SavedButton',

    setup() {
        const authStore = useAuthStore()
        const router = useRouter()

        async function handleSaved() {
            try {
                if (!authStore.isLoggedIn) {
                    router.push('/login')
                } else {
                    router.push('/favorite_articles')
                }
            } catch (error) {
                console.error('Saved button failed:', error)
            }
        }

        return { handleSaved }
    }
}
</script>

<template>
    <a @click.prevent="handleSaved" class="dropdown-item red-text">
        Favorites
    </a>
</template>
