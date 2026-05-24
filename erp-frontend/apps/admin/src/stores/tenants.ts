import { defineStore } from 'pinia'
import { ref } from 'vue'
import { tenantsApi } from '@nexaerp/api'

export const useTenantsStore = defineStore('tenants', () => {
  const tenants  = ref([])
  const tenant   = ref(null)
  const meta     = ref({ total: 0, current_page: 1, per_page: 15, last_page: 1 })
  const loading  = ref(false)

  async function fetchAll(params = {}) {
    loading.value = true
    try {
      const res = await tenantsApi.list(params)
      tenants.value = res.data.data ?? res.data
      if (res.data.meta) meta.value = res.data.meta
    } finally { loading.value = false }
  }

  async function fetchOne(id: number) {
    loading.value = true
    try {
      const res = await tenantsApi.show(id)
      tenant.value = res.data.data ?? res.data
    } finally { loading.value = false }
  }

  async function suspend(id: number) {
    await tenantsApi.suspend(id)
    await fetchAll()
  }

  async function activate(id: number) {
    await tenantsApi.activate(id)
    await fetchAll()
  }

  async function destroy(id: number) {
    await tenantsApi.destroy(id)
    await fetchAll()
  }

  return { tenants, tenant, meta, loading, fetchAll, fetchOne, suspend, activate, destroy }
})