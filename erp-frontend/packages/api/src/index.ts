import axios from 'axios'

// ─── Base factory ────────────────────────────────────────────────────────────
function createClient(baseURL: string, tokenKey: string) {
  const client = axios.create({ baseURL, headers: { Accept: 'application/json' } })

  client.interceptors.request.use((config) => {
    const token = localStorage.getItem(tokenKey)
    if (token) config.headers.Authorization = `Bearer ${token}`
    return config
  })

  client.interceptors.response.use(
    (res) => res,
    (err) => {
      if (err.response?.status === 401) localStorage.removeItem(tokenKey)
      return Promise.reject(err)
    }
  )
  return client
}

// ─── Clients ─────────────────────────────────────────────────────────────────

/** Central API — erp.test/api  (Super Admin guard) */
export const adminApi = createClient(
  import.meta.env.VITE_ADMIN_API_URL ?? 'http://erp.test:8000/api',
  'super_admin_token'
)

/** Tenant API — {subdomain}.erp.test/api  (Tenant guard) */
export const tenantApi = createClient(
  import.meta.env.VITE_TENANT_API_URL ?? `http://${window.location.hostname}/api`,
  'tenant_token'
)

/** Portfolio API — public endpoints on erp.test */
export const publicApi = axios.create({
  baseURL: import.meta.env.VITE_PUBLIC_API_URL ?? 'http://erp.test:8000/api',
  headers: { Accept: 'application/json' },
})

// ─── Re-exports ───────────────────────────────────────────────────────────────
export * from './admin'
export * from './tenant'
export * from './public'
