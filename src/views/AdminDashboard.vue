<script setup>
import { ref, onMounted, watch, computed } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { useArticleStore } from '@/stores/articles'
import { useAdminStore } from '@/stores/admin'
import Navbar from '@/components/Navbar.vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const auth = useAuthStore()
const adminStore = useAdminStore()
const articleStore = useArticleStore()
const activeTab = ref('dashboard')
const users = ref([])
const articles = ref([])
const stats = ref({
    totalUsers: 0,
    totalArticles: 0,
    totalLikes: 0,
    totalDislikes: 0,
    totalComments: 0
})
const selectedArticle = ref({
    id: null,
    title: '',
    content: '',
    author: '',
    created_at: ''
})
const userSortConfig = ref({
    key: 'firstname',
    direction: 'asc'
})
const articleSortConfig = ref({
    key: 'title',
    direction: 'asc'
})

const sortedUsers = computed(() => {
    const sortedArray = [...users.value]
    return sortedArray.sort((a, b) => {
        let aVal = a[userSortConfig.value.key]
        let bVal = b[userSortConfig.value.key]

        if (userSortConfig.value.key === 'created_at') {
            aVal = new Date(aVal)
            bVal = new Date(bVal)
        }

        if (['comments', 'favorites', 'dislikes'].includes(userSortConfig.value.key)) {
            aVal = Number(aVal)
            bVal = Number(bVal)
        }

        if (aVal < bVal) return userSortConfig.value.direction === 'asc' ? -1 : 1
        if (aVal > bVal) return userSortConfig.value.direction === 'asc' ? 1 : -1
        return 0
    })
})

const sortedArticles = computed(() => {
    const sortedArray = [...articleStore.articles]
    return sortedArray.sort((a, b) => {
        let aVal = a[articleSortConfig.value.key]
        let bVal = b[articleSortConfig.value.key]

        if (articleSortConfig.value.key === 'created_at') {
            aVal = new Date(aVal)
            bVal = new Date(bVal)
        }

        if (aVal < bVal) return articleSortConfig.value.direction === 'asc' ? -1 : 1
        if (aVal > bVal) return articleSortConfig.value.direction === 'asc' ? 1 : -1
        return 0
    })
})

function toggleUserSort(key) {
    if (userSortConfig.value.key === key) {
        userSortConfig.value.direction = userSortConfig.value.direction === 'asc' ? 'desc' : 'asc'
    } else {
        userSortConfig.value.key = key
        userSortConfig.value.direction = 'asc'
    }
}

function toggleArticleSort(key) {
    if (articleSortConfig.value.key === key) {
        articleSortConfig.value.direction = articleSortConfig.value.direction === 'asc' ? 'desc' : 'asc'
    } else {
        articleSortConfig.value.key = key
        articleSortConfig.value.direction = 'asc'
    }
}

async function loadData() {
    try {
        await articleStore.fetchArticles(1)
        await adminStore.getAllUsers()
        articles.value = articleStore.articles
        users.value = adminStore.users

        stats.value = {
            totalArticles: articleStore.total,
            totalUsers: adminStore.totalUsers,
            totalLikes: adminStore.totalLikes,
            totalDislikes: adminStore.totalDislikes,
        }
    } catch (error) {
        console.error('Failed to load data:', error)
    }
}

function editArticle(article) {
    selectedArticle.value = { ...article }
    activeTab.value = 'editor'
}

async function saveArticle() {
    try {
        if (selectedArticle.value.id) {
            await adminStore.updateArticle(
                selectedArticle.value.id,
                selectedArticle.value.title,
                selectedArticle.value.content,
                selectedArticle.value.author
            )
        } else {
            await articleStore.writeArticle(
                selectedArticle.value.title,
                selectedArticle.value.content,
                selectedArticle.value.author
            )
        }

        activeTab.value = 'dashboard'
        await loadData()
    } catch (error) {
        console.error('Failed to save article:', error)
    }
}

async function deleteArticle(articleId) {
    if (confirm('Are you sure you want to delete this article?')) {
        try {
            await adminStore.deleteArticle(articleId)
            await loadData()
        } catch (error) {
            console.error('Failed to delete article:', error)
        }
    }
}

async function deleteUser(userId) {
    if (confirm('Are you sure you want to delete this user?')) {
        try {
            await adminStore.deleteUser(userId)
            await loadData()
        } catch (error) {
            console.error('Failed to delete user:', error)
        }
    }
}

