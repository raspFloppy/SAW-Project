<script>
import { useAuthStore } from '@/stores/auth'
import LogoutButton from '@/components/LogoutButton.vue'

export default {
    name: 'Avatar',
    components: {
        LogoutButton
    },
    setup() {
        const authStore = useAuthStore()
        return { authStore }
    }
}

</script>

<template>
    <div class="flex-none gap-2">
        <div class="dropdown dropdown-end">
            <label tabindex="0" class="btn btn-ghost btn-circle avatar">
                <div class="w-10 rounded-full">
                    <img :src="`https://api.dicebear.com/7.x/initials/svg?seed=${authStore.user.firstname[0] + authStore.user.lastname[0] || 'U'}`"
                        alt="avatar" />
                </div>
            </label>
            <ul tabindex="0" class="mt-3 z-[1] p-2 shadow menu menu-sm dropdown-content bg-base-100 rounded-box w-52">
                <li>
                    <LogoutButton />
                </li>
            </ul>
        </div>
    </div>
</template>