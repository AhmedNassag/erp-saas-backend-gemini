import { defineStore } from 'pinia'
import { ref } from 'vue'
import { statsApi } from '@nexaerp/api'

export const useStatsStore = defineStore('stats', () => {
  const overview = ref(null)
  const loading  = ref(false)

  async function fetchOverview() {
    loading.value = true
    try {
      const res = await statsApi.overview()
      overview.value = res.data.data ?? res.data
    } finally { loading.value = false }
  }

  return { overview, loading, fetchOverview }
})