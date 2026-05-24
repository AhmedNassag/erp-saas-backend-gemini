import { ref } from 'vue'
import type { AxiosError } from 'axios'

export function useApi<T>() {
  const data    = ref<T | null>(null)
  const loading = ref(false)
  const error   = ref<string | null>(null)

  async function execute(fn: () => Promise<{ data: T }>) {
    loading.value = true
    error.value   = null
    try {
      const res  = await fn()
      data.value = res.data
      return res.data
    } catch (e) {
      const err = e as AxiosError<{ message: string }>
      error.value = err.response?.data?.message ?? err.message
      throw e
    } finally {
      loading.value = false
    }
  }

  return { data, loading, error, execute }
}
