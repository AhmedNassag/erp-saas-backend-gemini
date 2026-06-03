import { defineStore } from 'pinia'
import { Auth } from '../API/Auth'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: Auth.USER,
    permissions: Auth.PERMISSIONS,
    token: Auth.getToken(),
  }),
  getters: {
    isLoggedIn: (state) => !!state.token,
    userPermissions: (state) => state.permissions,
  },
  actions: {
    setUser(user) {
      this.user = user
      Auth.USER = user
    },
    setPermissions(perms) {
      this.permissions = perms
      Auth.PERMISSIONS = perms
    },
    setToken(token) {
      this.token = token
      localStorage.setItem('api_token', token)
    },
    logout() {
      this.user = null
      this.permissions = []
      this.token = null
      Auth.logOut()
    }
  }
})
