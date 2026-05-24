import { adminApi } from './index'

// ── Auth ──────────────────────────────────────────────────────────────────────
export const authApi = {
  login:             (data: { email: string; password: string }) =>
                       adminApi.post('/admin/auth/login', data),
  sendEmailOtp:      (email: string) =>
                       adminApi.post('/admin/auth/email-otp/send', { email }),
  verifyEmailOtp:    (data: { email: string; otp: string }) =>
                       adminApi.post('/admin/auth/email-otp/verify', data),
  sendMobileOtp:     (mobile: string) =>
                       adminApi.post('/admin/auth/mobile-otp/send', { mobile }),
  verifyMobileOtp:   (data: { mobile: string; otp: string }) =>
                       adminApi.post('/admin/auth/mobile-otp/verify', data),
  me:                () => adminApi.get('/admin/auth/me'),
  logout:            () => adminApi.post('/admin/auth/logout'),
  logoutAll:         () => adminApi.post('/admin/auth/logout-all'),
}

// ── Tenants ───────────────────────────────────────────────────────────────────
export const tenantsApi = {
  list:    (params?: object) => adminApi.get('/admin/tenants', { params }),
  show:    (id: number)      => adminApi.get(`/admin/tenants/${id}`),
  create:  (data: object)    => adminApi.post('/admin/tenants', data),
  update:  (id: number, data: object) => adminApi.put(`/admin/tenants/${id}`, data),
  destroy: (id: number)      => adminApi.delete(`/admin/tenants/${id}`),
  suspend: (id: number)      => adminApi.patch(`/admin/tenants/${id}/suspend`),
  activate:(id: number)      => adminApi.patch(`/admin/tenants/${id}/activate`),
}

// ── Packages ──────────────────────────────────────────────────────────────────
export const packagesApi = {
  list:    (params?: object) => adminApi.get('/admin/packages', { params }),
  show:    (id: number)      => adminApi.get(`/admin/packages/${id}`),
  create:  (data: object)    => adminApi.post('/admin/packages', data),
  update:  (id: number, data: object) => adminApi.put(`/admin/packages/${id}`, data),
  destroy: (id: number)      => adminApi.delete(`/admin/packages/${id}`),
}

// ── Subscriptions ─────────────────────────────────────────────────────────────
export const subscriptionsApi = {
  list:    (params?: object) => adminApi.get('/admin/subscriptions', { params }),
  show:    (id: number)      => adminApi.get(`/admin/subscriptions/${id}`),
  cancel:  (id: number)      => adminApi.patch(`/admin/subscriptions/${id}/cancel`),
  renew:   (id: number)      => adminApi.patch(`/admin/subscriptions/${id}/renew`),
}

// ── Portfolio CMS ─────────────────────────────────────────────────────────────
export const cmsApi = {
  getSettings:    ()             => adminApi.get('/admin/cms/settings'),
  updateSettings: (data: object) => adminApi.put('/admin/cms/settings', data),
  getHero:        ()             => adminApi.get('/admin/cms/hero'),
  updateHero:     (data: object) => adminApi.put('/admin/cms/hero', data),
  getFeatures:    ()             => adminApi.get('/admin/cms/features'),
  updateFeatures: (data: object) => adminApi.put('/admin/cms/features', data),
  getTestimonials:()             => adminApi.get('/admin/cms/testimonials'),
  updateTestimonials:(data: object) => adminApi.put('/admin/cms/testimonials', data),
}

// ── Dashboard Stats ───────────────────────────────────────────────────────────
export const statsApi = {
  overview: () => adminApi.get('/admin/stats/overview'),
  revenue:  (params?: object) => adminApi.get('/admin/stats/revenue', { params }),
}
