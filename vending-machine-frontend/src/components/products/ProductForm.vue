<template>
  <form @submit.prevent="submit" class="bg-white rounded-xl border border-gray-200 p-6 flex flex-col gap-4">
    <h2 class="text-lg font-semibold text-gray-900">{{ editingProduct ? 'Edit Product' : 'Add Product' }}</h2>

    <AlertBanner :message="error" />

    <BaseInput v-model="form.productName" label="Product Name" placeholder="e.g. Cola" required :error="fieldErrors.productName" />
    <BaseInput v-model.number="form.cost" label="Cost (cents, multiples of 5)" type="number" placeholder="e.g. 65" required :error="fieldErrors.cost" />
    <BaseInput v-model.number="form.amountAvailable" label="Amount Available" type="number" placeholder="e.g. 10" required :error="fieldErrors.amountAvailable" />

    <div class="flex gap-2">
      <BaseButton type="submit" :loading="loading">{{ editingProduct ? 'Save Changes' : 'Create Product' }}</BaseButton>
      <BaseButton v-if="editingProduct" variant="secondary" @click="$emit('cancel')">Cancel</BaseButton>
    </div>
  </form>
</template>

<script setup>
import { ref, watch } from 'vue'
import { useProductsStore } from '../../stores/products'
import BaseInput from '../ui/BaseInput.vue'
import BaseButton from '../ui/BaseButton.vue'
import AlertBanner from '../ui/AlertBanner.vue'

const props = defineProps({ editingProduct: { type: Object, default: null } })
const emit  = defineEmits(['saved', 'cancel'])

const store  = useProductsStore()
const loading = ref(false)
const error   = ref(null)
const fieldErrors = ref({})

const form = ref({ productName: '', cost: '', amountAvailable: '' })

watch(() => props.editingProduct, (p) => {
  if (p) form.value = { productName: p.productName, cost: p.cost, amountAvailable: p.amountAvailable }
  else   form.value = { productName: '', cost: '', amountAvailable: '' }
}, { immediate: true })

async function submit() {
  loading.value    = true
  error.value      = null
  fieldErrors.value = {}
  try {
    const payload = { ...form.value, cost: Number(form.value.cost), amountAvailable: Number(form.value.amountAvailable) }
    const result = props.editingProduct
      ? await store.updateProduct(props.editingProduct.id, payload)
      : await store.createProduct(payload)
    emit('saved', result)
    if (!props.editingProduct) form.value = { productName: '', cost: '', amountAvailable: '' }
  } catch (e) {
    if (e.response?.data?.errors) fieldErrors.value = Object.fromEntries(
      Object.entries(e.response.data.errors).map(([k, v]) => [k, v[0]])
    )
    error.value = e.response?.data?.message || 'Failed to save product.'
  } finally {
    loading.value = false
  }
}
</script>
