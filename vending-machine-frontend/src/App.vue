<template>
  <div class="min-h-screen bg-gray-50 flex flex-col">
    <AppHeader />
    <main class="flex-1">
      <RouterView />
    </main>
  </div>
</template>

<script setup>
import { onMounted } from 'vue'
import { useAuthStore } from './stores/auth'
import AppHeader from './components/layout/AppHeader.vue'

const auth = useAuthStore()

onMounted(async () => {
  if (auth.token && auth.user) {
    await auth.fetchUser()
  }

  document.addEventListener('visibilitychange', () => {
    if (!document.hidden && auth.isAuthenticated) {
      auth.fetchUser()
    }
  })
})
</script>
