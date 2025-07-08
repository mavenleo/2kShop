<template>
  <nav class="bg-white border-b border-gray-200 px-8 py-4 flex items-center justify-between">
    <div class="flex items-center gap-8">
      <span class="text-2xl font-bold text-blue-700">2kShop</span>
      <div class="flex gap-6">
        <Link href="/products" class="flex items-center gap-1 text-gray-700 hover:text-blue-700 font-medium">
          <span class="material-icons">shopping_cart</span> Products
        </Link>
        <Link href="/wishlist" class="flex items-center gap-1 text-gray-700 hover:text-blue-700 font-medium">
          <span class="material-icons">favorite_border</span> Wishlist
        </Link>
      </div>
    </div>
    <div class="flex items-center gap-4">
      <span class="text-gray-700 font-medium">{{ user?.name || 'User' }}</span>
      <button @click="$emit('logout')" class="text-gray-500 hover:text-red-600" title="Logout">
        <span class="material-icons">logout</span>
      </button>
    </div>
  </nav>
</template>

<script setup>
import { defineProps, defineEmits } from 'vue'
import { Link } from '@inertiajs/vue3'

defineProps({
  user: Object
})
defineEmits(['logout'])
</script>

<style scoped>
.material-icons {
  font-family: 'Material Icons';
  font-style: normal;
  font-weight: normal;
  font-size: 20px;
  line-height: 1;
  letter-spacing: normal;
  text-transform: none;
  display: inline-block;
  white-space: nowrap;
  direction: ltr;
  -webkit-font-feature-settings: 'liga';
  -webkit-font-smoothing: antialiased;
}
</style>
