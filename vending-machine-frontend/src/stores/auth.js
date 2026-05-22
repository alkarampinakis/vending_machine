import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import api from '../api/axios'

export const useAuthStore = defineStore('auth', () => {
  const user  = ref(JSON.parse(localStorage.getItem('user') || 'null'))
  const token = ref(localStorage.getItem('token') || null)
  const error = ref(null)
  const loading = ref(false)

  const isAuthenticated = computed(() => !!token.value)
  const isBuyer  = computed(() => user.value?.role === 'buyer')
  const isSeller = computed(() => user.value?.role === 'seller')

  function setToken(t) {
    token.value = t
    if (t) localStorage.setItem('token', t)
    else   localStorage.removeItem('token')
  }

  function setUser(u) {
    user.value = u
    if (u) localStorage.setItem('user', JSON.stringify(u))
    else   localStorage.removeItem('user')
  }

  async function login(credentials) {
    loading.value = true
    error.value   = null
    try {
      const { data } = await api.post('/login', credentials)
      setToken(data.token)
      setUser(data.user)
      return { success: true }
    } catch (e) {
      error.value = e.response?.data?.message || 'Login failed.'
      return { success: false, status: e.response?.status }
    } finally {
      loading.value = false
    }
  }

  async function register(payload) {
    loading.value = true
    error.value   = null
    try {
      await api.post('/user', payload)
      return await login({ username: payload.username, password: payload.password })
    } catch (e) {
      error.value = e.response?.data?.errors || e.response?.data?.message || 'Registration failed.'
      return { success: false }
    } finally {
      loading.value = false
    }
  }

  async function logout() {
    try { await api.post('/logout') } catch {}
    setToken(null)
    setUser(null)
  }

  async function logoutAll() {
    try { await api.post('/logout/all') } catch {}
    setToken(null)
    setUser(null)
  }

  async function fetchUser() {
    if (!token.value || !user.value?.id) return
    try {
      const { data } = await api.get(`/user/${user.value.id}`)
      setUser(data)
    } catch {
      setToken(null)
      setUser(null)
    }
  }

  function updateDeposit(amount) {
    if (user.value) {
      setUser({ ...user.value, deposit: amount })
    }
  }

  return { user, token, error, loading, isAuthenticated, isBuyer, isSeller,
           login, register, logout, logoutAll, fetchUser, updateDeposit, setUser }
})
