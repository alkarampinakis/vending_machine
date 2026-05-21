import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import api from '../api/axios'
import { useAuthStore } from './auth'

export const useProductsStore = defineStore('products', () => {
  const items       = ref([])
  const loading     = ref(false)
  const error       = ref(null)
  const pagination  = ref({ currentPage: 1, lastPage: 1, total: 0 })

  const myProducts = computed(() => {
    const authStore = useAuthStore()
    return items.value.filter(p => p.sellerId === authStore.user?.id)
  })

  async function fetchProducts(page = 1) {
    loading.value = true
    error.value   = null
    try {
      const { data } = await api.get(`/products?page=${page}`)
      items.value = data.data
      pagination.value = { currentPage: data.current_page, lastPage: data.last_page, total: data.total }
    } catch (e) {
      error.value = e.response?.data?.message || 'Failed to load products.'
    } finally {
      loading.value = false
    }
  }

  async function createProduct(payload) {
    const { data } = await api.post('/products', payload)
    items.value.unshift(data)
    return data
  }

  async function updateProduct(id, payload) {
    const { data } = await api.put(`/products/${id}`, payload)
    const idx = items.value.findIndex(p => p.id === id)
    if (idx !== -1) items.value[idx] = data
    return data
  }

  async function deleteProduct(id) {
    await api.delete(`/products/${id}`)
    items.value = items.value.filter(p => p.id !== id)
  }

  return { items, loading, error, pagination, myProducts,
           fetchProducts, createProduct, updateProduct, deleteProduct }
})
