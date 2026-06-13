<template>
  <div class="card card-flush">
    <div class="card-header pt-7">
      <h3 class="card-title align-items-start flex-column">
        <span class="card-label fw-bold text-dark">Warehouses</span>
        <span class="text-gray-400 mt-1 fw-semibold fs-6">Manage system warehouses</span>
      </h3>
      <div class="card-toolbar">
        <button type="button" class="btn btn-primary" @click="openForm()">
          <i class="ki-outline ki-plus fs-2"></i> Add Warehouse
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
              <th class="min-w-100px">Mobile</th>
              <th class="min-w-100px">Branch</th>
              <th class="min-w-100px">Area</th>
              <th class="min-w-100px">City</th>
              <th class="min-w-100px">Country</th>
              <th class="min-w-100px">Address</th>
              <th class="min-w-50px">Main</th>
              <th class="min-w-50px text-end">Status</th>
              <th class="min-w-50px text-end">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in items" :key="item.id">
              <td>{{ item.name }}</td>
              <td>{{ item.code }}</td>
              <td>{{ item.mobile }}</td>
              <td>{{ item.branch_name || '-' }}</td>
              <td>{{ item.area_name || '-' }}</td>
              <td>{{ item.city_name || '-' }}</td>
              <td>{{ item.country_name || '-' }}</td>
              <td>{{ item.address || '-' }}</td>
              <td>
                <span class="badge" :class="item.is_main ? 'badge-primary' : 'badge-secondary'">
                  {{ item.is_main ? 'Yes' : 'No' }}
                </span>
              </td>
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
              <td colspan="10" class="text-center text-muted py-10">No warehouses found</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    <div class="modal fade" tabindex="-1" ref="modalEl" data-bs-backdrop="static">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h3 class="modal-title">{{ editingId ? 'Edit Warehouse' : 'Add Warehouse' }}</h3>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form @submit.prevent="saveItem">
            <div class="modal-body">
              <div class="fv-row mb-7">
                <label class="required fs-6 fw-semibold mb-2">Name</label>
                <input type="text" v-model="form.name" class="form-control form-control-solid" required />
              </div>
              <div class="fv-row mb-7">
                <label class="required fs-6 fw-semibold mb-2">Mobile</label>
                <input type="number" v-model="form.mobile" class="form-control form-control-solid" required />
              </div>
              <div class="fv-row mb-7">
                <label class="required fs-6 fw-semibold mb-2">Branch</label>
                <select v-model="form.branch_id" class="form-select form-select-solid" required>
                  <option value="" disabled>Select branch</option>
                  <option v-for="b in branches" :key="b.id" :value="b.id">{{ b.name }}</option>
                </select>
              </div>
              <div class="fv-row mb-7">
                <label class="required fs-6 fw-semibold mb-2">Country</label>
                <select v-model="form.country_id" class="form-select form-select-solid" required>
                  <option value="" disabled>Select country</option>
                  <option v-for="c in countries" :key="c.id" :value="c.id">{{ c.name }}</option>
                </select>
              </div>
              <div class="fv-row mb-7">
                <label class="required fs-6 fw-semibold mb-2">City</label>
                <select v-model="form.city_id" class="form-select form-select-solid" required :disabled="!form.country_id">
                  <option value="" disabled>Select city</option>
                  <option v-for="c in cities" :key="c.id" :value="c.id">{{ c.name }}</option>
                </select>
              </div>
              <div class="fv-row mb-7">
                <label class="required fs-6 fw-semibold mb-2">Area</label>
                <select v-model="form.area_id" class="form-select form-select-solid" required :disabled="!form.city_id">
                  <option value="" disabled>Select area</option>
                  <option v-for="a in areas" :key="a.id" :value="a.id">{{ a.name }}</option>
                </select>
              </div>
              <div class="fv-row mb-7">
                <label class="fs-6 fw-semibold mb-2">Address</label>
                <input v-model="form.address" class="form-control form-control-solid" />
              </div>
              <div class="fv-row mb-7">
                <label class="fs-6 fw-semibold mb-2">
                  <input type="checkbox" v-model="form.is_main" class="form-check-input me-2" />
                  Main Warehouse
                </label>
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
import Warehouse from '../../../API/Modules/Core/Warehouse/Warehouse'
import Branch from '../../../API/Modules/Core/Branch/Branch'
import Country from '../../../API/Modules/Core/Country/Country'
import City from '../../../API/Modules/Core/City/City'
import Area from '../../../API/Modules/Core/Area/Area'
import Swal from 'sweetalert2'
import { notify } from '@kyvg/vue3-notification'

