import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const router = createRouter({
  history: createWebHistory(),
  routes: [
    // ── Auth ──────────────────────────────────────────────────────────────────
    {
      path: '/login',
      name: 'login',
      component: () => import('../pages/auth/LoginPage.vue'),
      meta: { guest: true },
    },

    // ── Admin Shell ───────────────────────────────────────────────────────────
    {
      path: '/',
      component: () => import('../components/layout/AdminShell.vue'),
      meta: { requiresAuth: true },
      children: [
        { path: '',          redirect: '/dashboard' },
        { path: 'dashboard', name: 'dashboard',     component: () => import('../pages/dashboard/DashboardPage.vue') },

        // Tenants
        { path: 'tenants',        name: 'tenants',       component: () => import('../pages/tenants/TenantsPage.vue') },
        { path: 'tenants/:id',    name: 'tenant-show',   component: () => import('../pages/tenants/TenantShowPage.vue') },

        // Packages
        { path: 'packages',       name: 'packages',      component: () => import('../pages/packages/PackagesPage.vue') },

        // Subscriptions
        { path: 'subscriptions',  name: 'subscriptions', component: () => import('../pages/subscriptions/SubscriptionsPage.vue') },

        // Portfolio CMS
        { path: 'cms',            name: 'cms',           component: () => import('../pages/portfolio-cms/CmsPage.vue') },
        { path: 'cms/hero',       name: 'cms-hero',      component: () => import('../pages/portfolio-cms/HeroEditor.vue') },
        { path: 'cms/features',   name: 'cms-features',  component: () => import('../pages/portfolio-cms/FeaturesEditor.vue') },
        { path: 'cms/pricing',    name: 'cms-pricing',   component: () => import('../pages/portfolio-cms/PricingEditor.vue') },
        { path: 'cms/testimonials', name: 'cms-testimonials', component: () => import('../pages/portfolio-cms/TestimonialsEditor.vue') },

        // Settings
        { path: 'settings',       name: 'settings',      component: () => import('../pages/settings/SettingsPage.vue') },
      ],
    },

    // 404
    { path: '/:pathMatch(.*)*', name: 'not-found', component: () => import('../pages/NotFoundPage.vue') },
  ],
})

// Navigation guard
router.beforeEach((to) => {
  const auth = useAuthStore()
  if (to.meta.requiresAuth && !auth.isLoggedIn) return { name: 'login' }
  if (to.meta.guest && auth.isLoggedIn) return { name: 'dashboard' }
})

export default router