<template>
  <div class="card card-flush">
    <div class="card-header pt-7">
      <h3 class="card-title align-items-start flex-column">
        <span class="card-label fw-bold text-dark">Expenses</span>
        <span class="text-gray-400 mt-1 fw-semibold fs-6">Manage system expenses</span>
      </h3>
      <div class="card-toolbar">
        <button type="button" class="btn btn-primary" @click="openForm()">
          <i class="ki-outline ki-plus fs-2"></i> Add Expense
        </button>
      </div>
    </div>
    <div class="card-body pt-0">
      <div class="table-responsive">
        <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
          <thead>
            <tr class="fw-bold text-muted">
              <th class="min-w-100px">Ref</th>
              <th class="min-w-100px">Date</th>
              <th class="min-w-150px">Details</th>
              <th class="min-w-100px">Amount</th>
              <th class="min-w-150px">Category</th>
              <th class="min-w-150px">Warehouse</th>
              <th class="min-w-50px text-end">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in items" :key="item.id">
              <td>{{ item.Ref }}</td>
              <td>{{ item.date }}</td>
              <td>{{ item.details }}</td>
              <td>{{ item.amount }}</td>
              <td>{{ item.expense_category_name || '-' }}</td>
              <td>{{ item.warehouse_name }}</td>
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
              <td colspan="7" class="text-center text-muted py-10">No expenses found</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    <div class="modal fade" tabindex="-1" ref="modalEl" data-bs-backdrop="static">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h3 class="modal-title">{{ editingId ? 'Edit Expense' : 'Add Expense' }}</h3>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form @submit.prevent="saveItem">
            <div class="modal-body">
              <div class="fv-row mb-7">
                <label class="required fs-6 fw-semibold mb-2">Date</label>
                <input type="date" v-model="form.date" class="form-control form-control-solid" required />
              </div>
              <div class="fv-row mb-7">
                <label class="required fs-6 fw-semibold mb-2">Amount</label>
                <input type="number" step="0.01" min="0" v-model="form.amount" class="form-control form-control-solid" required />
              </div>
              <div class="fv-row mb-7">
                <label class="fs-6 fw-semibold mb-2">Category</label>
                <select v-model="form.expense_category_id" class="form-select form-select-solid">
                  <option value="">Select category</option>
                  <option v-for="c in expenseCategories" :key="c.id" :value="c.id">{{ c.name }}</option>
                </select>
              </div>
              <div class="fv-row mb-7">
                <label class="required fs-6 fw-semibold mb-2">Warehouse</label>
                <select v-model="form.warehouse_id" class="form-select form-select-solid" required>
                  <option value="" disabled>Select warehouse</option>
                  <option v-for="w in warehouses" :key="w.id" :value="w.id">{{ w.name }}</option>
                </select>
              </div>
              <div class="fv-row">
                <label class="required fs-6 fw-semibold mb-2">Details</label>
                <textarea v-model="form.details" class="form-control form-control-solid" rows="3" placeholder="Enter the details..."></textarea>
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
import Expense from '../../../API/Modules/Inventory/Expense/Expense'
import ExpenseCategory from '../../../API/Modules/Inventory/ExpenseCategory/ExpenseCategory'
import Warehouse from '../../../API/Modules/Core/Warehouse/Warehouse'
import Swal from 'sweetalert2'
import { notify } from '@kyvg/vue3-notification'

export default {
  name: 'ExpensesView',
  data() {
    return {
      api: new Expense('api/v1/inventory/expense'),
      expenseCategoryApi: new ExpenseCategory('api/v1/inventory/expense-category'),
      warehouseApi: new Warehouse('api/v1/core/warehouse'),
      items: [], expenseCategories: [], warehouses: [], editingId: null,
      form: { date: '', details: '', amount: '', expense_category_id: '', warehouse_id: '' }, saving: false, modal: null,
    }
  },
  mounted() { this.loadItems(); this.loadExpenseCategories(); this.loadWarehouses(); this.modal = new Modal(this.$refs.modalEl) },
  methods: {
    async loadItems() { try { const d = await this.api.getAll(); this.items = d.data || d } catch { notify({ text: 'Failed to load expenses', type: 'error' }) } },
    async loadExpenseCategories() { try { const d = await this.expenseCategoryApi.getAll({ per_page: -1 }); this.expenseCategories = d.data || d } catch {} },
    async loadWarehouses() { try { const d = await this.warehouseApi.getAll({ per_page: -1 }); this.warehouses = d.data || d } catch {} },
    openForm() {
      this.editingId = null
      this.form = { date: '', details: '', amount: '', expense_category_id: '', warehouse_id: '' }
      this.modal.show()
    },
    async editItem(item) {
      this.editingId = item.id
      this.form.date = item.date
      this.form.details = item.details
      this.form.amount = item.amount
      this.form.expense_category_id = item.expense_category_id || ''
      this.form.warehouse_id = item.warehouse_id
      this.modal.show()
    },
    async saveItem() {
      this.saving = true
      try {
        const fd = new FormData()
        fd.append('date', this.form.date)
        fd.append('details', this.form.details)
        fd.append('amount', this.form.amount)
        if (this.form.expense_category_id) fd.append('expense_category_id', this.form.expense_category_id)
        fd.append('warehouse_id', this.form.warehouse_id)
        if (this.editingId) await this.api.update(this.editingId, fd); else await this.api.insert(fd)
        notify({ text: this.editingId ? 'Expense updated' : 'Expense created', type: 'success' })
        this.modal.hide();
        this.saving = false;
        this.loadItems()
      } catch (e) {
        const errors = e.response?.data?.errors
        if (errors) { Object.entries(errors).forEach(([field, msgs]) => msgs.forEach(msg => notify({ text: field + ': ' + msg, type: 'error' }))) }
        else { notify({ text: e.response?.data?.message || 'Error saving expense', type: 'error' }) }
        this.saving = false
      }
    },
    async deleteItem(id) {
      if ((await Swal.fire({ title: 'Delete Expense?', text: 'This action cannot be undone.', icon: 'warning', showCancelButton: true, confirmButtonText: 'Delete' })).isConfirmed) {
        try { await this.api.delete(id); this.loadItems() } catch { notify({ text: 'Failed to delete', type: 'error' }) }
      }
    },
  }
}
</script>
