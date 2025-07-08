<template>
  <div class="min-h-screen bg-gray-50">
    <TopNav :user="user" @logout="logout" />
    <div class="max-w-7xl mx-auto px-4 py-8">
      <h1 class="text-3xl font-bold mb-2">All Products</h1>
      <p class="text-gray-500 mb-8">Discover amazing products and add them to your wishlist</p>
      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        <div v-for="product in products" :key="product.id" class="bg-white rounded-xl shadow p-6 flex flex-col">
          <div class="font-bold text-lg mb-1">{{ product.name }}</div>
          <div class="text-gray-500 text-sm mb-2">{{ product.description }}</div>
          <div class="text-blue-600 font-bold text-xl mb-4">${{ product.price }}</div>
          <button
            @click="toggleWishlist(product)"
            :class="product.is_wishlisted ? 'bg-blue-600 text-white' : 'border border-gray-300'"
            class="w-full py-2 rounded font-semibold flex items-center justify-center gap-2 transition"
          >
            <span>{{ product.is_wishlisted ? '♥' : '♡' }}</span>
            {{ product.is_wishlisted ? 'Remove from Wishlist' : 'Add to Wishlist' }}
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
import { usePage, router } from '@inertiajs/vue3'
import { Inertia } from '@inertiajs/inertia'
import TopNav from '@/components/TopNav.vue'

const products = ref([])
const loading = ref(true)
const user = usePage().props.user

const currentPage = ref(1)
const lastPage = ref(1)

const fetchProducts = async (page = 1) => {
  loading.value = true
  const response = await axios.get('/api/v1/products', { params: { page } })

  products.value = response.data.data
  currentPage.value = response.data?.meta?.current_page
  lastPage.value = response.data?.meta?.last_page
  loading.value = false
}

onMounted(() => {
  fetchProducts()
})

const toggleWishlist = async (product) => {
  if (product.is_wishlisted) {
    await axios.delete('/api/v1/wishlist', { data: { product_id: product.id } })
    product.is_wishlisted = false
  } else {
    await axios.post('/api/v1/wishlist', { product_id: product.id })
    product.is_wishlisted = true
  }
}

const goToPage = (page) => {
  if (page >= 1 && page <= lastPage.value) {
    fetchProducts(page)
  }
}

const logout = () => {
  axios.post('/api/v1/auth/logout').then(() => {
      router.visit('/')
  })
}
</script>

<style>
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
