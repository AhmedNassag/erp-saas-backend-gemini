<template>
  <div class="card card-flush">
    <div class="card-header pt-7">
      <h3 class="card-title align-items-start flex-column">
        <span class="card-label fw-bold text-dark">Expense Categories</span>
        <span class="text-gray-400 mt-1 fw-semibold fs-6">Manage expense categories</span>
      </h3>
      <div class="card-toolbar">
        <button type="button" class="btn btn-primary" @click="openForm()">
          <i class="ki-outline ki-plus fs-2"></i> Add Expense Category
        </button>
      </div>
    </div>
    <div class="card-body pt-0">
      <PaginationWrapper :currentPage="currentPage" :lastPage="lastPage" :total="total" :perPage="perPage" :search="search" @update:currentPage="onPageChange" @update:perPage="onPerPageChange" @update:search="onSearchChange">
      <div class="table-responsive">
        <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
          <thead>
            <tr class="fw-bold text-muted">
              <th class="min-w-150px">Name</th>
              <th class="min-w-200px">Description</th>
              <th class="min-w-50px">Status</th>
              <th class="min-w-50px text-end">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in items" :key="item.id">
              <td>{{ item.name }}</td>
              <td>{{ item.description || '-' }}</td>
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
              <td colspan="4" class="text-center text-muted py-10">No expense categories found</td>
            </tr>
          </tbody>
        </table>
      </div>
      </PaginationWrapper>
    </div>
    <div class="modal fade" tabindex="-1" ref="modalEl" data-bs-backdrop="static">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h3 class="modal-title">{{ editingId ? 'Edit Expense Category' : 'Add Expense Category' }}</h3>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form @submit.prevent="saveItem">
            <div class="modal-body">
              <div class="fv-row mb-7">
                <label class="required fs-6 fw-semibold mb-2">Name</label>
                <input type="text" v-model="form.name" class="form-control form-control-solid" required />
              </div>
              <div class="fv-row mb-7">
                <label class="fs-6 fw-semibold mb-2">Description</label>
                <textarea v-model="form.description" class="form-control form-control-solid" rows="3"></textarea>
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
import ExpenseCategory from '../../../API/Modules/Inventory/ExpenseCategory/ExpenseCategory'
import Swal from 'sweetalert2'
import { notify } from '@kyvg/vue3-notification'
import PaginationWrapper from '../../../components/PaginationWrapper.vue'

export default {
  name: 'ExpenseCategoriesView',
  components: { PaginationWrapper },
  data() {
    return {
      api: new ExpenseCategory('api/v1/inventory/expense-category'),
      items: [], editingId: null,
      currentPage: 1, lastPage: 1, total: 0, perPage: 10, search: '',
      form: { name: '', description: '' }, saving: false, modal: null,
    }
  },
  mounted() { this.loadItems(); this.modal = new Modal(this.$refs.modalEl) },
  methods: {
    async loadItems() {
      try {
        const params = { page: this.currentPage, per_page: this.perPage }
        if (this.search) params.search = this.search
        const d = await this.api.getAll(params)
        if (Array.isArray(d)) {
          this.items = d; this.total = d.length; this.lastPage = 1; this.currentPage = 1
        } else {
          this.items = d.data || []; this.total = d.total || 0; this.lastPage = d.lastPage || 1; this.currentPage = d.currentPage || 1
        }
      } catch { notify({ text: 'Failed to load expense categories', type: 'error' }) }
    },
    onPageChange(page) { this.currentPage = page; this.loadItems() },
    onPerPageChange(val) { this.perPage = val; this.currentPage = 1; this.loadItems() },
    onSearchChange(val) { this.search = val; this.currentPage = 1; this.loadItems() },
    openForm() {
      this.editingId = null
      this.form = { name: '', description: '' }
      this.modal.show()
    },
    async editItem(item) {
      this.editingId = item.id
      this.form.name = item.name
      this.form.description = item.description || ''
      this.modal.show()
    },
    async saveItem() {
      this.saving = true
      try {
        const fd = new FormData()
        fd.append('name', this.form.name)
        if (this.form.description) fd.append('description', this.form.description)
        if (this.editingId) await this.api.update(this.editingId, fd); else await this.api.insert(fd)
        notify({ text: this.editingId ? 'Expense category updated' : 'Expense category created', type: 'success' })
        this.modal.hide();
        this.saving = false;
        this.loadItems()
      } catch (e) {
        const errors = e.response?.data?.errors
        if (errors) { Object.entries(errors).forEach(([field, msgs]) => msgs.forEach(msg => notify({ text: field + ': ' + msg, type: 'error' }))) }
        else { notify({ text: e.response?.data?.message || 'Error saving expense category', type: 'error' }) }
        this.saving = false
      }
    },
    async deleteItem(id) {
      if ((await Swal.fire({ title: 'Delete Expense Category?', text: 'This action cannot be undone.', icon: 'warning', showCancelButton: true, confirmButtonText: 'Delete' })).isConfirmed) {
        try { await this.api.delete(id); this.loadItems() } catch { notify({ text: 'Failed to delete', type: 'error' }) }
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
