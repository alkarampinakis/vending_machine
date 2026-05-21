<template>
  <div class="max-w-5xl mx-auto px-4 py-8 flex flex-col gap-8">

    <!-- Balance + controls -->
    <section class="bg-indigo-50 rounded-2xl p-6 flex flex-col sm:flex-row gap-6 items-start sm:items-center justify-between">
      <div>
        <p class="text-sm text-indigo-500 font-medium">Your Balance</p>
        <p class="text-4xl font-bold text-indigo-700">{{ auth.user?.deposit ?? 0 }}¢</p>
      </div>
      <div class="flex flex-col gap-3 w-full sm:w-auto">
        <CoinSelector />
        <BaseButton variant="secondary" :loading="vendingLoading" @click="resetDeposit" class="text-sm">
          Reset Deposit
        </BaseButton>
      </div>
    </section>

    <!-- Products -->
    <section>
      <h2 class="text-xl font-semibold text-gray-900 mb-4">Available Products</h2>

      <div v-if="productsStore.loading" class="text-gray-500 text-center py-8">Loading...</div>
      <AlertBanner v-else-if="productsStore.error" :message="productsStore.error" />

      <div v-else-if="productsStore.items.length === 0" class="text-gray-400 text-center py-8">
        No products available.
      </div>

      <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <ProductCard v-for="p in productsStore.items" :key="p.id" :product="p">
          <template #actions>
            <BaseButton
              variant="success"
              :disabled="p.amountAvailable === 0"
              @click="openBuy(p)"
              class="w-full text-sm"
            >
              {{ p.amountAvailable === 0 ? 'Out of stock' : 'Buy' }}
            </BaseButton>
          </template>
        </ProductCard>
      </div>
    </section>

    <BuyModal
      :open="buyModal.open"
      :product="buyModal.product"
      @close="buyModal.open = false"
      @purchased="onPurchased"
    />
  </div>
</template>

<script setup>
import { reactive, ref, onMounted } from 'vue'
import { useAuthStore } from '../stores/auth'
import { useProductsStore } from '../stores/products'
import { useVendingStore } from '../stores/vending'
import CoinSelector from '../components/vending/CoinSelector.vue'
import BuyModal from '../components/vending/BuyModal.vue'
import ProductCard from '../components/products/ProductCard.vue'
import BaseButton from '../components/ui/BaseButton.vue'
import AlertBanner from '../components/ui/AlertBanner.vue'

const auth          = useAuthStore()
const productsStore = useProductsStore()
const vendingStore  = useVendingStore()

const vendingLoading = ref(false)

const buyModal = reactive({ open: false, product: null })

onMounted(() => productsStore.fetchProducts())

function openBuy(product) {
  buyModal.product = product
  buyModal.open    = true
}

function onPurchased() {
  productsStore.fetchProducts()
}

async function resetDeposit() {
  vendingLoading.value = true
  await vendingStore.resetDeposit()
  vendingLoading.value = false
}
</script>
