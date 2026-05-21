<template>
  <div class="flex flex-col gap-3">
    <p class="text-sm font-medium text-gray-700">Insert coin:</p>
    <div class="flex flex-wrap gap-2">
      <BaseButton
        v-for="coin in coins"
        :key="coin"
        variant="coin"
        :loading="loading && activeCoin === coin"
        :disabled="loading"
        @click="deposit(coin)"
        class="w-16 h-16 rounded-full text-lg"
      >{{ coin }}¢</BaseButton>
    </div>
    <AlertBanner :message="error" />
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useVendingStore } from '../../stores/vending'
import BaseButton from '../ui/BaseButton.vue'
import AlertBanner from '../ui/AlertBanner.vue'

const store      = useVendingStore()
const activeCoin = ref(null)
const loading    = ref(false)
const error      = ref(null)

const coins = [5, 10, 20, 50, 100]

async function deposit(coin) {
  activeCoin.value = coin
  loading.value    = true
  error.value      = null
  try {
    await store.depositCoin(coin)
  } catch (e) {
    error.value = store.error
  } finally {
    loading.value    = false
    activeCoin.value = null
  }
}
</script>
