<template>
  <div class="min-h-screen bg-gray-50 flex items-center justify-center px-4">
    <div class="bg-white rounded-2xl shadow-md w-full max-w-sm p-8 flex flex-col gap-5">
      <div class="text-center">
        <h1 class="text-2xl font-bold text-gray-900">Welcome back</h1>
        <p class="text-gray-500 text-sm mt-1">Sign in to your account</p>
      </div>

      <AlertBanner :message="error" />

      <!-- Concurrent session action -->
      <div v-if="hasActiveSession" class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 flex flex-col gap-3">
        <p class="text-sm text-yellow-800">You can terminate all active sessions and sign in here instead.</p>
        <BaseButton variant="danger" :loading="loading" @click="forceLogin" class="w-full">
          Terminate all sessions &amp; sign in
        </BaseButton>
      </div>

      <form @submit.prevent="submit" class="flex flex-col gap-4">
        <BaseInput v-model="form.username" label="Username" placeholder="your_username" required />
        <BaseInput v-model="form.password" label="Password" type="password" placeholder="••••••••" required />
        <BaseButton type="submit" :loading="loading" class="w-full">Sign In</BaseButton>
      </form>

      <p class="text-center text-sm text-gray-500">
        No account?
        <RouterLink to="/register" class="text-indigo-600 hover:underline">Register</RouterLink>
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

const form           = ref({ username: '', password: '' })
const loading        = ref(false)
const error          = ref(null)
const hasActiveSession = ref(false)

async function submit() {
  loading.value        = true
  error.value          = null
  hasActiveSession.value = false
  const result = await auth.login(form.value)
  loading.value = false
  if (result.success) {
    router.push(auth.isBuyer ? '/buyer' : '/seller')
  } else {
    error.value = auth.error
    if (result.status === 409) hasActiveSession.value = true
  }
}

async function forceLogin() {
  loading.value = true
  error.value   = null
  const result  = await auth.login({ ...form.value, force: true })
  loading.value = false
  if (result.success) {
    router.push(auth.isBuyer ? '/buyer' : '/seller')
  } else {
    error.value          = auth.error
    hasActiveSession.value = false
  }
}
</script>
