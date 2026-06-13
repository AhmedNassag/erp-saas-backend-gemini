<template>
  <div>
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
      <!-- البحث في اليسار -->
      <div class="position-relative" style="width: 320px; max-width: 100%;">
        <i class="fas fa-search position-absolute top-50 translate-middle-y ms-3 text-gray-500"></i>
        <input 
          type="text" 
          class="form-control form-control-sm ps-10" 
          placeholder="Search..." 
          v-model="localSearch" 
        />
      </div>
      
      <!-- per_page في اليمين -->
      <div class="d-flex align-items-center gap-2">
        <span class="text-gray-700 fw-semibold fs-7">Show</span>
        <select class="form-select form-select-sm w-auto" :value="perPage" @change="$emit('update:perPage', Number($event.target.value))">
          <option v-for="opt in perPageOptions" :key="opt" :value="opt">{{ opt === -1 ? 'All' : opt }}</option>
        </select>
        <span class="text-gray-500 fs-7 d-none d-sm-inline">entries per page</span>
      </div>
    </div>

    <!-- Table slot -->
    <slot />

    <!-- Bottom pagination -->
    <div v-if="lastPage > 1" class="d-flex align-items-center justify-content-between mt-4">
      <span class="text-gray-500 fs-7">Showing {{ from }} to {{ to }} of {{ total }} entries</span>
      <nav>
        <ul class="pagination pagination-sm mb-0">
          <li class="page-item" :class="{ disabled: currentPage <= 1 }">
            <button class="page-link" @click="goToPage(currentPage - 1)">Previous</button>
          </li>
          <template v-for="p in visiblePages" :key="p">
            <li v-if="p !== '...'" class="page-item" :class="{ active: p === currentPage }">
              <button class="page-link" @click="goToPage(p)">{{ p }}</button>
            </li>
            <li v-else class="page-item disabled">
              <span class="page-link">...</span>
            </li>
          </template>
          <li class="page-item" :class="{ disabled: currentPage >= lastPage }">
            <button class="page-link" @click="goToPage(currentPage + 1)">Next</button>
          </li>
        </ul>
      </nav>
    </div>
  </div>
</template>

<script>
export default {
  name: 'PaginationWrapper',
  props: {
    currentPage: { type: Number, default: 1 },
    lastPage: { type: Number, default: 1 },
    total: { type: Number, default: 0 },
    perPage: { type: Number, default: 10 },
    perPageOptions: { type: Array, default: () => [5, 10, 50, 100, 500, -1] },
    search: { type: String, default: '' },
  },
  emits: ['update:currentPage', 'update:perPage', 'update:search'],
  data() {
    return {
      localSearch: this.search,
      searchTimer: null,
    }
  },
  watch: {
    search(val) { this.localSearch = val },
    localSearch(val) {
      if (this.searchTimer) clearTimeout(this.searchTimer)
      this.searchTimer = setTimeout(() => {
        this.$emit('update:search', val)
      }, 400)
    },
  },
  computed: {
    from() {
      if (!this.total) return 0
      return (this.currentPage - 1) * this.perPage + 1
    },
    to() {
      if (!this.total) return 0
      return Math.min(this.currentPage * this.perPage, this.total)
    },
    visiblePages() {
      if (this.lastPage <= 7) {
        return Array.from({ length: this.lastPage }, (_, i) => i + 1)
      }
      const pages = []
      const left = Math.max(2, this.currentPage - 1)
      const right = Math.min(this.lastPage - 1, this.currentPage + 1)
      pages.push(1)
      if (left > 2) pages.push('...')
      for (let i = left; i <= right; i++) pages.push(i)
      if (right < this.lastPage - 1) pages.push('...')
      if (this.lastPage > 1) pages.push(this.lastPage)
      return pages
    },
  },
  methods: {
    goToPage(p) {
      if (p < 1 || p > this.lastPage) return
      this.$emit('update:currentPage', p)
    },
  },
}
</script>
