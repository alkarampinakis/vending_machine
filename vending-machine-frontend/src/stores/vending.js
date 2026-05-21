import { defineStore } from 'pinia'
import { ref } from 'vue'
import api from '../api/axios'
import { useAuthStore } from './auth'

export const useVendingStore = defineStore('vending', () => {
  const lastPurchase = ref(null)
  const loading      = ref(false)
  const error        = ref(null)

  async function depositCoin(amount) {
    loading.value = true
    error.value   = null
    try {
      const { data } = await api.post('/deposit', { amount })
      useAuthStore().updateDeposit(data.deposit)
      return data
    } catch (e) {
      error.value = e.response?.data?.message || 'Deposit failed.'
      throw e
    } finally {
      loading.value = false
    }
  }

  async function buy(productId, amount) {
    loading.value  = true
    error.value    = null
    lastPurchase.value = null
    try {
      const { data } = await api.post('/buy', { productId, amount })
      lastPurchase.value = data
      useAuthStore().updateDeposit(0)
      return data
    } catch (e) {
      error.value = e.response?.data?.message || 'Purchase failed.'
      throw e
    } finally {
      loading.value = false
    }
  }

  async function resetDeposit() {
    loading.value = true
    error.value   = null
    try {
      const { data } = await api.post('/reset')
      useAuthStore().updateDeposit(data.deposit)
    } catch (e) {
      error.value = e.response?.data?.message || 'Reset failed.'
    } finally {
      loading.value = false
    }
  }

  return { lastPurchase, loading, error, depositCoin, buy, resetDeposit }
})
