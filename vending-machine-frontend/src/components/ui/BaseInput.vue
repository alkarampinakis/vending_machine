<template>
  <div class="flex flex-col gap-1">
    <label v-if="label" :for="id" class="text-sm font-medium text-gray-700">{{ label }}</label>
    <input
      :id="id"
      :type="type"
      :value="modelValue"
      :placeholder="placeholder"
      :required="required"
      @input="$emit('update:modelValue', $event.target.value)"
      class="rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 disabled:bg-gray-100"
      :class="{ 'border-red-500 focus:ring-red-500': error }"
    />
    <p v-if="error" class="text-xs text-red-600">{{ error }}</p>
  </div>
</template>

<script setup>
defineProps({
  id:          { type: String, default: () => `input-${Math.random().toString(36).slice(2)}` },
  label:       String,
  type:        { type: String, default: 'text' },
  modelValue:  String,
  placeholder: String,
  required:    Boolean,
  error:       String,
})
defineEmits(['update:modelValue'])
</script>
