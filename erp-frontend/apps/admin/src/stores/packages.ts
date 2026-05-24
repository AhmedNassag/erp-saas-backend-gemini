import { defineStore } from 'pinia'
import { ref } from 'vue'
import { packagesApi } from '@nexaerp/api'

export const usePackagesStore = defineStore('packages', () => {
  const packages = ref([])
  const loading  = ref(false)

  async function fetchAll() {
    loading.value = true
    try {
      const res = await packagesApi.list()
      packages.value = res.data.data ?? res.data
    } finally { loading.value = false }
  }

  async function create(data: object) {
    await packagesApi.create(data)
    await fetchAll()
  }

  async function update(id: number, data: object) {
    await packagesApi.update(id, data)
    await fetchAll()
  }

  async function destroy(id: number) {
    await packagesApi.destroy(id)
    await fetchAll()
  }

  return { packages, loading, fetchAll, create, update, destroy }
})