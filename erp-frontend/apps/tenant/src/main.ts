import { createApp } from 'vue'
import { createPinia } from 'pinia'
import { createI18n } from 'vue-i18n'
import router from './router'
import App from './App.vue'
import { en, ar } from '@nexaerp/i18n'
import './assets/main.css'
const i18n = createI18n({ legacy: false, locale: localStorage.getItem('locale') ?? 'en', fallbackLocale: 'en', messages: { en, ar } })
createApp(App).use(createPinia()).use(router).use(i18n).mount('#app')