import { ref, computed } from 'vue';

const cart = ref(JSON.parse(localStorage.getItem('cart')) || []);

export function useCart() {
  function addToCart(item) {
    const existingItem = cart.value.find(cartItem => cartItem.id === item.id);
    if (existingItem) {
      existingItem.quantity += item.quantity;
    } else {
      cart.value.push(item);
    }
    localStorage.setItem('cart', JSON.stringify(cart.value));
  }

  function removeFromCart(itemId) {
    const index = cart.value.findIndex(item => item.id === itemId);
    if (index !== -1) {
      cart.value.splice(index, 1);
      localStorage.setItem('cart', JSON.stringify(cart.value));
    }
  }

  const cartTotal = computed(() =>
    cart.value.reduce((total, item) => total + item.price * item.quantity, 0)
  );

  return { cart, addToCart, removeFromCart, cartTotal };
}
