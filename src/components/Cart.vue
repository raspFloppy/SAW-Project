<script setup>
import { ref } from 'vue';

// Reactive state to manage the cart
const cart = ref(JSON.parse(localStorage.getItem('cart')) || []);

const isCartOpen = ref(false);

// Function to calculate the total number of items in the cart
function getTotalItems() {
  return cart.value.reduce((total, item) => total + item.quantity, 0);
}

// Function to close the cart modal
function closeCart() {
  isCartOpen.value = false;
}

// Function to remove an item from the cart
function removeItem(index) {
  cart.value.splice(index, 1);
  localStorage.setItem('cart', JSON.stringify(cart.value));
}
</script>

<template>
  <!-- Cart Icon with Notification Badge -->
  <div class="fixed bottom-5 right-5">
    <button class="btn btn-circle btn-primary relative" @click="isCartOpen = true">
      <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h18l1 14H4L3 3z" />
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9h12M9 13h6M9 17h6" />
      </svg>
      <!-- Cart Notification Badge -->
      <span v-if="getTotalItems() > 0" class="absolute top-0 right-0 inline-block w-5 h-5 bg-red-500 text-white text-xs rounded-full text-center">
        {{ getTotalItems() }}
      </span>
    </button>
  </div>

  <!-- Cart Modal -->
  <div v-if="isCartOpen" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center">
    <div class="bg-white p-6 rounded-lg shadow-lg w-96">
      <h2 class="text-2xl font-bold mb-4">Your Cart</h2>
      <div v-if="cart.length === 0" class="text-center text-gray-500">Your cart is empty</div>
      <ul v-else>
        <li v-for="(item, index) in cart" :key="item.id" class="flex justify-between items-center mb-4">
          <span>{{ item.title }} ({{ item.quantity }})</span>
          <button @click="removeItem(index)" class="btn btn-sm btn-outline btn-danger">Remove</button>
        </li>
      </ul>
      <div class="flex justify-between items-center mt-4">
        <button @click="closeCart" class="btn btn-outline btn-sm">Close</button>
        <button class="btn btn-primary btn-sm">Checkout</button>
      </div>
    </div>
  </div>
</template>

