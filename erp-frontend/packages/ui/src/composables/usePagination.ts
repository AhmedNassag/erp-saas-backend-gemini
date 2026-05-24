import { ref, computed } from 'vue'

export function usePagination(defaultPerPage = 15) {
  const page    = ref(1)
  const perPage = ref(defaultPerPage)
  const total   = ref(0)

  const lastPage = computed(() => Math.ceil(total.value / perPage.value))
  const from     = computed(() => (page.value - 1) * perPage.value + 1)
  const to       = computed(() => Math.min(page.value * perPage.value, total.value))

  function setMeta(meta: { total: number; current_page: number; per_page: number }) {
    total.value   = meta.total
    page.value    = meta.current_page
    perPage.value = meta.per_page
  }

  return { page, perPage, total, lastPage, from, to, setMeta }
}
