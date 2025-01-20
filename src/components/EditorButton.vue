<script>
import { useAuthStore } from '@/stores/auth'
import { useRouter } from 'vue-router'

export default {
    name: 'EditorButton',

    setup() {
        const authStore = useAuthStore()
        const router = useRouter()

        async function handleEditor() {
            try {
                if (!authStore.isLoggedIn) {
                    router.push('/articles')
                } else {
                    router.push('/article-editor')
                }
            } catch (error) {
                console.error('Editor Button button failed:', error)
            }
        }

        return { handleDashboard }
    }
}
</script>

<template>
    <a @click.prevent="handleEditor" class="dropdown-item red-text">
        Dashboard
    </a>
</template>
