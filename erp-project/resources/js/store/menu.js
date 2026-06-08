import { defineStore } from 'pinia'
import menuData from '../core/data/menu'

export const useMenuStore = defineStore('menu', {
  state: () => ({
    data: menuData,
    activeParent: null,
    activeSub: null,
  }),
  actions: {
    setActive(path) {
      const parts = path.split('/')
      if (parts.length >= 2) {
        this.activeParent = parts[0]
        this.activeSub = parts[1]
      }
    }
  }
})
