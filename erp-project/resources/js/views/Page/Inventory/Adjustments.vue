<template>
  <div class="card card-flush">
    <div class="card-header pt-7">
      <h3 class="card-title align-items-start flex-column">
        <span class="card-label fw-bold text-dark">Stock Adjustments</span>
        <span class="text-gray-400 mt-1 fw-semibold fs-6">Manage inventory stock adjustments</span>
      </h3>
      <div class="card-toolbar">
        <button type="button" class="btn btn-primary" @click="openForm()">
          <i class="ki-outline ki-plus fs-2"></i> Add Adjustment
        </button>
      </div>
    </div>
    <div class="card-body pt-0">
      <PaginationWrapper :currentPage="currentPage" :lastPage="lastPage" :total="total" :perPage="perPage" :search="search" @update:currentPage="onPageChange" @update:perPage="onPerPageChange" @update:search="onSearchChange">
      <div class="table-responsive">
        <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
          <thead>
            <tr class="fw-bold text-muted">
              <th class="min-w-100px">Date</th>
              <th class="min-w-120px">Reference</th>
              <th class="min-w-150px">Warehouse</th>
              <th class="min-w-150px">User</th>
              <th class="min-w-50px text-end">Items</th>
              <th class="min-w-50px text-end">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in items" :key="item.id">
              <td>{{ item.date }}</td>
              <td>{{ item.Ref }}</td>
              <td>{{ item.warehouse_name }}</td>
              <td>{{ item.user_name }}</td>
              <td class="text-end">{{ item.items }}</td>
              <td class="text-end">
                <button class="btn btn-icon btn-bg-light btn-active-color-info btn-sm me-1" @click="showDetail(item.id)">
                  <i class="ki-outline ki-eye fs-2"></i>
                </button>
                <button class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1" @click="editItem(item.id)">
                  <i class="ki-outline ki-pencil fs-2"></i>
                </button>
                <button class="btn btn-icon btn-bg-light btn-active-color-danger btn-sm" @click="deleteItem(item.id)">
                  <i class="ki-outline ki-trash fs-2"></i>
                </button>
              </td>
            </tr>
            <tr v-if="!items.length">
              <td colspan="5" class="text-center text-muted py-10">No adjustments found</td>
            </tr>
          </tbody>
        </table>
      </div>
      </PaginationWrapper>
    </div>

    <div class="modal fade" tabindex="-1" ref="detailModalEl" data-bs-backdrop="static">
      <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow-sm">
          <div class="modal-header border-bottom-0 pb-0">
            <div class="d-flex align-items-center">
              <span class="symbol symbol-40 symbol-circle bg-light-primary me-3">
                <i class="ki-outline ki-information-3 fs-2 text-primary"></i>
              </span>
              <div>
                <h3 class="modal-title fw-bold text-gray-900 mb-0">Adjustment Details</h3>
                <span class="text-gray-500 fs-7">Reference #{{ detailAdjustment.Ref }}</span>
              </div>
            </div>
            <button type="button" class="btn btn-sm btn-icon btn-bg-light btn-active-color-primary" data-bs-dismiss="modal">
              <i class="ki-outline ki-cross fs-2"></i>
            </button>
          </div>
          <div class="modal-body pt-6">
            <div class="row g-5 mb-8">
              <div class="col-md-4">
                <div class="card card-dashed card-flush bg-light rounded-3 p-5 h-100">
                  <div class="d-flex align-items-center mb-2">
                    <i class="ki-outline ki-calendar fs-3 text-primary me-2"></i>
                    <span class="text-gray-600 fs-7 fw-semibold">Date</span>
                  </div>
                  <span class="fw-bold fs-4 text-gray-900">{{ detailAdjustment.date }}</span>
                </div>
              </div>
              <div class="col-md-4">
                <div class="card card-dashed card-flush bg-light rounded-3 p-5 h-100">
                  <div class="d-flex align-items-center mb-2">
                    <i class="ki-outline ki-shop fs-3 text-primary me-2"></i>
                    <span class="text-gray-600 fs-7 fw-semibold">Warehouse</span>
                  </div>
                  <span class="fw-bold fs-4 text-gray-900">{{ detailAdjustment.warehouse_name }}</span>
                </div>
              </div>
              <div class="col-md-4">
                <div class="card card-dashed card-flush bg-light rounded-3 p-5 h-100">
                  <div class="d-flex align-items-center mb-2">
                    <i class="ki-outline ki-profile-circle fs-3 text-primary me-2"></i>
                    <span class="text-gray-600 fs-7 fw-semibold">Created By</span>
                  </div>
                  <div class="d-flex align-items-center">
                    <span class="symbol symbol-30 symbol-circle bg-light-info me-2">
                      <span class="symbol-label fw-bold fs-8 text-info">{{ detailAdjustment.user_name ? detailAdjustment.user_name.charAt(0).toUpperCase() : '?' }}</span>
                    </span>
                    <span class="fw-bold fs-4 text-gray-900">{{ detailAdjustment.user_name || 'N/A' }}</span>
                  </div>
                </div>
              </div>
              <div v-if="detailAdjustment.notes" class="col-12">
                <div class="card card-dashed card-flush bg-light-warning rounded-3 p-5">
                  <div class="d-flex align-items-center mb-2">
                    <i class="ki-outline ki-notepad fs-3 text-warning me-2"></i>
                    <span class="text-warning fs-7 fw-semibold">Notes</span>
                  </div>
                  <span class="fw-semibold fs-5 text-gray-800">{{ detailAdjustment.notes }}</span>
                </div>
              </div>
            </div>

            <div class="d-flex align-items-center justify-content-between mb-4">
              <h5 class="text-gray-800 fw-bold mb-0">
                <i class="ki-outline ki-element-11 fs-3 text-primary me-1"></i>
                Adjusted Products
              </h5>
              <span class="badge badge-primary fs-7 px-3 py-2 rounded-pill">{{ detailRows.length }} item{{ detailRows.length !== 1 ? 's' : '' }}</span>
            </div>

            <div class="table-responsive rounded-3 border">
              <table class="table table-striped align-middle mb-0">
                <thead class="bg-gray-100">
                  <tr>
                    <th class="ps-6 fw-bold text-gray-700 min-w-40px">#</th>
                    <th class="fw-bold text-gray-700 min-w-160px">Product</th>
                    <th class="fw-bold text-gray-700 min-w-110px">Code</th>
                    <th class="fw-bold text-gray-700 text-end pe-6 min-w-90px">Qty</th>
                    <th class="fw-bold text-gray-700 text-center min-w-110px">Type</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(d, idx) in detailRows" :key="idx">
                    <td class="ps-6 text-gray-500">{{ idx + 1 }}</td>
                    <td>
                      <div class="d-flex align-items-center">
                        <span class="symbol symbol-35 symbol-circle bg-light-info me-3">
                          <i class="ki-outline ki-box fs-3 text-info"></i>
                        </span>
                        <span class="fw-semibold text-gray-800 text-hover-primary cursor-pointer">{{ d.name }}</span>
                      </div>
                    </td>
                    <td><span class="badge badge-light-info fs-8 px-2 py-1">{{ d.code }}</span></td>
                    <td class="text-end pe-6">
                      <span class="fw-bold fs-6">{{ d.quantity }} <span class="fw-normal text-gray-500 fs-7">{{ d.unit }}</span></span>
                    </td>
                    <td class="text-center">
                      <span class="badge fs-7 px-3 py-2 rounded-pill" :class="d.type === 'add' ? 'badge-light-success' : 'badge-light-danger'">
                        <i class="ki-outline fs-6 me-1" :class="d.type === 'add' ? 'ki-plus' : 'ki-minus'"></i>
                        {{ d.type === 'add' ? 'Addition' : 'Subtraction' }}
                      </span>
                    </td>
                  </tr>
                  <tr v-if="!detailRows.length">
                    <td colspan="5" class="text-center text-gray-500 py-8">
                      <i class="ki-outline ki-box fs-3x text-gray-300 d-block mb-3"></i>
                      No products in this adjustment
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
          <div class="modal-footer border-top-0 pt-0">
            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" tabindex="-1" ref="modalEl" data-bs-backdrop="static">
      <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content shadow-sm">
          <div class="modal-header border-bottom-0 pb-0">
            <div class="d-flex align-items-center">
              <span class="symbol symbol-40 symbol-circle" :class="editingId ? 'bg-light-warning' : 'bg-light-success'">
                <i class="ki-outline fs-2" :class="editingId ? 'ki-pencil text-warning' : 'ki-plus-squared text-success'"></i>
              </span>
              <div class="ms-3">
                <h3 class="modal-title fw-bold text-gray-900 mb-0">{{ editingId ? 'Edit Adjustment' : 'Add Adjustment' }}</h3>
                <span class="text-gray-500 fs-7">{{ editingId ? 'Update stock adjustment details' : 'Create a new stock adjustment' }}</span>
              </div>
            </div>
            <button type="button" class="btn btn-sm btn-icon btn-bg-light btn-active-color-primary" data-bs-dismiss="modal">
              <i class="ki-outline ki-cross fs-2"></i>
            </button>
          </div>
          <form @submit.prevent="saveItem">
            <div class="modal-body pt-6">
              <div class="row g-5 mb-6">
                <div class="col-md-6">
                  <label class="required fs-6 fw-semibold mb-3 text-gray-700">Warehouse</label>
                  <div class="position-relative">
                    <i class="ki-outline ki-shop fs-2 position-absolute top-50 translate-middle-y ms-4 text-gray-500"></i>
                    <select v-model="form.warehouse_id" class="form-select form-select-solid ps-12" required :disabled="details.length > 0" @change="onWarehouseChange">
                      <option value="" disabled>Select warehouse</option>
                      <option v-for="w in warehouses" :key="w.id" :value="w.id">{{ w.name }}</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-6">
                  <label class="required fs-6 fw-semibold mb-3 text-gray-700">Date</label>
                  <div class="position-relative">
                    <i class="ki-outline ki-calendar fs-2 position-absolute top-50 translate-middle-y ms-4 text-gray-500"></i>
                    <input type="date" v-model="form.date" class="form-control form-control-solid ps-12" required />
                  </div>
                </div>
              </div>

              <div class="separator separator-dashed my-7"></div>

              <div class="mb-6">
                <label class="fs-6 fw-semibold mb-3 text-gray-700">
                  <i class="ki-outline ki-search-list fs-3 text-primary me-1"></i>
                  Product Search
                </label>
                <div class="position-relative">
                  <i class="ki-outline ki-search fs-2 position-absolute top-50 translate-middle-y ms-4 text-gray-500"></i>
                  <input type="text" class="form-control form-control-solid ps-12" v-model="searchInput" @input="onSearch" @focus="searchFocused = true" @blur="onSearchBlur" placeholder="Scan / Search product by code or name..." />
                  <ul v-if="searchFocused && searchResults.length" class="list-group position-absolute w-100 z-index-3 mt-1 rounded-3 shadow-sm" style="max-height:220px;overflow-y:auto">
                    <li class="list-group-item list-group-item-action d-flex align-items-center gap-3 border-0 border-bottom" v-for="r in searchResults" :key="r.id" @mousedown.prevent="selectProduct(r)" style="cursor:pointer">
                      <span class="symbol symbol-35 symbol-circle bg-light-info">
                        <i class="ki-outline ki-box fs-3 text-info"></i>
                      </span>
                      <div>
                        <span class="fw-semibold text-gray-800 d-block">{{ r.name }}</span>
                        <span class="text-gray-500 fs-7">{{ r.code }}</span>
                      </div>
                    </li>
                  </ul>
                </div>
              </div>

              <div class="d-flex align-items-center justify-content-between mb-4">
                <h5 class="text-gray-800 fw-bold mb-0">
                  <i class="ki-outline ki-element-11 fs-3 text-primary me-1"></i>
                  Products
                </h5>
                <span class="badge badge-primary fs-7 px-3 py-2 rounded-pill">{{ details.length }} item{{ details.length !== 1 ? 's' : '' }}</span>
              </div>

              <div class="table-responsive rounded-3 border mb-6">
                <table class="table table-striped align-middle mb-0">
                  <thead class="bg-gray-100">
                    <tr>
                      <th class="ps-6 fw-bold text-gray-700 min-w-40px">#</th>
                      <th class="fw-bold text-gray-700 min-w-130px">Code</th>
                      <th class="fw-bold text-gray-700 min-w-160px">Product</th>
                      <th class="fw-bold text-gray-700 text-center min-w-110px">Current Stock</th>
                      <th class="fw-bold text-gray-700 text-center min-w-160px">Qty</th>
                      <th class="fw-bold text-gray-700 text-center min-w-120px">Type</th>
                      <th class="fw-bold text-center min-w-50px"></th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-if="!details.length">
                      <td colspan="7" class="text-center text-gray-500 py-8">
                        <i class="ki-outline ki-box fs-3x text-gray-300 d-block mb-3"></i>
                        Search and select products to add
                      </td>
                    </tr>
                    <tr v-for="(d, i) in details" :key="d.detail_id" :class="{ 'bg-light-danger': d.del === 1 }">
                      <td class="ps-6 text-gray-500">{{ d.detail_id }}</td>
                      <td><span class="badge badge-light-info fs-8 px-2 py-1">{{ d.code }}</span></td>
                      <td>
                        <div class="d-flex align-items-center">
                          <span class="symbol symbol-30 symbol-circle bg-light-info me-2">
                            <i class="ki-outline ki-box fs-3 text-info"></i>
                          </span>
                          <span class="fw-semibold text-gray-800">{{ d.name }}</span>
                        </div>
                      </td>
                      <td class="text-center">
                        <span class="badge badge-light-warning fs-7 px-3 py-2 rounded-pill">{{ d.current }}</span>
                      </td>
                      <td class="text-center">
                        <div class="input-group input-group-sm justify-content-center flex-nowrap" style="max-width:150px;margin:0 auto">
                          <button class="btn btn-icon btn-sm btn-primary" type="button" @click="decrementQty(i)">
                            <i class="ki-outline ki-minus fs-3"></i>
                          </button>
                          <input type="number" min="1" step="1" v-model.number="d.quantity" class="form-control text-center fw-bold border-primary" style="max-width:65px" @input="verifyQty(i)" />
                          <button class="btn btn-icon btn-sm btn-primary" type="button" @click="incrementQty(i)">
                            <i class="ki-outline ki-plus fs-3"></i>
                          </button>
                        </div>
                      </td>
                      <td class="text-center">
                        <select v-model="d.type" class="form-select form-select-sm border-0 bg-light fw-semibold text-center px-3" :class="d.type === 'add' ? 'text-success' : 'text-danger'" @change="verifyQty(i)">
                          <option value="add" class="text-success">Addition</option>
                          <option value="sub" class="text-danger">Subtraction</option>
                        </select>
                      </td>
                      <td class="text-center">
                        <button class="btn btn-icon btn-sm btn-bg-light btn-active-color-danger" type="button" @click="removeDetail(i)">
                          <i class="ki-outline ki-trash fs-3 text-gray-500"></i>
                        </button>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>

              <div class="fv-row">
                <label class="fs-6 fw-semibold mb-3 text-gray-700">
                  <i class="ki-outline ki-notepad fs-3 text-primary me-1"></i>
                  Notes
                </label>
                <textarea v-model="form.notes" class="form-control form-control-solid" rows="3" placeholder="Enter any additional notes..."></textarea>
              </div>
            </div>
            <div class="modal-footer border-top-0 pt-0">
              <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
              <button type="submit" class="btn btn-primary" :disabled="saving">
                <span v-if="saving" class="spinner-border spinner-border-sm me-2"></span>
                <i v-else class="ki-outline ki-check-circle fs-6 me-1"></i>
                {{ editingId ? 'Update' : 'Save' }}
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
import Adjustment from '../../../API/Modules/Inventory/Adjustment/Adjustment'
import Warehouse from '../../../API/Modules/Core/Warehouse/Warehouse'
import Product from '../../../API/Modules/Inventory/Product/Product'
import Swal from 'sweetalert2'
import { notify } from '@kyvg/vue3-notification'
import PaginationWrapper from '../../../components/PaginationWrapper.vue'

