<template>
  <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex flex-col gap-3">
    <div class="flex items-start justify-between">
      <h3 class="font-semibold text-gray-900">{{ product.productName }}</h3>
      <span class="text-indigo-600 font-bold text-lg">{{ product.cost }}¢</span>
    </div>

    <div class="text-sm text-gray-500">
      <span>Stock: <strong class="text-gray-700">{{ product.amountAvailable }}</strong></span>
      <span v-if="product.seller" class="ml-3">By: <strong class="text-gray-700">{{ product.seller.username }}</strong></span>
    </div>

    <div class="flex gap-2 mt-auto pt-2">
      <slot name="actions" />
    </div>
  </div>
</template>

<script setup>
defineProps({ product: { type: Object, required: true } })
</script>
