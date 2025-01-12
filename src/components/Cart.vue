<template>
    <div class="dropdown dropdown-end">
      <div tabindex="0" role="button" class="btn btn-ghost btn-circle">
        <div class="indicator">
          <svg
            class="h-5 w-5 text-gray-600"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor">
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
          </svg>
          <span class="badge badge-sm indicator-item bg-red-500 text-white">{{ cart.length }}</span>
        </div>
      </div>
      <div
        tabindex="0"
        class="card card-compact dropdown-content bg-white border border-gray-200 rounded-lg shadow-lg mt-3 w-60">
        <div class="card-body p-4">
          <template v-if="cart.length > 0">
            <ul class="divide-y divide-gray-300">
              <li v-for="(item, index) in cart" :key="item.id" class="py-2 flex justify-between items-center">
                <div>
                  <p class="text-sm font-medium text-gray-700">{{ item.name }}</p>
                  <p class="text-xs text-gray-500">Quantity: {{ item.quantity }}</p>
                </div>
                <button
                  class="btn btn-error btn-xs bg-red-500 text-white hover:bg-red-600 px-2 py-1 rounded"
                  @click="removeFromCart(item.id)">Remove</button>
              </li>
            </ul>
            <div class="mt-3">
              <p class="text-right text-sm font-semibold text-gray-700">Total: ${{ cartTotal }}</p>
            </div>
            <button @click="$router.push('/checkout')" class="btn btn-primary w-full mt-4">
              Proceed to Checkout
            </button>
          </template>
          <p v-else class="text-center text-gray-500">Your cart is empty.</p>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  import { useCart } from '@/stores/useCart';
  
  export default {
    setup() {
      const { cart, removeFromCart, cartTotal } = useCart();
  
      return { cart, removeFromCart, cartTotal };
    },
  };
  </script>
  
  <style scoped>
  .indicator {
    display: flex;
    align-items: center;
    justify-content: center;
  }
  
  .card {
    background-color: #ffffff;
    border-radius: 0.5rem;
    overflow: hidden;
  }
  
  .card-body {
    padding: 1rem;
  }
  
  .badge {
    font-size: 0.75rem;
    font-weight: bold;
    padding: 0.25rem 0.5rem;
    border-radius: 9999px;
  }
  
  .btn {
    font-size: 0.75rem;
    font-weight: 500;
  }
  </style>
  