export default {
  name: 'AdjustmentsView',
  components: { PaginationWrapper },
  data() {
    return {
      api: new Adjustment('api/v1/inventory/adjustment'),
      warehouseApi: new Warehouse('api/v1/core/warehouse'),
      productApi: new Product('api/v1/inventory/product'),
      items: [], warehouses: [], products: [],
      currentPage: 1, lastPage: 1, total: 0, perPage: 10, search: '',
      editingId: null, saving: false, modal: null, detailModal: null,
      searchInput: '', searchFocused: false, searchResults: [],
      timer: null,
      form: { warehouse_id: '', date: new Date().toISOString().slice(0, 10), notes: '' },
      details: [],
      detailAdjustment: {}, detailRows: [],
    }
  },
  mounted() {
    this.loadItems()
    this.modal = new Modal(this.$refs.modalEl)
    this.detailModal = new Modal(this.$refs.detailModalEl)
  },
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
      } catch { notify({ text: 'Failed to load adjustments', type: 'error' }) }
    },
    onPageChange(page) { this.currentPage = page; this.loadItems() },
    onPerPageChange(val) { this.perPage = val; this.currentPage = 1; this.loadItems() },
    onSearchChange(val) { this.search = val; this.currentPage = 1; this.loadItems() },
    resetForm() {
      this.form = { warehouse_id: '', date: new Date().toISOString().slice(0, 10), notes: '' }
      this.details = []
      this.searchInput = ''
      this.searchResults = []
      this.products = []
    },
    async openForm() {
      this.editingId = null
      this.resetForm()
      await this.loadWarehouses()
      this.modal.show()
    },
    async loadWarehouses() {
      try {
        const d = await this.warehouseApi.getAll({ per_page: 1000 })
        this.warehouses = Array.isArray(d) ? d : (d.data || [])
      } catch { notify({ text: 'Failed to load warehouses', type: 'error' }) }
    },
    async onWarehouseChange() {
      this.searchInput = ''
      this.searchResults = []
      this.products = []
      if (!this.form.warehouse_id) return
      try {
        const res = await this.productApi.byWarehouse(this.form.warehouse_id)
        this.products = Array.isArray(res) ? res : (res.data || [])
      } catch { notify({ text: 'Failed to load products', type: 'error' }) }
    },
    onSearch() {
      if (this.timer) clearTimeout(this.timer)
      if (this.searchInput.length < 1) { this.searchResults = []; return }
      if (!this.form.warehouse_id) { notify({ text: 'Please select a warehouse first', type: 'warning' }); return }
      this.timer = setTimeout(() => {
        const q = this.searchInput.toLowerCase()
        const exact = this.products.filter(p => p.code === this.searchInput || (p.barcode && p.barcode.includes(this.searchInput)))
        if (exact.length === 1) { this.selectProduct(exact[0]); return }
        this.searchResults = this.products.filter(p =>
          p.name.toLowerCase().startsWith(q) || p.code.toLowerCase().startsWith(q) || (p.barcode && p.barcode.toLowerCase().startsWith(q))
        )
      }, 800)
    },
    onSearchBlur() { setTimeout(() => { this.searchFocused = false }, 200) },
    selectProduct(p) {
      this.searchInput = ''
      this.searchResults = []
      this.searchFocused = false
      if (this.details.some(d => d.product_id === p.id && d.product_variant_id === (p.product_variant_id || null))) {
        notify({ text: 'Product already added', type: 'warning' }); return
      }
      const maxId = this.details.length ? Math.max(...this.details.map(d => d.detail_id)) : 0
      this.details.push({
        id: 0,
        detail_id: maxId + 1,
        product_id: p.id,
        product_variant_id: p.product_variant_id || null,
        code: p.code,
        name: p.name,
        current: p.qty || 0,
        quantity: 1,
        type: 'add',
        del: 0,
      })
    },
    verifyQty(i) {
      const d = this.details[i]
      d.quantity = Math.floor(Math.abs(d.quantity)) || 1
      if (d.type === 'sub' && d.quantity > d.current) {
        notify({ text: 'Quantity exceeds current stock', type: 'warning' })
        d.quantity = Math.min(d.current, 1)
      }
    },
    incrementQty(i) {
      const d = this.details[i]
      if (d.type === 'sub' && d.quantity + 1 > d.current) { notify({ text: 'Low stock', type: 'warning' }); return }
      d.quantity = (d.quantity || 0) + 1
    },
    decrementQty(i) {
      const d = this.details[i]
      if (d.quantity > 1) d.quantity--
    },
    removeDetail(i) { this.details.splice(i, 1) },
    verifiedForm() {
      if (!this.details.length) { notify({ text: 'Please add at least one product', type: 'warning' }); return false }
      if (this.details.some(d => !d.quantity || d.quantity <= 0)) { notify({ text: 'All products must have a valid quantity', type: 'warning' }); return false }
      return true
    },
    async editItem(id) {
      this.editingId = id
      this.resetForm()
      try {
        const res = await this.api.show(id)
        const d = res.data
        this.warehouses = d.warehouses || []
        this.form.warehouse_id = d.adjustment?.warehouse_id || ''
        this.form.date = d.adjustment?.date || ''
        this.form.notes = d.adjustment?.notes || ''
        this.details = (d.details || []).map((item, idx) => ({ ...item, detail_id: idx + 1 }))
        if (this.form.warehouse_id) {
          const prodRes = await this.productApi.byWarehouse(this.form.warehouse_id)
          this.products = Array.isArray(prodRes) ? prodRes : (prodRes.data || [])
        }
        this.modal.show()
      } catch { notify({ text: 'Failed to load adjustment', type: 'error' }) }
    },
    async showDetail(id) {
      try {
        const res = await this.api.show(id)
        const d = res.data
        this.detailAdjustment = d.adjustment || {}
        this.detailRows = d.details || []
        this.detailModal.show()
      } catch { notify({ text: 'Failed to load details', type: 'error' }) }
    },
    async saveItem() {
      if (!this.verifiedForm()) return
      this.saving = true
      try {
        const fd = new FormData()
        fd.append('warehouse_id', this.form.warehouse_id)
        fd.append('date', this.form.date)
        if (this.form.notes) fd.append('notes', this.form.notes)
        this.details.forEach((d, i) => {
          fd.append(`details[${i}][product_id]`, d.product_id)
          fd.append(`details[${i}][quantity]`, d.quantity)
          fd.append(`details[${i}][type]`, d.type)
          if (d.product_variant_id) fd.append(`details[${i}][product_variant_id]`, d.product_variant_id)
        })
        if (this.editingId) await this.api.update(this.editingId, fd); else await this.api.insert(fd)
        notify({ text: this.editingId ? 'Adjustment updated' : 'Adjustment created', type: 'success' })
        this.modal.hide()
        this.saving = false
        this.loadItems()
      } catch (e) {
        const errors = e.response?.data?.errors
        if (errors) { Object.entries(errors).forEach(([field, msgs]) => msgs.forEach(msg => notify({ text: field + ': ' + msg, type: 'error' }))) }
        else { notify({ text: e.response?.data?.message || 'Error saving adjustment', type: 'error' }) }
        this.saving = false
      }
    },
    async deleteItem(id) {
      if ((await Swal.fire({ title: 'Delete Adjustment?', text: 'This action cannot be undone.', icon: 'warning', showCancelButton: true, confirmButtonText: 'Delete' })).isConfirmed) {
        try { await this.api.delete(id); this.loadItems() }
        catch { notify({ text: 'Failed to delete', type: 'error' }) }
      }
    },
  },
}
</script>
