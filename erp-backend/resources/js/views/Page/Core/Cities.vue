<template>
  <div class="card card-flush">
    <div class="card-header pt-7">
      <h3 class="card-title align-items-start flex-column">
        <span class="card-label fw-bold text-dark">Cities</span>
        <span class="text-gray-400 mt-1 fw-semibold fs-6">Manage system cities</span>
      </h3>
      <div class="card-toolbar">
        <button type="button" class="btn btn-primary" @click="openForm()">
          <i class="ki-outline ki-plus fs-2"></i> Add City
        </button>
      </div>
    </div>
    <div class="card-body pt-0">
      <div class="table-responsive">
        <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
          <thead>
            <tr class="fw-bold text-muted">
              <th class="min-w-150px">Name</th>
              <th class="min-w-100px">Country</th>
              <th class="min-w-100px">Status</th>
              <th class="min-w-50px text-end">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in items" :key="item.id">
              <td>{{ item.name }}</td>
              <td>{{ item.country_name || '-' }}</td>
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
              <td colspan="3" class="text-center text-muted py-10">No cities found</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    <div class="modal fade" tabindex="-1" ref="modalEl" data-bs-backdrop="static">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h3 class="modal-title">{{ editingId ? 'Edit City' : 'Add City' }}</h3>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form @submit.prevent="saveItem">
            <div class="modal-body">
              <div class="fv-row mb-7">
                <label class="required fs-6 fw-semibold mb-2">Name</label>
                <input type="text" v-model="form.name" class="form-control form-control-solid" required />
              </div>
              <div class="fv-row mb-7">
                <label class="required fs-6 fw-semibold mb-2">Country</label>
                <select v-model="form.country_id" class="form-select form-select-solid" required>
                  <option value="" disabled>Select country</option>
                  <option v-for="c in countries" :key="c.id" :value="c.id">{{ c.name }}</option>
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
import City from '../../../API/Modules/Core/City/City'
import Country from '../../../API/Modules/Core/Country/Country'
import Swal from 'sweetalert2'
import { notify } from '@kyvg/vue3-notification'

export default {
  name: 'CitiesView',
  data() {
    return {
      api: new City('api/v1/core/city'),
      countriesApi: new Country('api/v1/core/country'),
      items: [], countries: [],
      editingId: null,
      form: { name: '', country_id: '' },
      saving: false, modal: null,
    }
  },
  mounted() { this.loadItems(); this.loadCountries(); this.modal = new Modal(this.$refs.modalEl) },
  methods: {
    async loadItems() { try { const d = await this.api.getAll(); this.items = d.data || d } catch { notify({ text: 'Failed to load cities', type: 'error' }) } },
    async loadCountries() { try { const d = await this.countriesApi.getAll({ per_page: -1 }); this.countries = d.data || d } catch {} },
    openForm() { this.editingId = null; this.form = { name: '', country_id: '' }; this.modal.show() },
    editItem(item) { this.editingId = item.id; this.form = { name: item.name, country_id: item.country_id || item.country?.id }; this.modal.show() },
    async saveItem() {
      this.saving = true
      try {
        const fd = new FormData(); fd.append('name', this.form.name); fd.append('country_id', this.form.country_id)
        if (this.editingId) await this.api.update(this.editingId, fd); else await this.api.insert(fd)
        notify({ text: this.editingId ? 'City updated' : 'City created', type: 'success' })
        this.modal.hide(); this.loadItems()
      } catch (e) { notify({ text: e.response?.data?.message || 'Error saving city', type: 'error' }) } finally { this.saving = false }
    },
    async deleteItem(id) {
      if ((await Swal.fire({ title: 'Delete City?', text: 'This action cannot be undone.', icon: 'warning', showCancelButton: true, confirmButtonText: 'Delete' })).isConfirmed) {
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
