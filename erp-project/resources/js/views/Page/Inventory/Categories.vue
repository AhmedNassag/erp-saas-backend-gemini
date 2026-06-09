<template>
  <div class="card card-flush">
    <div class="card-header pt-7">
      <h3 class="card-title align-items-start flex-column">
        <span class="card-label fw-bold text-dark">Categories</span>
        <span class="text-gray-400 mt-1 fw-semibold fs-6">Manage system categories</span>
      </h3>
      <div class="card-toolbar">
        <button type="button" class="btn btn-primary" @click="openForm()">
          <i class="ki-outline ki-plus fs-2"></i> Add Category
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
              <th class="min-w-100px">Type</th>
              <th class="min-w-150px">Parent</th>
              <th class="min-w-50px text-end">Status</th>
              <th class="min-w-50px text-end">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in items" :key="item.id">
              <td>{{ item.name }}</td>
              <td>{{ item.code }}</td>
              <td>
                <span class="badge" :class="item.is_main ? 'badge-primary' : 'badge-info'">
                  {{ item.is_main ? 'Main' : 'Sub' }}
                </span>
              </td>
              <td>{{ item.parent_name || '-' }}</td>
              <td>
                <span class="badge" :class="item.status ? 'badge-success' : 'badge-danger'" style="cursor:pointer" @click="changeStatus(item)">
                  {{ item.status ? 'Active' : 'Inactive' }}
                </span>
              </td>
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
              <td colspan="6" class="text-center text-muted py-10">No categories found</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    <div class="modal fade" tabindex="-1" ref="modalEl" data-bs-backdrop="static">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h3 class="modal-title">{{ editingId ? 'Edit Category' : 'Add Category' }}</h3>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form @submit.prevent="saveItem">
            <div class="modal-body">
              <div class="fv-row mb-7">
                <label class="required fs-6 fw-semibold mb-2">Name</label>
                <input type="text" v-model="form.name" class="form-control form-control-solid" required />
              </div>
              <div class="fv-row mb-7">
                <label class="required fs-6 fw-semibold mb-2">Code</label>
                <input type="text" v-model="form.code" class="form-control form-control-solid" required />
              </div>
              <div class="fv-row mb-7">
                <div class="form-check form-switch form-check-custom form-check-solid">
                  <label class="form-check-label fs-6 fw-semibold me-3">Main Category</label>
                  <input type="checkbox" v-model="form.is_main" class="form-check-input" @change="onIsMainChange" />
                </div>
              </div>
              <div class="fv-row mb-7" v-if="!form.is_main">
                <label class="required fs-6 fw-semibold mb-2">Parent Category</label>
                <select v-model="form.category_id" class="form-select form-select-solid" required>
                  <option value="" disabled>Select parent category</option>
                  <option v-for="p in parentCategories" :key="p.id" :value="p.id">{{ p.name }}</option>
                </select>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
              <button type="submit" class="btn btn-primary" :disabled="saving">
                <span v-if="saving" class="spinner-border spinner-border-sm me-2"></span>Save
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
import Category from '../../../API/Modules/Inventory/Category/Category'
import Swal from 'sweetalert2'
import { notify } from '@kyvg/vue3-notification'

export default {
  name: 'CategoriesView',
  data() {
    return {
      api: new Category('api/v1/inventory/category'),
      items: [], parentCategories: [], editingId: null,
      form: { name: '', code: '', is_main: false, category_id: '' }, saving: false, modal: null,
    }
  },
  mounted() { this.loadItems(); this.loadParents(); this.modal = new Modal(this.$refs.modalEl) },
  methods: {
    async loadItems() { try { const d = await this.api.getAll(); this.items = d.data || d } catch { notify({ text: 'Failed to load categories', type: 'error' }) } },
    async loadParents() { try { const d = await this.api.getAll({ per_page: -1, is_main: true }); this.parentCategories = d.data || d } catch {} },
    onIsMainChange() {
      if (this.form.is_main) { this.form.category_id = '' }
    },
    openForm() {
      this.editingId = null
      this.form = { name: '', code: '', is_main: false, category_id: '' }
      this.modal.show()
    },
    async editItem(item) {
      this.editingId = item.id
      this.form.name = item.name
      this.form.code = item.code || ''
      this.form.is_main = !!item.is_main
      this.form.category_id = item.category_id || ''
      this.modal.show()
    },
    async saveItem() {
      this.saving = true
      try {
        const fd = new FormData()
        fd.append('name', this.form.name)
        fd.append('code', this.form.code)
        fd.append('is_main', this.form.is_main ? '1' : '0')
        if (!this.form.is_main) { fd.append('category_id', this.form.category_id) }
        if (this.editingId) await this.api.update(this.editingId, fd); else await this.api.insert(fd)
        notify({ text: this.editingId ? 'Category updated' : 'Category created', type: 'success' })
        this.modal.hide(); this.loadItems(); this.loadParents()
      } catch (e) {
        const errors = e.response?.data?.errors
        if (errors) { Object.entries(errors).forEach(([field, msgs]) => msgs.forEach(msg => notify({ text: field + ': ' + msg, type: 'error' }))) }
        else { notify({ text: e.response?.data?.message || 'Error saving category', type: 'error' }) }
        this.saving = false
      }
    },
    async deleteItem(id) {
      if ((await Swal.fire({ title: 'Delete Category?', text: 'This action cannot be undone.', icon: 'warning', showCancelButton: true, confirmButtonText: 'Delete' })).isConfirmed) {
        try { await this.api.delete(id); this.loadItems(); this.loadParents() } catch { notify({ text: 'Failed to delete', type: 'error' }) }
      }
    },
    async changeStatus(item) {
      try {
        const newStatus = item.status ? 0 : 1
        await this.api.changeStatus(item.id, { status: newStatus })
        item.status = newStatus
        notify({ text: 'Status updated', type: 'success' })
      } catch { notify({ text: 'Failed to update status', type: 'error' }) }
    },
  }
}
</script>
