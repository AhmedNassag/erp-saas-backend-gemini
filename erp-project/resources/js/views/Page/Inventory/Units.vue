<template>
  <div class="card card-flush">
    <div class="card-header pt-7">
      <h3 class="card-title align-items-start flex-column">
        <span class="card-label fw-bold text-dark">Units</span>
        <span class="text-gray-400 mt-1 fw-semibold fs-6">Manage system units</span>
      </h3>
      <div class="card-toolbar">
        <button type="button" class="btn btn-primary" @click="openForm()">
          <i class="ki-outline ki-plus fs-2"></i> Add Unit
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
              <th class="min-w-100px">Short Name</th>
              <th class="min-w-100px">Base Unit</th>
              <th class="min-w-80px">Operator</th>
              <th class="min-w-80px">Value</th>
              <th class="min-w-50px text-end">Status</th>
              <th class="min-w-50px text-end">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in items" :key="item.id">
              <td>{{ item.name }}</td>
              <td>{{ item.code }}</td>
              <td>{{ item.shortName }}</td>
              <td>{{ item.base_unit_name || '-' }}</td>
              <td>{{ item.operator || '*' }}</td>
              <td>{{ item.base_unit ? item.operator_value : '-' }}</td>
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
              <td colspan="8" class="text-center text-muted py-10">No units found</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    <div class="modal fade" tabindex="-1" ref="modalEl" data-bs-backdrop="static">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h3 class="modal-title">{{ editingId ? 'Edit Unit' : 'Add Unit' }}</h3>
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
                <label class="required fs-6 fw-semibold mb-2">Short Name</label>
                <input type="text" v-model="form.shortName" class="form-control form-control-solid" required />
              </div>
              <div class="fv-row mb-7">
                <label class="fs-6 fw-semibold mb-2">Base Unit</label>
                <select v-model="form.base_unit" class="form-select form-select-solid" @change="onBaseUnitChange">
                  <option value="">-- None (base unit) --</option>
                  <option v-for="u in baseUnits" :key="u.id" :value="u.id">{{ u.name }}</option>
                </select>
              </div>
              <div class="fv-row mb-7" v-if="showOperator">
                <label class="required fs-6 fw-semibold mb-2">Operator</label>
                <select v-model="form.operator" class="form-select form-select-solid" required>
                  <option value="*">Multiply (*)</option>
                  <option value="/">Divide (/)</option>
                </select>
              </div>
              <div class="fv-row mb-7" v-if="showOperator">
                <label class="required fs-6 fw-semibold mb-2">Operator Value</label>
                <input type="number" step="any" v-model="form.operator_value" class="form-control form-control-solid" required />
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
import Unit from '../../../API/Modules/Inventory/Unit/Unit'
import Swal from 'sweetalert2'
import { notify } from '@kyvg/vue3-notification'

export default {
  name: 'UnitsView',
  data() {
    return {
      api: new Unit('api/v1/inventory/unit'),
      items: [], baseUnits: [], editingId: null, showOperator: false,
      form: { name: '', code: '', shortName: '', base_unit: '', operator: '*', operator_value: 1 }, saving: false, modal: null,
    }
  },
  mounted() { this.loadItems(); this.loadBaseUnits(); this.modal = new Modal(this.$refs.modalEl) },
  methods: {
    async loadItems() { try { const d = await this.api.getAll(); this.items = d.data || d } catch { notify({ text: 'Failed to load units', type: 'error' }) } },
    async loadBaseUnits() { try { const d = await this.api.getAll({ per_page: -1 }); this.baseUnits = d.data || d } catch {} },
    onBaseUnitChange() {
      this.showOperator = !!this.form.base_unit
      if (!this.form.base_unit) { this.form.operator = '*'; this.form.operator_value = 1 }
    },
    openForm() {
      this.editingId = null
      this.form = { name: '', code: '', shortName: '', base_unit: '', operator: '*', operator_value: 1 }
      this.showOperator = false
      this.modal.show()
    },
    async editItem(item) {
      this.editingId = item.id
      this.form.name = item.name
      this.form.code = item.code || ''
      this.form.shortName = item.shortName || ''
      this.form.base_unit = item.base_unit || ''
      this.form.operator = item.operator || '*'
      this.form.operator_value = item.operator_value ?? 1
      this.showOperator = !!this.form.base_unit
      this.modal.show()
    },
    async saveItem() {
      this.saving = true
      try {
        const fd = new FormData()
        fd.append('name', this.form.name)
        fd.append('code', this.form.code)
        fd.append('shortName', this.form.shortName)
        if (this.form.base_unit) { fd.append('base_unit', this.form.base_unit); fd.append('operator', this.form.operator); fd.append('operator_value', this.form.operator_value) }
        if (this.editingId) await this.api.update(this.editingId, fd); else await this.api.insert(fd)
        notify({ text: this.editingId ? 'Unit updated' : 'Unit created', type: 'success' })
        this.modal.hide(); this.loadItems(); this.loadBaseUnits()
      } catch (e) {
        const errors = e.response?.data?.errors
        if (errors) { Object.entries(errors).forEach(([field, msgs]) => msgs.forEach(msg => notify({ text: field + ': ' + msg, type: 'error' }))) }
        else { notify({ text: e.response?.data?.message || 'Error saving unit', type: 'error' }) }
        this.saving = false
      }
    },
    async deleteItem(id) {
      if ((await Swal.fire({ title: 'Delete Unit?', text: 'This action cannot be undone.', icon: 'warning', showCancelButton: true, confirmButtonText: 'Delete' })).isConfirmed) {
        try { await this.api.delete(id); this.loadItems(); this.loadBaseUnits() } catch { notify({ text: 'Failed to delete', type: 'error' }) }
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
