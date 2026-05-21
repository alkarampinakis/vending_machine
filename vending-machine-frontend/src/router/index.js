import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const routes = [
  { path: '/', redirect: '/products' },
  { path: '/login',    component: () => import('../pages/LoginPage.vue'),    meta: { guest: true } },
  { path: '/register', component: () => import('../pages/RegisterPage.vue'), meta: { guest: true } },
  { path: '/products', component: () => import('../pages/ProductsPage.vue') },
  { path: '/buyer',    component: () => import('../pages/BuyerDashboard.vue'),  meta: { role: 'buyer' } },
  { path: '/seller',   component: () => import('../pages/SellerDashboard.vue'), meta: { role: 'seller' } },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

router.beforeEach((to) => {
  const auth = useAuthStore()

  if (to.meta.guest && auth.isAuthenticated) {
    return auth.isBuyer ? '/buyer' : '/seller'
  }

  if (to.meta.role) {
    if (!auth.isAuthenticated) return '/login'
    if (auth.user?.role !== to.meta.role) {
      return auth.isBuyer ? '/buyer' : '/seller'
    }
  }
})

export default router
