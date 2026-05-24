import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { authApi } from '@nexaerp/api'

export const useAuthStore = defineStore('auth', () => {
  const admin = ref(JSON.parse(localStorage.getItem('admin') ?? 'null'))
  const token = ref(localStorage.getItem('super_admin_token') ?? '')

  const isLoggedIn = computed(() => !!token.value)

  async function loginWithPassword(email: string, password: string) {
    const res = await authApi.login({ email, password })
    setSession(res.data.token, res.data.admin)
  }

  async function sendEmailOtp(email: string) {
    await authApi.sendEmailOtp(email)
  }

  async function verifyEmailOtp(email: string, otp: string) {
    const res = await authApi.verifyEmailOtp({ email, otp })
    setSession(res.data.token, res.data.admin)
  }

  async function sendMobileOtp(mobile: string) {
    await authApi.sendMobileOtp(mobile)
  }

  async function verifyMobileOtp(mobile: string, otp: string) {
    const res = await authApi.verifyMobileOtp({ mobile, otp })
    setSession(res.data.token, res.data.admin)
  }

  async function logout() {
    try { await authApi.logout() } catch {}
    clearSession()
  }

  function setSession(t: string, a: object) {
    token.value = t
    admin.value = a
    localStorage.setItem('super_admin_token', t)
    localStorage.setItem('admin', JSON.stringify(a))
  }

  function clearSession() {
    token.value = ''
    admin.value = null
    localStorage.removeItem('super_admin_token')
    localStorage.removeItem('admin')
  }

  return { admin, token, isLoggedIn, loginWithPassword, sendEmailOtp, verifyEmailOtp, sendMobileOtp, verifyMobileOtp, logout }
})