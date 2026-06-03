<template>
  <div class="card card-flush">
    <div class="card-header pt-7">
      <h3 class="card-title align-items-start flex-column">
        <span class="card-label fw-bold text-dark">Branches</span>
        <span class="text-gray-400 mt-1 fw-semibold fs-6">Manage system branches</span>
      </h3>
      <div class="card-toolbar">
        <button type="button" class="btn btn-primary" @click="openForm()">
          <i class="ki-outline ki-plus fs-2"></i> Add Branch
        </button>
      </div>
    </div>
    <div class="card-body pt-0">
      <div class="table-responsive">
        <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
          <thead>
            <tr class="fw-bold text-muted">
              <th class="min-w-150px">Name</th>
              <th class="min-w-100px">Code</th>
              <th class="min-w-100px">Area</th>
              <th class="min-w-50px text-end">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in items" :key="item.id">
              <td>{{ item.name }}</td>
              <td>{{ item.code }}</td>
              <td>{{ item.area?.name || '-' }}</td>
              <td class="text-end">
                <button class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1" @click="editItem(item)">
                  <i class="ki-outline ki-pencil fs-2"></i>
                </button>
                <button class="btn btn-icon btn-bg-light btn-active-color-danger btn-sm" @click="deleteItem(item.id)">
                  <i class="ki-outline ki-trash fs-2"></i>
                </button>
              </td>
            </tr>
            <tr v-if="!items.length">
              <td colspan="4" class="text-center text-muted py-10">No branches found</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    <div class="modal fade" tabindex="-1" ref="modalEl" data-bs-backdrop="static">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h3 class="modal-title">{{ editingId ? 'Edit Branch' : 'Add Branch' }}</h3>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form @submit.prevent="saveItem">
            <div class="modal-body">
              <div class="fv-row mb-7">
                <label class="required fs-6 fw-semibold mb-2">Name</label>
                <input type="text" v-model="form.name" class="form-control form-control-solid" required />
              </div>
              <div class="fv-row mb-7">
                <label class="fs-6 fw-semibold mb-2">Code</label>
                <input type="text" v-model="form.code" class="form-control form-control-solid" />
              </div>
              <div class="fv-row mb-7">
                <label class="required fs-6 fw-semibold mb-2">Area</label>
                <select v-model="form.area_id" class="form-select form-select-solid" required>
                  <option value="" disabled>Select area</option>
                  <option v-for="a in areas" :key="a.id" :value="a.id">{{ a.name }}</option>
                </select>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
              <button type="submit" class="btn btn-primary" :disabled="saving">
                <span v-if="saving" class="spinner-border spinner-border-sm me-2"></span> Save
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { Modal } from 'bootstrap'
import API from '../../../API/API'
import Swal from 'sweetalert2'
import { notify } from '@kyvg/vue3-notification'

export default {
  name: 'BranchesView',
  data() {
    return {
      api: new API('api/v1/core/branch'), areasApi: new API('api/v1/core/area'),
      items: [], areas: [], editingId: null,
      form: { name: '', code: '', area_id: '' }, saving: false, modal: null,
    }
  },
  mounted() { this.loadItems(); this.loadAreas(); this.modal = new Modal(this.$refs.modalEl) },
  methods: {
    async loadItems() { try { const d = await this.api.getAll(); this.items = d.data || d } catch { notify({ text: 'Failed to load branches', type: 'error' }) } },
    async loadAreas() { try { const d = await this.areasApi.getAll({ per_page: -1 }); this.areas = d.data || d } catch {} },
    openForm() { this.editingId = null; this.form = { name: '', code: '', area_id: '' }; this.modal.show() },
    editItem(item) { this.editingId = item.id; this.form = { name: item.name, code: item.code || '', area_id: item.area_id }; this.modal.show() },
    async saveItem() {
      this.saving = true
      try {
        const fd = new FormData(); fd.append('name', this.form.name); fd.append('area_id', this.form.area_id)
        if (this.form.code) fd.append('code', this.form.code)
        if (this.editingId) await this.api.update(this.editingId, fd); else await this.api.insert(fd)
        notify({ text: this.editingId ? 'Branch updated' : 'Branch created', type: 'success' })
        this.modal.hide(); this.loadItems()
      } catch (e) { notify({ text: e.response?.data?.message || 'Error saving branch', type: 'error' }) } finally { this.saving = false }
    },
    async deleteItem(id) {
      if ((await Swal.fire({ title: 'Delete Branch?', text: 'This action cannot be undone.', icon: 'warning', showCancelButton: true, confirmButtonText: 'Delete' })).isConfirmed) {
        try { await this.api.delete(id); this.loadItems() } catch { notify({ text: 'Failed to delete', type: 'error' }) }
      }
    }
  }
}
</script>
