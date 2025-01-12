
<template>
    <div class=" mx-auto p-6 bg-gray-100 min-h-screen">
      <h1 class="text-3xl font-bold mb-6">Checkout <br></h1>

      <div v-if="cart.length > 0" class="space-y-4">
        <div v-for="(item, index) in cart" :key="item.id" class="flex items-center border p-4 rounded-lg">
            <img src="@/assets/images/HTML_course_image.jpg " class="custom-img"  />
            <div class="flex-grow">
            <h2 class="text-xl font-semibold">{{ item.name }}</h2>
            <p>Quantity: {{ item.quantity }}</p>
          </div>
          <div>
            <p class="text-lg font-bold">${{ item.price * item.quantity }}</p>
          </div>
          <button
            @click="removeFromCart(item.id)"
            class="btn btn-error btn-xs ml-4 bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600"
          >
            Remove
          </button>
        </div>
  
        <div class="flex justify-between items-center mt-4">
          <input
            type="text"
            v-model="discountCode"
            placeholder="Discount code or gift card"
            class="border p-2 rounded w-2/3"
          />
          <button @click="applyDiscount" class="btn btn-primary px-6 py-2 ml-4">Apply</button>
        </div>
  
        <div class="text-right mt-6">
          <p class="text-lg font-bold">Total: <span class="text-2xl">${{ cartTotal }}</span></p>
          <p class="text-sm text-gray-500">Including ${{ taxes }} in taxes</p>
        </div>
  
        <form @submit.prevent="handleCheckout" class="mt-6 space-y-4">
          <div>
            <label for="email" class="block text-sm font-medium">Email</label>
            <input
              type="email"
              v-model="form.email"
              id="email"
              required
              class="border p-2 rounded w-full"
              placeholder="Enter your email"
            />
          </div>
          <button type="submit" class="btn btn-primary w-full">Place Order</button>
        </form>
      </div>
      <p v-else>Your cart is empty.</p>
    </div>
  </template>
  
  <script>
  import { reactive, computed } from "vue";
  import { useCart } from "@/stores/useCart";
  
  export default {
    setup() {
      const { cart, removeFromCart, cartTotal } = useCart();
      const form = reactive({
        email: "",
      });
      const discountCode = reactive("");
  
      const taxes = computed(() => (cartTotal.value * 0.1).toFixed(2)); 
  
      function applyDiscount() {
        if (discountCode === "saw24") {
          alert("Discount applied!");
          
        } else {
          alert("Invalid discount code.");
        }
      }
  
      function handleCheckout() {
        alert(`Order placed successfully!`);
        console.log({
          items: cart,
          customer: form,
          total: cartTotal.value,
        });
        localStorage.removeItem("cart");
        location.reload();
      }
  
      return { cart, removeFromCart, cartTotal, form, discountCode, taxes, applyDiscount, handleCheckout };
    },
  };
  </script>
  
  <style scoped>
  .container {
    max-width: 800px;
  }
 
.custom-img {
  width: 150px; /* Imposta la larghezza */
  height: 150px; /* Imposta l'altezza */
  object-fit: cover; /* Mantiene il rapporto d'aspetto e riempie l'area */
  border-radius: 8px; /* Angoli arrotondati */
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* Aggiunge ombra */
}
</style>

 
  