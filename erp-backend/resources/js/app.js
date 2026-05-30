import { createApp } from 'vue'
import { createPinia } from 'pinia'
import { createI18n } from 'vue-i18n'
import Notifications from '@kyvg/vue3-notification'
import App from './App.vue'
import router from './router'
import { Auth } from './API/Auth'

import '@fortawesome/fontawesome-free/css/all.min.css'

Auth.run()

router.afterEach(() => {
  requestAnimationFrame(() => {
    if (window.KTMenu) KTMenu.createInstances()
    if (window.KTScroll) KTScroll.createInstances()
    if (window.KTAppSidebar) KTAppSidebar.init()
  })
})

const app = createApp(App)
app.use(createPinia())
app.use(router)
app.use(createI18n({ locale: 'en', fallbackLocale: 'en', messages: {} }))
app.use(Notifications)

app.mount('#app')