async function changePage(page) {
    await articleStore.fetchArticles(page)
}

async function cancelEdit() {
    selectedArticle.value = {
        id: null,
        title: '',
        content: '',
        author: '',
        created_at: ''
    }

    activeTab.value = 'dashboard'
}

async function handleRoleChange(userId, event) {
    const newRole = event.target.value;
    try {
        if (confirm('Are you sure you want to change this user\'s role?')) {
            await adminStore.changeUserRole(userId, newRole);
            await loadData();
        }
        return
    } catch (error) {
        console.error('Failed to change user role:', error);
    }
}

watch(
    () => auth.$state,
    (newState) => {
        articleStore.fetchArticles(articleStore.currentPage);
    },
    { immediate: true }
)

onMounted(async () => {
    await auth.validateSession()
    await articleStore.fetchArticles(1)
    await adminStore.getAllUsers();
    await adminStore.getAllDislikes();
    await adminStore.getAllLikes();

    if (!auth.isAdmin) {
        router.push('/dashboard')
        return
    }
    await loadData()
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

        <div class="p-4 grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="stat bg-base-100 shadow-xl rounded-box">
                <div class="stat-title">Total Articles</div>
                <div class="stat-value">{{ stats.totalArticles }}</div>
            </div>
            <div class="stat bg-base-100 shadow-xl rounded-box">
                <div class="stat-title">Total Users</div>
                <div class="stat-value text-success">{{ stats.totalUsers }}</div>
            </div>
            <div class="stat bg-base-100 shadow-xl rounded-box">
                <div class="stat-title">Total Likes</div>
                <div class="stat-value text-error">{{ stats.totalLikes }}</div>
            </div>
            <div class="stat bg-base-100 shadow-xl rounded-box">
                <div class="stat-title">Total Dislikes</div>
                <div class="stat-value">{{ stats.totalDislikes }}</div>
            </div>
        </div>

        <div class="tabs tabs-boxed justify-center m-4">
            <a class="tab" :class="{ 'tab-active': activeTab === 'users' }" @click="activeTab = 'users'">Users</a>
            <a class="tab" :class="{ 'tab-active': activeTab === 'dashboard' }"
                @click="activeTab = 'dashboard'">Articles</a>
            <a class="tab" :class="{ 'tab-active': activeTab === 'editor' }" @click="activeTab = 'editor'">Article
                Editor</a>
        </div>

        <div v-if="activeTab === 'dashboard'" class="px-4">
            <div class="overflow-x-auto">
                <h2 class="text-2xl font-bold mb-4">Articles Management</h2>
                <table class="table w-full bg-base-100 rounded-box">
                    <thead>
                        <tr>
                            <th @click="toggleArticleSort('title')" class="cursor-pointer hover:bg-base-200">
                                Title
                                <span v-if="articleSortConfig.key === 'title'">
                                    {{ articleSortConfig.direction === 'asc' ? '↑' : '↓' }}
                                </span>
                            </th>
                            <th @click="toggleArticleSort('author')" class="cursor-pointer hover:bg-base-200">
                                Author
                                <span v-if="articleSortConfig.key === 'author'">
                                    {{ articleSortConfig.direction === 'asc' ? '↑' : '↓' }}
                                </span>
                            </th>
                            <th @click="toggleArticleSort('created_at')" class="cursor-pointer hover:bg-base-200">
                                Created At
                                <span v-if="articleSortConfig.key === 'created_at'">
                                    {{ articleSortConfig.direction === 'asc' ? '↑' : '↓' }}
                                </span>
                            </th>
                            <th>Likes</th>
                            <th>Dislike</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="article in sortedArticles" :key="article.id">
                            <td>{{ article.title }}</td>
                            <td>{{ article.author }}</td>
                            <td>{{ article.created_at }}</td>
                            <td>{{ article.favorite_count }}</td>
                            <td>{{ article.dislikes }}</td>
                            <td class="flex gap-2">
                                <button class="btn btn-primary btn-sm" @click="editArticle(article)">
                                    Edit
                                </button>
                                <button class="btn btn-error btn-sm" @click="deleteArticle(article.id)">
                                    Delete
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <div class="flex justify-center mt-4">
                    <div class="join">
                        <button class="join-item btn" :disabled="articleStore.currentPage === 1"
                            @click="changePage(articleStore.currentPage - 1)">
                            «
                        </button>
                        <button class="join-item btn">Page {{ articleStore.currentPage }}</button>
                        <button class="join-item btn" :disabled="articleStore.currentPage === articleStore.totalPages"
                            @click="changePage(articleStore.currentPage + 1)">
                            »
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="activeTab === 'users'" class="px-4 pb-4">
            <div class="overflow-x-auto">
                <h2 class="text-2xl font-bold mb-4">Users Management</h2>
                <table class="table w-full bg-base-100 rounded-box">
                    <thead>
                        <tr>
                            <th @click="toggleUserSort('firstname')" class="cursor-pointer hover:bg-base-200">
                                Firstname
                                <span v-if="userSortConfig.key === 'firstname'">
                                    {{ userSortConfig.direction === 'asc' ? '↑' : '↓' }}
                                </span>
                            </th>
                            <th @click="toggleUserSort('lastname')" class="cursor-pointer hover:bg-base-200">
                                Lastname
                                <span v-if="userSortConfig.key === 'lastname'">
                                    {{ userSortConfig.direction === 'asc' ? '↑' : '↓' }}
                                </span>
                            </th>
                            <th>Email</th>
                            <th @click="toggleUserSort('type')" class="cursor-pointer hover:bg-base-200">
                                Type
                                <span v-if="userSortConfig.key === 'type'">
                                    {{ userSortConfig.direction === 'asc' ? '↑' : '↓' }}
                                </span>
                            </th>
                            <th @click="toggleUserSort('comments')" class="cursor-pointer hover:bg-base-200">
                                Comments
                                <span v-if="userSortConfig.key === 'comments'">
                                    {{ userSortConfig.direction === 'asc' ? '↑' : '↓' }}
                                </span>
                            </th>
                            <th @click="toggleUserSort('favorites')" class="cursor-pointer hover:bg-base-200">
                                Likes
                                <span v-if="userSortConfig.key === 'favorites'">
                                    {{ userSortConfig.direction === 'asc' ? '↑' : '↓' }}
                                </span>
                            </th>
                            <th @click="toggleUserSort('dislikes')" class="cursor-pointer hover:bg-base-200">
                                Dislike
                                <span v-if="userSortConfig.key === 'dislikes'">
                                    {{ userSortConfig.direction === 'asc' ? '↑' : '↓' }}
                                </span>
                            </th>
                            <th @click="toggleUserSort('created_at')" class="cursor-pointer hover:bg-base-200">
                                Joined Date
                                <span v-if="userSortConfig.key === 'created_at'">
                                    {{ userSortConfig.direction === 'asc' ? '↑' : '↓' }}
                                </span>
                            </th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="user in sortedUsers" :key="user.id">
                            <td>{{ user.firstname }}</td>
                            <td>{{ user.lastname }}</td>
                            <td>{{ user.email }}</td>
                            <td>{{ user.type }}</td>
                            <td>{{ user.comments }}</td>
                            <td>{{ user.favorites }}</td>
                            <td>{{ user.dislikes }}</td>
                            <td>{{ user.created_at }}</td>
                            <td>
                                <select class="select select-primary w-full max-w-xs" :value="user.type"
                                    @change="handleRoleChange(user.id, $event)">
                                    <option value="normal">normal</option>
                                    <option value="admin">admin</option>
                                </select>
                            </td>
                            <td>
                                <button class="btn btn-error btn-sm" @click="deleteUser(user.id)">
                                    Delete
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div v-if="activeTab === 'editor'" class="p-4">
            <div class="max-w-4xl mx-auto bg-base-100 rounded-box p-6 shadow-xl">
                <h2 class="text-2xl font-bold mb-4">
                    {{ selectedArticle.id ? 'Edit Article' : 'New Article' }}
                </h2>

                <form class="space-y-4">
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Title</span>
                        </label>
                        <input type="text" v-model="selectedArticle.title" class="input input-bordered w-full" />
                    </div>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Content</span>
                        </label>
                        <textarea v-model="selectedArticle.content" class="textarea textarea-bordered h-64" />
                    </div>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Author</span>
                        </label>
                        <input type="text" v-model="selectedArticle.author" class="input input-bordered w-full" />
                    </div>

                    <div class="flex justify-end gap-2">
                        <button class="btn text-red-500" @click.prevent="cancelEdit()">
                            Cancel
                        </button>
                        <button class="btn btn-primary" @click.prevent="saveArticle">
                            {{ selectedArticle.id ? 'Update Article' : 'Publish Article' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>