<template>
  <div v-if="open" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6 flex flex-col gap-4">
      <div class="flex items-center justify-between">
        <h2 class="text-lg font-semibold">Buy {{ product?.productName }}</h2>
        <button @click="$emit('close')" class="text-gray-400 hover:text-gray-600 text-xl">✕</button>
      </div>

      <div v-if="!receipt">
        <p class="text-sm text-gray-500 mb-4">Cost: <strong>{{ product?.cost }}¢</strong> each · Stock: <strong>{{ product?.amountAvailable }}</strong></p>

        <AlertBanner :message="error" class="mb-3" />

        <BaseInput
          v-model.number="qty"
          label="Quantity"
          type="number"
          :placeholder="`max ${product?.amountAvailable}`"
          required
        />

        <div class="flex gap-2 mt-4">
          <BaseButton @click="submit" :loading="loading" class="flex-1">Confirm Purchase</BaseButton>
          <BaseButton variant="secondary" @click="$emit('close')">Cancel</BaseButton>
        </div>
      </div>

      <div v-else class="flex flex-col gap-3">
        <AlertBanner message="Purchase successful!" type="success" />
        <p class="text-sm">Total spent: <strong>{{ receipt.totalSpent }}¢</strong></p>
        <div v-if="Object.keys(receipt.change).length">
          <p class="text-sm font-medium text-gray-700 mb-1">Change returned:</p>
          <div class="flex flex-wrap gap-2">
            <span
              v-for="(count, coin) in receipt.change"
              :key="coin"
              class="bg-yellow-100 text-yellow-800 text-sm px-2 py-1 rounded-full"
            >{{ coin }}¢ × {{ count }}</span>
          </div>
        </div>
        <p v-else class="text-sm text-gray-500">No change.</p>
        <BaseButton @click="$emit('close')" variant="secondary">Close</BaseButton>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue'
import { useVendingStore } from '../../stores/vending'
import BaseButton from '../ui/BaseButton.vue'
import BaseInput from '../ui/BaseInput.vue'
import AlertBanner from '../ui/AlertBanner.vue'

const props = defineProps({ open: Boolean, product: Object })
const emit  = defineEmits(['close', 'purchased'])

const store   = useVendingStore()
const qty     = ref(1)
const loading = ref(false)
const error   = ref(null)
const receipt = ref(null)

watch(() => props.open, (v) => {
  if (v) { qty.value = 1; error.value = null; receipt.value = null }
})

async function submit() {
  loading.value = true
  error.value   = null
  try {
    receipt.value = await store.buy(props.product.id, qty.value)
    emit('purchased', receipt.value)
  } catch (e) {
    error.value = store.error
  } finally {
    loading.value = false
  }
}
</script>
