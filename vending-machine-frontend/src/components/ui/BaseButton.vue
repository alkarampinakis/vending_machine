<template>
  <button
    :type="type"
    :disabled="disabled || loading"
    :class="[
      'inline-flex items-center justify-center gap-2 rounded-lg px-4 py-2 font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed',
      variants[variant],
    ]"
    v-bind="$attrs"
  >
    <svg v-if="loading" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
    </svg>
    <slot />
  </button>
</template>

<script setup>
defineProps({
  type:     { type: String, default: 'button' },
  variant:  { type: String, default: 'primary' },
  loading:  { type: Boolean, default: false },
  disabled: { type: Boolean, default: false },
})

const variants = {
  primary:   'bg-indigo-600 text-white hover:bg-indigo-700 focus:ring-indigo-500',
  secondary: 'bg-gray-200 text-gray-800 hover:bg-gray-300 focus:ring-gray-400',
  danger:    'bg-red-600 text-white hover:bg-red-700 focus:ring-red-500',
  success:   'bg-green-600 text-white hover:bg-green-700 focus:ring-green-500',
  coin:      'bg-yellow-400 text-yellow-900 hover:bg-yellow-500 focus:ring-yellow-400 font-bold',
}
</script>
