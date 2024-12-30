<template>
  <div>

    <div class="navbar bg-base-100">
      <div class="flex-1"></div>
      <div class="flex-none">
        <div class="dropdown dropdown-end">
          <div tabindex="0" role="button" class="btn btn-ghost btn-circle">
            <div class="indicator">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor">
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
              </svg>
              <span class="badge badge-sm indicator-item">{{ cartItems.length }}</span>
            </div>
          </div>
          <div
            tabindex="0"
            class="card card-compact dropdown-content bg-base-100 z-[1] mt-3 w-52 shadow">
            <div class="card-body">
              <span class="text-lg font-bold">{{ cartItems.length }} Items</span>
              <span class="text-info">Subtotal: ${{ cartTotal }}</span>
              <div class="card-actions">
                <button class="btn btn-primary btn-block" @click="showCart = true">
                  View Cart
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

  
    <div class="hero bg-base-200 min-h-screen">
      <div class="hero-content flex-col lg:flex-row">
        <img
          src="@/assets/images/HTML_course_image.jpg"
          class="max-w-sm rounded-lg shadow-2xl" />
        <div>
          <h1 class="text-5xl font-bold">Learn HTML <br> 90$</h1>
          <p class="py-6">
            Begin your web development journey by learning HTML, the standard markup language for creating web pages. With HTML, you can build beautiful websites, design easy-to-use interfaces, and organize the structure and content of web pages. This course will give you everything you need, from learning the material to preparing for your final exam.
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

    <Cart v-if="showCart" :cart="cartItems" @close="showCart = false" />
  </div>
</template>

<script>
import Cart from "@/components/cart.vue";

export default {
  name: "CerHtml",
  components: {
    Cart,
  },
  data() {
    return {
      quantity: 1,
      price: 90,
      cartItems: [],
      showCart: false,
    };
  },
  methods: {
    increment() {
      this.quantity++;
    },
    decrement() {
      if (this.quantity > 1) {
        this.quantity--;
      }
    },
    addToCart() {
      const item = {
        name: "HTML Course",
        price: this.price,
        quantity: this.quantity,
        total: this.quantity * this.price,
      };

      this.cartItems.push(item);
      alert("conferm to add!");
    },
  },
  computed: {
    cartTotal() {
      return this.cartItems.reduce((total, item) => total + item.total, 0);
    },
  },
};
</script>

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
