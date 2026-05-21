<template>
  <div class="min-h-screen bg-gray-50 flex items-center justify-center px-4">
    <div class="bg-white rounded-2xl shadow-md w-full max-w-sm p-8 flex flex-col gap-5">
      <div class="text-center">
        <h1 class="text-2xl font-bold text-gray-900">Create account</h1>
        <p class="text-gray-500 text-sm mt-1">Join the vending machine</p>
      </div>

      <AlertBanner :message="typeof error === 'string' ? error : null" />

      <form @submit.prevent="submit" class="flex flex-col gap-4">
        <BaseInput v-model="form.username" label="Username" placeholder="your_username" required :error="fieldErrors.username" />
        <BaseInput v-model="form.password" label="Password" type="password" placeholder="min 8 characters" required :error="fieldErrors.password" />

        <div class="flex flex-col gap-1">
          <label class="text-sm font-medium text-gray-700">Role</label>
          <select
            v-model="form.role"
            class="rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
          >
            <option value="buyer">Buyer — deposit coins and purchase products</option>
            <option value="seller">Seller — list and manage products</option>
          </select>
        </div>

        <BaseButton type="submit" :loading="loading" class="w-full">Create Account</BaseButton>
      </form>

      <p class="text-center text-sm text-gray-500">
        Already have an account?
        <RouterLink to="/login" class="text-indigo-600 hover:underline">Sign in</RouterLink>
      </p>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import BaseInput from '../components/ui/BaseInput.vue'
import BaseButton from '../components/ui/BaseButton.vue'
import AlertBanner from '../components/ui/AlertBanner.vue'

const auth   = useAuthStore()
const router = useRouter()

const form    = ref({ username: '', password: '', role: 'buyer' })
const loading = ref(false)
const error   = ref(null)
const fieldErrors = ref({})

async function submit() {
  loading.value    = true
  error.value      = null
  fieldErrors.value = {}
  const result = await auth.register(form.value)
  loading.value = false
  if (result.success) {
    router.push(auth.isBuyer ? '/buyer' : '/seller')
  } else {
    const e = auth.error
    if (typeof e === 'object') fieldErrors.value = Object.fromEntries(Object.entries(e).map(([k,v]) => [k, v[0]]))
    else error.value = e
  }
}
</script>
