<template>
  <div class="card card-flush">
    <div class="card-header pt-7">
      <h3 class="card-title align-items-start flex-column">
        <span class="card-label fw-bold text-dark">Permissions</span>
        <span class="text-gray-400 mt-1 fw-semibold fs-6">Manage system permissions</span>
      </h3>
    </div>
    <div class="card-body pt-0">
      <div class="table-responsive">
        <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
          <thead>
            <tr class="fw-bold text-muted">
              <th class="min-w-200px">Name</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in items" :key="item.id">
              <td>{{ item.name }}</td>
            </tr>
            <tr v-if="!items.length">
              <td colspan="3" class="text-center text-muted py-10">No permissions found</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script>
import API from '../../../API/API'
import Swal from 'sweetalert2'
import { notify } from '@kyvg/vue3-notification'

export default {
  name: 'PermissionsView',
  data() {
    return { api: new API('api/v1/core/permissions'), items: [] }
  },
  mounted() { this.loadItems() },
  methods: {
    async loadItems() {
      try { const d = await this.api.getAll(); this.items = d.data || d } catch { notify({ text: 'Failed to load permissions', type: 'error' }) }
    },
    async deleteItem(id) {
      if ((await Swal.fire({ title: 'Delete Permission?', text: 'This action cannot be undone.', icon: 'warning', showCancelButton: true, confirmButtonText: 'Delete' })).isConfirmed) {
        try { await this.api.delete(id); this.loadItems() } catch { notify({ text: 'Failed to delete', type: 'error' }) }
      }
    }
  }
}
</script>
