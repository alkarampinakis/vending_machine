<template>
  <div class="max-w-5xl mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold text-gray-900 mb-6">All Products</h1>

    <div v-if="store.loading" class="text-center text-gray-500 py-12">Loading products...</div>
    <AlertBanner v-else-if="store.error" :message="store.error" />

    <div v-else-if="store.items.length === 0" class="text-center text-gray-400 py-12">
      No products available yet.
    </div>

    <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
      <ProductCard v-for="product in store.items" :key="product.id" :product="product" />
    </div>

    <div v-if="store.pagination.lastPage > 1" class="mt-6 flex justify-center gap-2">
      <BaseButton
        variant="secondary"
        :disabled="store.pagination.currentPage === 1"
        @click="store.fetchProducts(store.pagination.currentPage - 1)"
      >← Prev</BaseButton>
      <span class="self-center text-sm text-gray-600">
        Page {{ store.pagination.currentPage }} / {{ store.pagination.lastPage }}
      </span>
      <BaseButton
        variant="secondary"
        :disabled="store.pagination.currentPage === store.pagination.lastPage"
        @click="store.fetchProducts(store.pagination.currentPage + 1)"
      >Next →</BaseButton>
    </div>
  </div>
</template>

<script setup>
import { onMounted } from 'vue'
import { useProductsStore } from '../stores/products'
import ProductCard from '../components/products/ProductCard.vue'
import BaseButton from '../components/ui/BaseButton.vue'
import AlertBanner from '../components/ui/AlertBanner.vue'

const store = useProductsStore()
onMounted(() => store.fetchProducts())
</script>