export default {
  name: 'WarehousesView',
  data() {
    return {
      api: new Warehouse('api/v1/core/warehouse'),
      branchesApi: new Branch('api/v1/core/branch'),
      countriesApi: new Country('api/v1/core/country'),
      citiesApi: new City('api/v1/core/city'),
      areasApi: new Area('api/v1/core/area'),
      items: [], countries: [], cities: [], areas: [], branches: [], editingId: null,
      form: { name: '', mobile: '', branch_id: '', country_id: '', city_id: '', area_id: '', address: '', is_main: false }, saving: false, modal: null, skipWatch: false,
    }
  },
  mounted() { this.loadItems(); this.loadCountries(); this.loadBranches(); this.modal = new Modal(this.$refs.modalEl) },
  watch: {
    'form.country_id': function(val) {
      if (this.skipWatch) return
      this.form.city_id = ''
      this.form.area_id = ''
      this.cities = []
      this.areas = []
      if (val) this.loadCities(val)
    },
    'form.city_id': function(val) {
      if (this.skipWatch) return
      this.form.area_id = ''
      this.areas = []
      if (val) this.loadAreas(val)
    }
  },
  methods: {
    async loadItems() { try { const d = await this.api.getAll(); this.items = d.data || d } catch { notify({ text: 'Failed to load warehouses', type: 'error' }) } },
    async loadCountries() { try { const d = await this.countriesApi.getAll({ per_page: -1 }); this.countries = d.data || d } catch {} },
    async loadBranches() { try { const d = await this.branchesApi.getAll({ per_page: -1 }); this.branches = d.data || d } catch {} },
    async loadCities(countryId) { try { const d = await this.citiesApi.getAll({ per_page: -1, country_id: countryId }); this.cities = d.data || d } catch {} },
    async loadAreas(cityId) { try { const d = await this.areasApi.getAll({ per_page: -1, city_id: cityId }); this.areas = d.data || d } catch {} },
    openForm() {
      this.editingId = null
      this.form = { name: '', mobile: '', branch_id: '', country_id: '', city_id: '', area_id: '', address: '', is_main: false }
      this.cities = []; this.areas = []
      this.modal.show()
    },
    async editItem(item) {
      this.editingId = item.id
      this.skipWatch = true
      this.form.name = item.name
      this.form.mobile = item.mobile || ''
      this.form.branch_id = item.branch_id || ''
      this.form.address = item.address || ''
      this.form.is_main = !!item.is_main
      this.form.country_id = item.country_id || ''
      this.form.city_id = item.city_id || ''
      this.form.area_id = item.area_id
      if (this.form.country_id) await this.loadCities(this.form.country_id)
      if (this.form.city_id) await this.loadAreas(this.form.city_id)
      this.skipWatch = false
      this.modal.show()
    },
    async saveItem() {
      this.saving = true
      try {
        const fd = new FormData()
        fd.append('name', this.form.name)
        fd.append('mobile', this.form.mobile)
        fd.append('branch_id', this.form.branch_id)
        fd.append('area_id', this.form.area_id)
        if (this.form.address) fd.append('address', this.form.address)
        fd.append('is_main', this.form.is_main ? '1' : '0')
        if (this.editingId) await this.api.update(this.editingId, fd); else await this.api.insert(fd)
        notify({ text: this.editingId ? 'Warehouse updated' : 'Warehouse created', type: 'success' })
        this.modal.hide();
        this.saving = false;
        this.loadItems()
      } catch (e) {
        const errors = e.response?.data?.errors
        if (errors) { Object.entries(errors).forEach(([field, msgs]) => msgs.forEach(msg => notify({ text: field + ': ' + msg, type: 'error' }))) }
        else { notify({ text: e.response?.data?.message || 'Error saving warehouse', type: 'error' }) }
        this.saving = false
      }
    },
    async deleteItem(id) {
      if ((await Swal.fire({ title: 'Delete Warehouse?', text: 'This action cannot be undone.', icon: 'warning', showCancelButton: true, confirmButtonText: 'Delete' })).isConfirmed) {
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
