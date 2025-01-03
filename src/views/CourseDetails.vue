<script setup>
import { onMounted, ref } from 'vue';
import { useRoute } from 'vue-router';
import { useCourseStore } from '@/stores/course';
import { useCart } from '@/stores/useCart';

const route = useRoute();
const courseStore = useCourseStore();
const { addToCart } = useCart();
const quantity = ref(1);

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

function handleAddToCart() {
  const course = courseStore.currentCourse;
  addToCart({
    id: course.id,
    name: course.title,
    price: course.price,
    quantity: quantity.value,
  });
  alert(`${course.title} aggiunto al carrello!`);
}
</script>

<template>
  <div v-if="courseStore.loading" class="w-full">Loading course details...</div>
  <div v-else-if="courseStore.error" class="w-full">{{ courseStore.error }}</div>
  <div v-else-if="courseStore.currentCourse" class="hero bg-base-200 min-h-screen">
    <div class="hero-content flex-col lg:flex-row w-full">
      <img src="@/assets/images/HTML_course_image.jpg" class="max-w-sm rounded-lg shadow-2xl" />
      <div class="w-full">
        <h1 class="text-5xl font-bold"> {{ courseStore.currentCourse.title }} <br> ${{ courseStore.currentCourse.price }} </h1>
        <p class="py-6">
          {{ courseStore.currentCourse.description }}
        </p>
        <div class="quantity-container flex items-center mb-4">
          <label class="mr-4 font-bold">Quantity:</label>
          <button class="btn btn-outline btn-sm" @click="decrement">-</button>
          <span class="mx-2">{{ quantity }}</span>
          <button class="btn btn-outline btn-sm" @click="increment">+</button>
        </div>
        <button class="btn btn-primary" @click="handleAddToCart">Buy Now</button>
      </div>
    </div>
  </div>
  <div v-else class="w-full">
    <router-view name="not-found" />
  </div>
</template>

<style scoped>
.quantity-container {
  display: flex;
  align-items: center;
}

.quantity-container span {
  font-size: 1.25rem;
  font-weight: bold;
}
</style>
