import { createRouter, createWebHistory } from 'vue-router'
const router = createRouter({
  history: createWebHistory(),
  routes: [
    { path: '/',          name: 'home',      component: () => import('../pages/HomePage.vue') },
    { path: '/about',     name: 'about',     component: () => import('../pages/AboutPage.vue') },
    { path: '/pricing',   name: 'pricing',   component: () => import('../pages/PricingPage.vue') },
    { path: '/contact',   name: 'contact',   component: () => import('../pages/ContactPage.vue') },
    { path: '/subscribe/:id', name: 'subscribe', component: () => import('../pages/SubscribePage.vue') },
    { path: '/success',   name: 'success',   component: () => import('../pages/SuccessPage.vue') },
  ],
  scrollBehavior: () => ({ top: 0 }),
})
export default router