import { tenantApi } from './index'

// ── Auth ──────────────────────────────────────────────────────────────────────
export const tenantAuthApi = {
  login:   (data: { email: string; password: string }) =>
             tenantApi.post('/login', data),
  logout:  () => tenantApi.post('/logout'),
}

// ── Dashboard ─────────────────────────────────────────────────────────────────
export const dashboardApi = {
  index: () => tenantApi.get('/dashboard'),
}

// ── Core — Countries ──────────────────────────────────────────────────────────
export const countriesApi = {
  list:    (params?: object) => tenantApi.get('/v1/core/country', { params }),
  show:    (id: number)      => tenantApi.get(`/v1/core/country/${id}`),
  create:  (data: object)    => tenantApi.post('/v1/core/country', data),
  update:  (id: number, data: object) => tenantApi.put(`/v1/core/country/${id}`, data),
  destroy: (id: number)      => tenantApi.delete(`/v1/core/country/${id}`),
}

// ── Core — Cities ─────────────────────────────────────────────────────────────
export const citiesApi = {
  list:    (params?: object) => tenantApi.get('/v1/core/city', { params }),
  show:    (id: number)      => tenantApi.get(`/v1/core/city/${id}`),
  create:  (data: object)    => tenantApi.post('/v1/core/city', data),
  update:  (id: number, data: object) => tenantApi.put(`/v1/core/city/${id}`, data),
  destroy: (id: number)      => tenantApi.delete(`/v1/core/city/${id}`),
}

// ── Core — Areas ──────────────────────────────────────────────────────────────
export const areasApi = {
  list:    (params?: object) => tenantApi.get('/v1/core/area', { params }),
  show:    (id: number)      => tenantApi.get(`/v1/core/area/${id}`),
  create:  (data: object)    => tenantApi.post('/v1/core/area', data),
  update:  (id: number, data: object) => tenantApi.put(`/v1/core/area/${id}`, data),
  destroy: (id: number)      => tenantApi.delete(`/v1/core/area/${id}`),
}

// ── Core — Branches ───────────────────────────────────────────────────────────
export const branchesApi = {
  list:    (params?: object) => tenantApi.get('/v1/core/branch', { params }),
  show:    (id: number)      => tenantApi.get(`/v1/core/branch/${id}`),
  create:  (data: object)    => tenantApi.post('/v1/core/branch', data),
  update:  (id: number, data: object) => tenantApi.put(`/v1/core/branch/${id}`, data),
  destroy: (id: number)      => tenantApi.delete(`/v1/core/branch/${id}`),
}

// ── Core — Roles ──────────────────────────────────────────────────────────────
export const rolesApi = {
  list:    (params?: object) => tenantApi.get('/v1/core/roles', { params }),
  show:    (id: number)      => tenantApi.get(`/v1/core/roles/${id}`),
  create:  (data: object)    => tenantApi.post('/v1/core/roles', data),
  update:  (id: number, data: object) => tenantApi.put(`/v1/core/roles/${id}`, data),
  destroy: (id: number)      => tenantApi.delete(`/v1/core/roles/${id}`),
}

// ── Core — Permissions ────────────────────────────────────────────────────────
export const permissionsApi = {
  list: (params?: object) => tenantApi.get('/v1/core/permissions', { params }),
}
