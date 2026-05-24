import { createRouter, createWebHistory } from 'vue-router'
import { useTenantAuthStore } from '../stores/auth'
const router = createRouter({
  history: createWebHistory(),
  routes: [
    { path: '/login', name: 'login', component: () => import('../pages/auth/LoginPage.vue'), meta: { guest: true } },
    {
      path: '/',
      component: () => import('../components/layout/TenantShell.vue'),
      meta: { requiresAuth: true },
      children: [
        { path: '', redirect: '/dashboard' },
        { path: 'dashboard', name: 'dashboard', component: () => import('../pages/dashboard/DashboardPage.vue') },
        { path: 'core/countries', name: 'countries', component: () => import('../pages/core/CountriesPage.vue') },
        { path: 'core/cities',    name: 'cities',    component: () => import('../pages/core/CitiesPage.vue') },
        { path: 'core/areas',     name: 'areas',     component: () => import('../pages/core/AreasPage.vue') },
        { path: 'core/branches',  name: 'branches',  component: () => import('../pages/core/BranchesPage.vue') },
        { path: 'core/roles',     name: 'roles',     component: () => import('../pages/core/RolesPage.vue') },
      ],
    },
    { path: '/:pathMatch(.*)*', redirect: '/' },
  ],
})
router.beforeEach((to) => {
  const auth = useTenantAuthStore()
  if (to.meta.requiresAuth && !auth.isLoggedIn) return { name: 'login' }
  if (to.meta.guest && auth.isLoggedIn) return { name: 'dashboard' }
})
export default router