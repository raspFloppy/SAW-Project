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

async function handleProfileDelete() {
    isLoading.value = true
    try {
        if (confirm('Are you sure you want to delete your profile?')) {

            const result = await auth.deleteProfile()
            if (result.success) {
                showAlert(alert, result.success, 'Profile deleted successfully')
                router.push('/login')
            } else {
                showAlert(alert, result.success, result.message)
            }
        }
    } catch (error) {
        showAlert(alert, false, 'Failed to delete profile')
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
    router.push('/favorite_articles')
}

onMounted(async () => {
    await auth.validateSession();
    if (!auth.isLoggedIn) {
        router.push('/login')
    } else {
        initializeForm()
        favoritesCount.value = await getFavoritesCount()
    }
})
</script>

<template>
    <div class="min-h-screen bg-base-200">
        <Navbar>
            <template #left>
                <div class="flex-1">
                    <RouterLink :class="{ 'underline': $route.path === '/dashboard' }" to="/dashboard"
                        class="btn btn-ghost normal-case text-xl">
                        User Dashboard
                    </RouterLink>
                    <RouterLink :class="{ 'underline': $route.path === '/favorite_articles' }" to="/favorite_articles"
                        class="btn btn-ghost normal-case text-xl">
                        Favorites Dashboard
                    </RouterLink>
                    <RouterLink v-if="auth.isAdmin" :class="{ 'underline': $route.path === '/admin_dashboard' }"
                        to='/admin_dashboard' class="btn btn-ghost normal-case text-xl">
                        Admin Dashboard
                    </RouterLink>
                </div>
            </template>
        </Navbar>

        <div class="container mx-auto px-4 py-8">
            <div v-if="alert.show" :class="`alert ${alert.type} mb-4`">
                <span>{{ alert.message }}</span>
            </div>

            <!-- Profile Details -->
            <h2 class="text-2xl font-bold mb-4 px-2">Profile Details</h2>
            <div class="card bg-base-100 shadow-xl">
                <div class="card-body">
                    <div v-if="isEditing">
                        <form @submit.prevent="handleUpdateProfile" class="space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text">First Name</span>
                                    </label>
                                    <input type="text" v-model="formData.firstname" class="input input-bordered"
                                        required />
                                </div>

                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text">Last Name</span>
                                    </label>
                                    <input type="text" v-model="formData.lastname" class="input input-bordered"
                                        required />
                                </div>

                                <div class="form-control md:col-span-2">
                                    <label class="label">
                                        <span class="label-text">Email</span>
                                    </label>
                                    <input type="email" v-model="formData.email" class="input input-bordered"
                                        required />
                                </div>
                            </div>

                            <div class="flex justify-end gap-2">
                                <button type="button" class="btn btn-ghost" @click="resetForm">
                                    Reset
                                </button>
                                <button type="submit" class="btn btn-primary" :disabled="isLoading">
                                    {{ isLoading ? 'Updating...' : 'Update Profile' }}
                                </button>
                                <button type="button" class="btn btn-secondary" @click="isEditing = false">
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                    <div v-else>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="stat bg-base-200 rounded-box">
                                <div class="stat-title">First Name</div>
                                <div class="stat-value text-primary text-2xl">{{ user?.firstname }}</div>
                            </div>

                            <div class="stat bg-base-200 rounded-box">
                                <div class="stat-title">Last Name</div>
                                <div class="stat-value text-primary text-2xl">{{ user?.lastname }}</div>
                            </div>

                            <div class="stat bg-base-200 rounded-box md:col-span-2">
                                <div class="stat-title">Email</div>
                                <div class="stat-value text-primary text-2xl">{{ user?.email }}</div>
                            </div>
                        </div>
                        <div class="flex justify-end mt-4">
                            <button type="button" class="btn btn-primary" @click="isEditing = true">
                                Edit Profile
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- User Statistics -->
            <h2 class="text-2xl font-bold mt-8 mb-4 px-2">User Statistics</h2>
            <div class="stats stats-vertical md:stats-horizontal shadow bg-base-100 w-full">
                <div class="stat">
                    <div class="stat-figure text-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            class="w-6 h-6 sm:w-8 sm:h-8">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="stat-title">User Created</div>
                    <div class="stat-value text-primary text-lg sm:text-2xl">{{ formatDate(user?.created_at) }}
                    </div>
                    <div class="stat-desc">Account Age</div>
                </div>

                <div class="stat">
                    <div class="stat-figure text-secondary">
                        <svg @click.prevent="goToFavorites" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" class="w-6 h-6 sm:w-8 sm:h-8 cursor-pointer">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                    </div>
                    <div class="stat-title">Favorites</div>
                    <div class="stat-value text-secondary text-lg sm:text-2xl">{{ favoritesCount }}</div>
                    <div class="stat-desc">Saved Items</div>
                </div>
            </div>
        </div>
    </div>
</template>