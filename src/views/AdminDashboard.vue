<script setup>
import { ref, computed, onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { useArticleStore } from '@/stores/articles'
import { useRouter } from 'vue-router'
import { showAlert, formatDate } from '@/utils/utils'
import Navbar from "@/components/Navbar.vue"

const router = useRouter()
const auth = useAuthStore()
const article = useArticleStore()
const isLoading = ref(false)
const alert = ref({ show: false, type: '', message: '' })
const isEditing = ref(false)
const favoritesCount = ref(0)
const user = computed(() => auth.user)
const formData = ref({
    firstname: '',
    lastname: '',
    email: '',
    type: '',
    created_at: ''
})

function initializeForm() {
    if (user.value) {
        formData.value = {
            firstname: user.value.firstname,
            lastname: user.value.lastname,
            email: user.value.email,
            type: user.value.type,
            created_at: user.value.created_at
        }
    }
}

function resetForm() {
    initializeForm()
}

async function handleUpdateProfile() {
    isLoading.value = true
    try {
        const result = await auth.updateProfile(
            formData.value.firstname,
            formData.value.lastname,
            formData.value.email
        )
        if (result.success) {
            showAlert(alert, result.success, 'Profile updated successfully')
            isEditing.value = false
        } else {
            showAlert(alert, result.success, result.message)
        }
    } catch (error) {
        showAlert(alert, false, 'Failed to update profile')
    } finally {
        isLoading.value = false
    }
}

async function getFavoritesCount() {
    try {
        const result = await article.getFavoritesCount()
        return result
    } catch (error) {
        showAlert(alert, false, 'Failed to get favorites')
    }
}

async function goToFavorites() {
    router.push('/dashboard')
}

onMounted(async () => {
    if (!auth.isLoggedIn) {
        router.push('/login')
    } else if (!auth.isAdmin) {
        router.push('/dashboard')
    } else {
        initializeForm()
        favoritesCount = await getFavoritesCount()
    }
});
</script>

<template>
    <div class="min-h-screen bg-base-200">
        <Navbar>
            <template #left>
                <div class="flex-1">
                    <RouterLink to="/dashboard" class="btn btn-ghost normal-case text-xl">
                        User Dashboard
                    </RouterLink>
                    <RouterLink v-if="auth.isAdmin" to='/admin_dashboard' class="btn btn-ghost normal-case text-xl">
                        AdminDashboard
                    </RouterLink>
                </div>
            </template>
        </Navbar>
    </div>
</template>