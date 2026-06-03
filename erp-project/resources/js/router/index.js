import { createRouter, createWebHistory } from 'vue-router'
import BodyView from '../layout/BodyView.vue'
import { Auth } from '../API/Auth'
import CoreRoutes from './modules/Core'

const routes = [
  {
    path: '/',
    component: BodyView,
    meta: { requiresAuth: true },
    children: [
      {
        path: '',
        redirect: '/dashboard'
      },
      {
        path: 'dashboard',
        name: 'Dashboard',
        component: () => import('../views/Page/Dashboard/Dashboard.vue'),
        meta: { title: 'Dashboard' }
      },
      ...CoreRoutes,
    ]
  },
  {
    path: '/login',
    name: 'Login',
    component: () => import('../views/Page/Auth/Login.vue'),
    meta: { title: 'Login', requiresGuest: true }
  },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

router.beforeEach((to, from, next) => {
  const token = Auth.getToken()
  if (to.meta.requiresAuth && !token) {
    return next('/login')
  }
  if (to.meta.requiresGuest && token) {
    return next('/dashboard')
  }
  document.title = to.meta.title ? `${to.meta.title} - ERP` : 'ERP'
  next()
})

export default router
