<template>
  <div class="card card-flush">
    <div class="card-header pt-7">
      <h3 class="card-title align-items-start flex-column">
        <span class="card-label fw-bold text-dark">Settings</span>
        <span class="text-gray-400 mt-1 fw-semibold fs-6">Manage company settings</span>
      </h3>
    </div>
    <div class="card-body pt-0">
      <div v-if="loading" class="text-center py-10">
        <div class="spinner-border text-primary"></div>
      </div>
      <form v-else @submit.prevent="saveItem" class="mw-700px mx-auto">
        <div class="fv-row mb-7">
          <label class="required fs-6 fw-semibold mb-2">Company Name</label>
          <input type="text" v-model="form.companyName" class="form-control form-control-solid" required />
        </div>
        <div class="fv-row mb-7">
          <label class="required fs-6 fw-semibold mb-2">Company Phone</label>
          <input type="text" v-model="form.companyPhone" class="form-control form-control-solid" required />
        </div>
        <div class="fv-row mb-7">
          <label class="required fs-6 fw-semibold mb-2">Company Address</label>
          <input type="text" v-model="form.companyAdress" class="form-control form-control-solid" required />
        </div>
        <div class="fv-row mb-7">
          <label class="required fs-6 fw-semibold mb-2">Currency</label>
          <select v-model="form.currency_id" class="form-select form-select-solid" required>
            <option value="" disabled>Select currency</option>
            <option v-for="c in currencies" :key="c.id" :value="c.id">{{ c.name }} ({{ c.code }})</option>
          </select>
        </div>
        <div class="fv-row mb-7">
          <label class="required fs-6 fw-semibold mb-2">Client</label>
          <select v-model="form.client_id" class="form-select form-select-solid" required>
            <option value="" disabled>Select client</option>
            <option v-for="c in clients" :key="c.id" :value="c.id">{{ c.name }}</option>
          </select>
        </div>
        <div class="fv-row mb-7">
          <label class="required fs-6 fw-semibold mb-2">Warehouse</label>
          <select v-model="form.warehouse_id" class="form-select form-select-solid" required>
            <option value="" disabled>Select warehouse</option>
            <option v-for="w in warehouses" :key="w.id" :value="w.id">{{ w.name }}</option>
          </select>
        </div>
        <div class="fv-row mb-7">
          <label class="fs-6 fw-semibold mb-2">Developed By</label>
          <input type="text" v-model="form.developed_by" class="form-control form-control-solid" />
        </div>
        <div class="fv-row mb-7">
          <label class="fs-6 fw-semibold mb-2">Footer</label>
          <input type="text" v-model="form.footer" class="form-control form-control-solid" />
        </div>
        <div class="fv-row mb-7">
          <label class="fs-6 fw-semibold mb-2">Logo</label>
          <div v-if="form.imagePreview" class="mb-3">
            <img :src="form.imagePreview" class="rounded border" style="max-width:150px;max-height:150px;object-fit:cover" />
          </div>
          <input type="file" ref="imageInput" accept="image/png,image/jpg,image/jpeg" class="form-control form-control-solid" @change="onImageChange" />
        </div>
        <div class="text-center pt-5">
          <button type="submit" class="btn btn-primary" :disabled="saving">
            <span v-if="saving" class="spinner-border spinner-border-sm me-2"></span>Save Settings
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import Setting from '../../../API/Modules/Inventory/Setting/Setting'
import Currency from '../../../API/Modules/Inventory/Currency/Currency'
import Client from '../../../API/Modules/Inventory/Client/Client'
import Warehouse from '../../../API/Modules/Core/Warehouse/Warehouse'
import { notify } from '@kyvg/vue3-notification'

export default {
  name: 'SettingsView',
  data() {
    return {
      api: new Setting('api/v1/inventory/setting'),
      currencyApi: new Currency('api/v1/inventory/currency'),
      clientApi: new Client('api/v1/inventory/client'),
      warehouseApi: new Warehouse('api/v1/core/warehouse'),
      currencies: [], clients: [], warehouses: [],
      editingId: null, loading: true, saving: false,
      form: { companyName: '', companyPhone: '', companyAdress: '', currency_id: '', client_id: '', warehouse_id: '', developed_by: '', footer: '', imagePreview: null, imageFile: null },
    }
  },
  async mounted() {
    await Promise.all([this.loadSetting(), this.loadCurrencies(), this.loadClients(), this.loadWarehouses()])
    this.loading = false
  },
  methods: {
    resetFileInputs() {
      if (this.$refs.imageInput) this.$refs.imageInput.value = ''
    },
    async loadSetting() {
      try { const d = await this.api.getAll(); if (d) { this.editingId = d.id; this.form.companyName = d.companyName || ''; this.form.companyPhone = d.companyPhone || ''; this.form.companyAdress = d.companyAdress || ''; this.form.currency_id = d.currency_id || ''; this.form.client_id = d.client_id || ''; this.form.warehouse_id = d.warehouse_id || ''; this.form.developed_by = d.developed_by || ''; this.form.footer = d.footer || ''; this.form.imagePreview = d.image || null } }
      catch { notify({ text: 'Failed to load settings', type: 'error' }) }
    },
    async loadCurrencies() { try { const d = await this.currencyApi.getAll({ per_page: -1 }); this.currencies = d.data || d } catch {} },
    async loadClients() { try { const d = await this.clientApi.getAll({ per_page: -1 }); this.clients = d.data || d } catch {} },
    async loadWarehouses() { try { const d = await this.warehouseApi.getAll({ per_page: -1 }); this.warehouses = d.data || d } catch {} },
    onImageChange(e) {
      const file = e.target.files[0]
      if (file) { this.form.imageFile = file; this.form.imagePreview = URL.createObjectURL(file) }
    },
    async saveItem() {
      this.saving = true
      try {
        const fd = new FormData()
        fd.append('companyName', this.form.companyName)
        fd.append('companyPhone', this.form.companyPhone)
        fd.append('companyAdress', this.form.companyAdress)
        fd.append('currency_id', this.form.currency_id)
        fd.append('client_id', this.form.client_id)
        fd.append('warehouse_id', this.form.warehouse_id)
        fd.append('developed_by', this.form.developed_by)
        fd.append('footer', this.form.footer)
        if (this.form.imageFile) { fd.append('image', this.form.imageFile) }
        if (this.editingId) await this.api.update(this.editingId, fd); else await this.api.insert(fd)
        notify({ text: 'Settings saved successfully', type: 'success' })
        this.resetFileInputs()
        this.saving = false;
        this.loadSetting()
      } catch (e) {
        const errors = e.response?.data?.errors
        if (errors) { Object.entries(errors).forEach(([field, msgs]) => msgs.forEach(msg => notify({ text: field + ': ' + msg, type: 'error' }))) }
        else { notify({ text: e.response?.data?.message || 'Error saving settings', type: 'error' }) }
        this.saving = false
      }
    },
  }
}
</script>
