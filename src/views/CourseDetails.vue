<script setup>
import { onMounted, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useCourseStore } from '@/stores/course';

const route = useRoute();
const router = useRouter();
const courseStore = useCourseStore();
const quantity = ref(1);
const cart = ref(JSON.parse(localStorage.getItem('cart')) || []);

onMounted(() => {
  courseStore.fetchCourseById(route.params.id);
});

function increment() {
  quantity.value++;
}

function decrement() {
  if (quantity.value > 1) {
    quantity.value--;
  }
}

function addToCart() {
  const course = courseStore.currentCourse;
  const courseInCart = cart.value.find(item => item.id === course.id);
  
  if (courseInCart) {
    courseInCart.quantity += quantity.value;
  } else {
    cart.value.push({ ...course, quantity: quantity.value });
  }

  localStorage.setItem('cart', JSON.stringify(cart.value));
}

</script>

<template>
  <div v-if="courseStore.loading" class="w-full">Loading course details...</div>
  <div v-else-if="courseStore.error" class="w-full">{{ courseStore.error }}</div>
  <div v-else-if="courseStore.currentCourse" class="hero bg-base-200 min-h-screen">
    <div class="hero-content flex-col lg:flex-row w-full">
      <img src="@/assets/images/HTML_course_image.jpg" class="max-w-sm rounded-lg shadow-2xl" />
      <div class="w-full">
        <h1 class="text-5xl font-bold"> {{ courseStore.currentCourse.title }} <br> ${{ courseStore.currentCourse.price}} </h1>
        <p class="py-6">
          {{ courseStore.currentCourse.description }}
        </p>
        <div class="quantity-container flex items-center mb-4">
          <label class="mr-4 font-bold">Quantity:</label>
          <button class="btn btn-outline btn-sm" @click="decrement">-</button>
          <span class="mx-2">{{ quantity }}</span>
          <button class="btn btn-outline btn-sm" @click="increment">+</button>
        </div>
        <button class="btn btn-primary" @click="addToCart">Buy Now</button>
      </div>
    </div>
  </div>
  <div v-else class="w-full">
    <router-view name="not-found" />
  </div>
</template>
