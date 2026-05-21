<template>
  <header class="bg-indigo-700 text-white shadow-md">
    <div class="max-w-5xl mx-auto px-4 py-3 flex items-center justify-between">
      <RouterLink to="/products" class="text-xl font-bold tracking-tight">🏧 VendingMachine</RouterLink>

      <nav class="flex items-center gap-4 text-sm">
        <RouterLink to="/products" class="hover:text-indigo-200 transition-colors">Products</RouterLink>

        <template v-if="auth.isAuthenticated">
          <RouterLink
            v-if="auth.isBuyer"
            to="/buyer"
            class="hover:text-indigo-200 transition-colors"
          >Dashboard</RouterLink>
          <RouterLink
            v-if="auth.isSeller"
            to="/seller"
            class="hover:text-indigo-200 transition-colors"
          >My Products</RouterLink>

          <span v-if="auth.isBuyer" class="text-indigo-200 text-xs">
            Balance: <strong class="text-white">{{ auth.user?.deposit ?? 0 }}¢</strong>
          </span>

          <span class="text-indigo-300 text-xs">{{ auth.user?.username }}</span>

          <button
            @click="auth.logout().then(() => router.push('/login'))"
            class="text-indigo-200 hover:text-white transition-colors"
          >Logout</button>
        </template>

        <template v-else>
          <RouterLink to="/login"    class="hover:text-indigo-200 transition-colors">Login</RouterLink>
          <RouterLink to="/register" class="bg-white text-indigo-700 px-3 py-1 rounded-md font-medium hover:bg-indigo-100 transition-colors">Register</RouterLink>
        </template>
      </nav>
    </div>
  </header>
</template>

<script setup>
import { useAuthStore } from '../../stores/auth'
import { useRouter } from 'vue-router'
const auth   = useAuthStore()
const router = useRouter()
</script>
