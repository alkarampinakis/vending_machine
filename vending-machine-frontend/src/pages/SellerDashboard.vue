<template>
  <div class="max-w-5xl mx-auto px-4 py-8 flex flex-col gap-8">
    <h1 class="text-2xl font-bold text-gray-900">My Products</h1>

    <AlertBanner :message="actionError" />

    <!-- Create / Edit form -->
    <ProductForm
      :editing-product="editingProduct"
      @saved="onSaved"
      @cancel="editingProduct = null"
    />

    <!-- Product list -->
    <div v-if="store.loading" class="text-gray-500 text-center py-8">Loading...</div>

    <div v-else-if="store.myProducts.length === 0" class="text-gray-400 text-center py-8">
      You haven't listed any products yet. Use the form above to add one.
    </div>

    <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
      <ProductCard v-for="p in store.myProducts" :key="p.id" :product="p">
        <template #actions>
          <BaseButton variant="secondary" @click="editingProduct = p" class="flex-1 text-sm">Edit</BaseButton>
          <BaseButton variant="danger" :loading="deletingId === p.id" @click="deleteProduct(p.id)" class="flex-1 text-sm">Delete</BaseButton>
        </template>
      </ProductCard>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useProductsStore } from '../stores/products'
import ProductCard from '../components/products/ProductCard.vue'
import ProductForm from '../components/products/ProductForm.vue'
import BaseButton from '../components/ui/BaseButton.vue'
import AlertBanner from '../components/ui/AlertBanner.vue'

const store = useProductsStore()

const editingProduct = ref(null)
const deletingId     = ref(null)
const actionError    = ref(null)

onMounted(() => store.fetchProducts())

function onSaved() {
  editingProduct.value = null
}

async function deleteProduct(id) {
  if (!confirm('Delete this product?')) return
  deletingId.value  = id
  actionError.value = null
  try {
    await store.deleteProduct(id)
  } catch (e) {
    actionError.value = e.response?.data?.message || 'Failed to delete.'
  } finally {
    deletingId.value = null
  }
}
</script>
