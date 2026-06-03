import { defineStore } from 'pinia'

export const useLayoutStore = defineStore('layout', {
  state: () => ({
    sidebar: 'compact',
    theme: 'light',
    primaryColor: '#009ef7',
  }),
  actions: {
    setLayout(type) {
      this.sidebar = type
    },
    setTheme(mode) {
      this.theme = mode
      document.documentElement.setAttribute('data-bs-theme', mode)
      localStorage.setItem('data-bs-theme', mode)
    }
  }
})
