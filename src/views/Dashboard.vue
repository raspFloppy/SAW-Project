<script setup>
import { ref, computed, onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { useRouter } from 'vue-router'

const router = useRouter()
const auth = useAuthStore()
const isLoading = ref(false)
const alert = ref({ show: false, type: '', message: '' })

const user = computed(() => auth.user)

const formData = ref({
    firstname: '',
    lastname: '',
    email: ''
})

const initializeForm = () => {
    if (user.value) {
        formData.value = {
            firstname: user.value.firstname,
            lastname: user.value.lastname,
            email: user.value.email
        }
    }
}

const resetForm = () => {
    initializeForm()
}

const showAlert = (type, message) => {
    alert.value = {
        show: true,
        type: type === 'success' ? 'alert-success' : 'alert-error',
        message
    }
    setTimeout(() => {
        alert.value.show = false
    }, 5000)
}

const handleUpdateProfile = async () => {
    isLoading.value = true
    try {
        const result = await auth.updateProfile(
            formData.value.firstname,
            formData.value.lastname,
            formData.value.email
        )

        if (result.success) {
            showAlert('success', 'Profile updated successfully')
        } else {
            showAlert('error', result.message)
        }
    } catch (error) {
        showAlert('error', 'Failed to update profile')
    } finally {
        isLoading.value = false
    }
}

const logout = async () => {
    await auth.logout()
    router.push('/login')
}

onMounted(() => {
    if (!auth.isLoggedIn) {
        router.push('/login')
    } else {
        initializeForm()
    }
})
</script>

<template>
    <div class="min-h-screen bg-base-200">
        <div class="navbar bg-base-100 shadow-lg">
            <div class="flex-1">
                <a class="btn btn-ghost normal-case text-xl">User Dashboard</a>
            </div>
            <div class="flex-none gap-2">
                <div class="dropdown dropdown-end">
                    <label tabindex="0" class="btn btn-ghost btn-circle avatar">
                        <div class="w-10 rounded-full">
                            <img :src="`https://api.dicebear.com/7.x/initials/svg?seed=${user?.firstname || 'U'}`"
                                alt="avatar" />
                        </div>
                    </label>
                    <ul tabindex="0"
                        class="mt-3 z-[1] p-2 shadow menu menu-sm dropdown-content bg-base-100 rounded-box w-52">
                        <li><a @click="logout">Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="container mx-auto px-4 py-8">
            <div v-if="alert.show" :class="`alert ${alert.type} mb-4`">
                <span>{{ alert.message }}</span>
            </div>

            <div class="card bg-base-100 shadow-xl mb-8">
                <div class="card-body">
                    <h2 class="card-title text-2xl">Welcome back, {{ user?.firstname }} {{ user?.lastname }}!</h2>
                    <p class="text-base-content/70">Here's your personal dashboard</p>
                </div>
            </div>

            <div class="card bg-base-100 shadow-xl mb-8">
                <div class="card-body">
                    <h2 class="card-title mb-4">Update Profile</h2>
                    <form @submit.prevent="handleUpdateProfile" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">First Name</span>
                                </label>
                                <input type="text" v-model="formData.firstname" class="input input-bordered" required />
                            </div>

                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">Last Name</span>
                                </label>
                                <input type="text" v-model="formData.lastname" class="input input-bordered" required />
                            </div>

                            <div class="form-control md:col-span-2">
                                <label class="label">
                                    <span class="label-text">Email</span>
                                </label>
                                <input type="email" v-model="formData.email" class="input input-bordered" required />
                            </div>
                        </div>

                        <div class="flex justify-end gap-2">
                            <button type="button" class="btn btn-ghost" @click="resetForm">
                                Reset
                            </button>
                            <button type="submit" class="btn btn-primary" :disabled="isLoading">
                                {{ isLoading ? 'Updating...' : 'Update Profile' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card bg-base-100 shadow-xl">
                <div class="card-body">
                    <h2 class="card-title mb-4">Current Profile Details</h2>
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
                </div>
            </div>
        </div>
    </div>
</template>