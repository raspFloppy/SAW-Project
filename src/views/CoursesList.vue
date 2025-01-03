<script setup>
    import { onMounted } from 'vue';
    import { useRouter } from 'vue-router';
    import { useCourseStore } from '@/stores/course';
    import backgroundImage from '@/assets/images/background.jpg';
    import htmlImage from '@/assets/images/html2.png';
    import javascriptImage from '@/assets/images/java.jpeg';
    import cssImage from '@/assets/images/css.png';

    const router = useRouter();
    const courseStore = useCourseStore();
    
    onMounted(() => {
      courseStore.fetchCourses();
    });

    function goToCourse(id) {
        router.push(`/course/${id}`);
    }
</script>

<template>
    <div class="container mx-auto p-4">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div v-for="course in courseStore.courses" :key="course.id" class="card shadow-lg">
                <div class="card card-compact bg-base-100 w-96 shadow-xl">
                    <figure>
                        <img :src="cssImage" alt="css" class="image-size" />
                    </figure>
                    <div class="card-body">
                        <h2 class="card-title">{{ course.title }}</h2>
                        <p> {{course.subtitle }} </p>
                        <div class="card-actions justify-end">
                        <button class="btn btn-primary" @click="goToCourse(course.id)">View Course</button>
                    </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>