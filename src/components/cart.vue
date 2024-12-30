<template>
  <aside class="cart-container">
  
    <div class="cart-header flex justify-between items-center mb-4">
      <h2 class="text-2xl font-bold text-gray-700">Your Cart</h2>
      <button class="btn btn-sm btn-error" @click="$emit('close')">Close</button>
    </div>


    <div v-if="cart.length === 0" class="mt-4 text-center text-gray-500">
      <p>Your cart is empty. Add some courses!</p>
    </div>

   
    <ul v-else>
      <li v-for="(item, index) in cart" :key="index" class="cart-item flex justify-between items-center mb-4">
        <div>
          <h3 class="font-bold text-lg text-gray-800">{{ item.name }}</h3>
          <p class="text-sm text-gray-500">Quantity: {{ item.quantity }}</p>
          <p class="text-sm text-gray-500">Total: ${{ item.total }}</p>
        </div>
        <button class="btn btn-sm btn-outline btn-error" @click="removeItem(index)">Remove</button>
      </li>
    </ul>

    
    <div v-if="cart.length > 0" class="cart-total mt-6">
      <h3 class="text-xl font-bold text-gray-800">Total: ${{ cartTotal }}</h3>
      <button class="btn btn-success w-full mt-4">Checkout</button>
    </div>
  </aside>
</template>

<script>
export default {
  name: "Cart",
  props: {
    cart: {
      type: Array,
      required: true,
    },
  },
  methods: {
   
    removeItem(index) {
      this.cart.splice(index, 1);
      this.$emit("update-cart", this.cart);
    },
  },
  computed: {
    cartTotal() {
      return this.cart.reduce((total, item) => total + item.total, 0);
    },
  },
};
</script>

<style scoped>
.cart-container {
  position: fixed;
  right: 20px;
  top: 80px;
  width: 350px;
  background: #f9f9f9;
  padding: 20px;
  border: 1px solid #ddd;
  border-radius: 10px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.cart-item {
  background: #ffffff;
  padding: 10px 15px;
  border: 1px solid #e5e5e5;
  border-radius: 8px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.cart-item:hover {
  background: #f1f1f1;
}

.cart-total {
  text-align: center;
  font-size: 1.25rem;
  font-weight: bold;
}

.cart-header {
  border-bottom: 1px solid #e5e5e5;
  padding-bottom: 10px;
  margin-bottom: 10px;
}
</style>
