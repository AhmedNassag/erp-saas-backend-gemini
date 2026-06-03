import { defineStore } from 'pinia'

export const useLanguageStore = defineStore('language', {
  state: () => ({
    locale: localStorage.getItem('locale') || 'en',
    languages: [
      { code: 'en', name: 'English', flag: 'fi fi-us' },
      { code: 'ar', name: 'العربية', flag: 'fi fi-eg' },
    ]
  }),
  actions: {
    setLocale(locale) {
      this.locale = locale
      localStorage.setItem('locale', locale)
    }
  }
})
