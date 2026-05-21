<template>
  <div
    v-if="message"
    :class="[
      'rounded-lg px-4 py-3 text-sm flex items-start gap-2',
      types[type],
    ]"
  >
    <span class="shrink-0 mt-0.5">{{ icons[type] }}</span>
    <span>{{ message }}</span>
  </div>
</template>

<script setup>
defineProps({
  message: String,
  type:    { type: String, default: 'error' },
})

const types = {
  error:   'bg-red-50 text-red-700 border border-red-200',
  success: 'bg-green-50 text-green-700 border border-green-200',
  info:    'bg-blue-50 text-blue-700 border border-blue-200',
  warning: 'bg-yellow-50 text-yellow-700 border border-yellow-200',
}

const icons = { error: '✕', success: '✓', info: 'ℹ', warning: '⚠' }
</script>
