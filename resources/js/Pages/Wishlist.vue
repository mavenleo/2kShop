<template>
  <div class="min-h-screen bg-gray-50">
    <TopNav :user="user" @logout="logout" />
    <div class="max-w-7xl mx-auto px-4 py-8">
      <h1 class="text-3xl font-bold mb-2">My Wishlist</h1>
      <p class="text-gray-500 mb-8">You have {{ wishlist.length }} item{{ wishlist.length === 1 ? '' : 's' }} in your wishlist</p>
      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        <div v-for="item in wishlist" :key="item.product.id" class="bg-white rounded-xl shadow p-6 flex flex-col">
          <div class="font-bold text-lg mb-1">{{ item.product.name }}</div>
          <div class="text-gray-500 text-sm mb-2">{{ item.product.description }}</div>
          <div class="text-blue-600 font-bold text-xl mb-4">${{ item.product.price }}</div>
          <button
            @click="removeFromWishlist(item.product.id)"
            class="w-full py-2 bg-blue-600 text-white rounded font-semibold flex items-center justify-center gap-2"
          >
            <span>♥</span> Remove from Wishlist
          </button>
        </div>
      </div>
      <div class="flex justify-center mt-8 gap-2">
        <button
          @click="goToPage(currentPage - 1)"
          :disabled="currentPage === 1"
          class="px-3 py-1 rounded border bg-white text-gray-700 disabled:opacity-50"
        >Prev</button>
        <span class="px-3 py-1">{{ currentPage }} / {{ lastPage }}</span>
        <button
          @click="goToPage(currentPage + 1)"
          :disabled="currentPage === lastPage"
          class="px-3 py-1 rounded border bg-white text-gray-700 disabled:opacity-50"
        >Next</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import { usePage } from '@inertiajs/vue3'
import { Inertia } from '@inertiajs/inertia'
import TopNav from '@/components/TopNav.vue'

const user = usePage().props.user
const wishlist = ref([])
const currentPage = ref(1)
const lastPage = ref(1)

const fetchWishlist = async (page = 1) => {
  const res = await axios.get('/api/v1/wishlist', { params: { page } })
  wishlist.value = res.data.data.map(item => ({ product: item.product }))
  currentPage.value = res.data.pagination.current_page
  lastPage.value = res.data.pagination.last_page
}

onMounted(() => {
  fetchWishlist()
})

const removeFromWishlist = async (productId) => {
  await axios.delete('/api/v1/wishlist', { data: { product_id: productId } })
  wishlist.value = wishlist.value.filter(item => item.product.id !== productId)
}

const logout = () => {
  axios.post('/api/v1/auth/logout').then(() => {
    Inertia.visit('/')
  })
}

const goToPage = (page) => {
  if (page >= 1 && page <= lastPage.value) {
    fetchWishlist(page)
  }
}
</script>
