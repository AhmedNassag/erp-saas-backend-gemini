import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { tenantAuthApi } from '@nexaerp/api'

export const useTenantAuthStore = defineStore('tenantAuth', () => {
  const user  = ref(JSON.parse(localStorage.getItem('tenant_user') ?? 'null'))
  const token = ref(localStorage.getItem('tenant_token') ?? '')
  const isLoggedIn = computed(() => !!token.value)

  async function login(email: string, password: string) {
    const res = await tenantAuthApi.login({ email, password })
    token.value = res.data.token
    user.value  = res.data.user
    localStorage.setItem('tenant_token', res.data.token)
    localStorage.setItem('tenant_user', JSON.stringify(res.data.user))
  }

  async function logout() {
    try { await tenantAuthApi.logout() } catch {}
    token.value = ''; user.value = null
    localStorage.removeItem('tenant_token')
    localStorage.removeItem('tenant_user')
  }

  return { user, token, isLoggedIn, login, logout }
})