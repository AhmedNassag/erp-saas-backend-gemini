<template>
  <div class="card card-flush">
    <div class="card-header pt-7">
      <h3 class="card-title align-items-start flex-column">
        <span class="card-label fw-bold text-dark">Products</span>
        <span class="text-gray-400 mt-1 fw-semibold fs-6">Manage system products</span>
      </h3>
      <div class="card-toolbar">
        <button type="button" class="btn btn-primary" @click="openForm()">
          <i class="ki-outline ki-plus fs-2"></i> Add Product
        </button>
      </div>
    </div>
    <div class="card-body pt-0">
      <div class="table-responsive">
        <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
          <thead>
            <tr class="fw-bold text-muted">
              <th class="min-w-80px">Image</th>
              <th class="min-w-100px">Code</th>
              <th class="min-w-150px">Name</th>
              <th class="min-w-100px">Category</th>
              <th class="min-w-80px">Cost</th>
              <th class="min-w-80px">Price</th>
              <th class="min-w-50px text-end">Status</th>
              <th class="min-w-50px text-end">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in items" :key="item.id">
              <td>
                <img v-if="item.image" :src="item.image" class="rounded" style="width:50px;height:50px;object-fit:cover" />
                <span v-else class="text-muted">-</span>
              </td>
              <td>{{ item.code }}</td>
              <td>{{ item.name }}</td>
              <td>{{ item.category_name }}</td>
              <td>{{ item.cost }}</td>
              <td>{{ item.price }}</td>
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
              <td colspan="8" class="text-center text-muted py-10">No products found</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    <div class="modal fade" tabindex="-1" ref="modalEl" data-bs-backdrop="static">
      <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h3 class="modal-title">{{ editingId ? 'Edit Product' : 'Add Product' }}</h3>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form @submit.prevent="saveItem">
            <div class="modal-body">
              <div class="row">
                <div class="col-md-6">
                  <div class="fv-row mb-7">
                    <label class="required fs-6 fw-semibold mb-2">Name</label>
                    <input type="text" v-model="form.name" class="form-control form-control-solid" required />
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="fv-row mb-7">
                    <label class="required fs-6 fw-semibold mb-2">Code</label>
                    <input type="text" v-model="form.code" class="form-control form-control-solid" required />
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="fv-row mb-7">
                    <label class="required fs-6 fw-semibold mb-2">Barcode Symbology</label>
                    <select v-model="form.Type_barcode" class="form-select form-select-solid" required>
                      <option value="CODE128">Code 128</option>
                      <option value="CODE39">Code 39</option>
                      <option value="EAN8">EAN8</option>
                      <option value="EAN13">EAN13</option>
                      <option value="UPC">UPC</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="fv-row mb-7">
                    <label class="required fs-6 fw-semibold mb-2">Category</label>
                    <select v-model="form.category_id" class="form-select form-select-solid" required>
                      <option value="" disabled>Select category</option>
                      <option v-for="c in categories" :key="c.id" :value="c.id">{{ c.name }}</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="fv-row mb-7">
                    <label class="fs-6 fw-semibold mb-2">Brand</label>
                    <select v-model="form.brand_id" class="form-select form-select-solid">
                      <option value="">Select brand</option>
                      <option v-for="b in brands" :key="b.id" :value="b.id">{{ b.name }}</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="fv-row mb-7">
                    <label class="required fs-6 fw-semibold mb-2">Unit</label>
                    <select v-model="form.unit_id" class="form-select form-select-solid" required @change="onUnitChange">
                      <option value="" disabled>Select unit</option>
                      <option v-for="u in units" :key="u.id" :value="u.id">{{ u.name }}</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="fv-row mb-7">
                    <label class="required fs-6 fw-semibold mb-2">Unit Sale</label>
                    <select v-model="form.unit_sale_id" class="form-select form-select-solid" required>
                      <option value="" disabled>Select unit sale</option>
                      <option v-for="u in unitSubs" :key="u.id" :value="u.id">{{ u.name }}</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="fv-row mb-7">
                    <label class="required fs-6 fw-semibold mb-2">Unit Purchase</label>
                    <select v-model="form.unit_purchase_id" class="form-select form-select-solid" required>
                      <option value="" disabled>Select unit purchase</option>
                      <option v-for="u in unitSubs" :key="u.id" :value="u.id">{{ u.name }}</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="fv-row mb-7">
                    <label class="required fs-6 fw-semibold mb-2">Cost</label>
                    <input type="number" step="0.01" v-model="form.cost" class="form-control form-control-solid" required />
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="fv-row mb-7">
                    <label class="required fs-6 fw-semibold mb-2">Price</label>
                    <input type="number" step="0.01" v-model="form.price" class="form-control form-control-solid" required />
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="fv-row mb-7">
                    <label class="fs-6 fw-semibold mb-2">Tax (%)</label>
                    <input type="number" step="0.01" min="0" max="100" v-model="form.TaxNet" class="form-control form-control-solid" />
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="fv-row mb-7">
                    <label class="fs-6 fw-semibold mb-2">Tax Method</label>
                    <select v-model="form.tax_method" class="form-select form-select-solid">
                      <option value="1">Exclusive</option>
                      <option value="2">Inclusive</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="fv-row mb-7">
                    <label class="fs-6 fw-semibold mb-2">Stock Alert</label>
                    <input type="number" min="0" v-model="form.stock_alert" class="form-control form-control-solid" />
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="fv-row mb-7">
                    <label class="fs-6 fw-semibold mb-2">Note</label>
                    <textarea v-model="form.note" class="form-control form-control-solid" rows="1"></textarea>
                  </div>
                </div>
                <div class="col-md-12">
                  <div class="fv-row mb-7">
                    <div class="form-check form-check-custom form-check-solid">
                      <input class="form-check-input" type="checkbox" v-model="form.is_variant" id="isVariant" />
                      <label class="form-check-label fw-semibold fs-6" for="isVariant">Product Has Multi Variants</label>
                    </div>
                  </div>
                </div>
                <div class="col-md-12" v-if="form.is_variant">
                  <div class="fv-row mb-7">
                    <label class="fs-6 fw-semibold mb-2">Variants</label>
                    <div class="input-group mb-2">
                      <input type="text" v-model="variantInput" class="form-control form-control-solid" placeholder="Enter variant name" @keyup.enter="addVariant" />
                      <button class="btn btn-primary" type="button" @click="addVariant">Add</button>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                      <span v-for="(v, i) in variants" :key="i" class="badge badge-light-primary fs-7 m-1" style="cursor:pointer" @click="removeVariant(i)">
                        {{ v.text || v.name || v }} <i class="ki-outline ki-cross fs-6 ms-1"></i>
                      </span>
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="fv-row mb-7">
                    <label class="fs-6 fw-semibold mb-2">Image</label>
                    <div v-if="form.imagePreview" class="mb-3">
                      <img :src="form.imagePreview" class="rounded border" style="max-width:150px;max-height:150px;object-fit:cover" />
                    </div>
                    <input type="file" ref="imageInput" accept="image/png,image/jpg,image/jpeg" class="form-control form-control-solid" @change="onImageChange" />
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="fv-row mb-7">
                    <label class="fs-6 fw-semibold mb-2">Images</label>
                    <div v-if="form.imagesPreview.length" class="mb-3 d-flex gap-2 flex-wrap">
                      <div v-for="(img, idx) in form.imagesPreview" :key="idx" class="position-relative">
                        <img :src="img.url || img" class="rounded border" style="max-width:100px;max-height:100px;object-fit:cover" />
                        <span class="position-absolute top-0 end-0 badge bg-danger rounded-circle" style="cursor:pointer;padding:2px 6px;font-size:10px" @click="removeImage(idx)">&times;</span>
                      </div>
                    </div>
                    <input type="file" ref="imagesInput" multiple accept="image/png,image/jpg,image/jpeg" class="form-control form-control-solid" @change="onImagesChange" />
                  </div>
                </div>
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
import Product from '../../../API/Modules/Inventory/Product/Product'
import Category from '../../../API/Modules/Inventory/Category/Category'
import Brand from '../../../API/Modules/Inventory/Brand/Brand'
import Unit from '../../../API/Modules/Inventory/Unit/Unit'
import Swal from 'sweetalert2'
import { notify } from '@kyvg/vue3-notification'

export default {
  name: 'ProductsView',
  data() {
    return {
      api: new Product('api/v1/inventory/product'),
      catApi: new Category('api/v1/inventory/category'),
      brandApi: new Brand('api/v1/inventory/brand'),
      unitApi: new Unit('api/v1/inventory/unit'),
      items: [], categories: [], brands: [], units: [], unitSubs: [],
      editingId: null, saving: false, modal: null,
      form: {
        code: '', Type_barcode: 'CODE128', name: '', cost: '', price: '',
        category_id: '', brand_id: '', unit_id: '', unit_sale_id: '', unit_purchase_id: '',
        TaxNet: 0, tax_method: '1', note: '', stock_alert: 0, is_variant: false,
        imagePreview: null, imageFile: null,
        imagesPreview: [], imagesFiles: [],
      },
      variants: [], variantInput: '',
    }
  },
  mounted() {
    this.loadItems()
    this.loadSelects()
    this.modal = new Modal(this.$refs.modalEl)
  },
  methods: {
    async loadItems() {
      try { const d = await this.api.getAll(); this.items = d.data || d }
      catch { notify({ text: 'Failed to load products', type: 'error' }) }
    },
    async loadSelects() {
      try {
        const [cats, brds, uns] = await Promise.all([
          this.catApi.getAll({ per_page: -1 }),
          this.brandApi.getAll({ per_page: -1 }),
          this.unitApi.getAll({ per_page: -1 }),
        ])
        this.categories = cats.data || cats
        this.brands = brds.data || brds
        this.units = (uns.data || uns).filter(u => u.base_unit === null)
      } catch { notify({ text: 'Failed to load select data', type: 'error' }) }
    },
    resetFileInputs() {
      if (this.$refs.imageInput) this.$refs.imageInput.value = ''
      if (this.$refs.imagesInput) this.$refs.imagesInput.value = ''
    },
    openForm() {
      this.editingId = null
      this.form = { code: '', Type_barcode: 'CODE128', name: '', cost: '', price: '', category_id: '', brand_id: '', unit_id: '', unit_sale_id: '', unit_purchase_id: '', TaxNet: 0, tax_method: '1', note: '', stock_alert: 0, is_variant: false, imagePreview: null, imageFile: null, imagesPreview: [], imagesFiles: [] }
      this.variants = []; this.variantInput = ''; this.unitSubs = []
      this.resetFileInputs()
      this.modal.show()
    },
    async editItem(item) {
      this.editingId = item.id
      this.form.code = item.code
      this.form.Type_barcode = item.Type_barcode
      this.form.name = item.name
      this.form.cost = item.cost
      this.form.price = item.price
      this.form.category_id = item.category_id
      this.form.brand_id = item.brand_id || ''
      this.form.unit_id = item.unit_id || ''
      this.form.unit_sale_id = item.unit_sale_id || ''
      this.form.unit_purchase_id = item.unit_purchase_id || ''
      this.form.TaxNet = item.TaxNet
      this.form.tax_method = item.tax_method || '1'
      this.form.note = item.note || ''
      this.form.stock_alert = item.stock_alert
      this.form.is_variant = !!item.is_variant
      this.form.imagePreview = item.image || null
      this.form.imageFile = null
      this.form.imagesPreview = item.images || []
      this.form.imagesFiles = []
      this.resetFileInputs()
      this.variants = (item.variants || []).map(v => ({ id: v.id, text: v.name, qty: v.qty || 0, product_id: item.id }))
      this.variantInput = ''
      if (item.unit_id) await this.loadUnitSubs(item.unit_id)
      this.modal.show()
    },
    addVariant() {
      const val = this.variantInput.trim()
      if (val && !this.variants.some(v => (v.text || v.name || v) === val)) {
        this.variants.push({ text: val })
      }
      this.variantInput = ''
    },
    removeVariant(index) { this.variants.splice(index, 1) },
    onImageChange(e) {
      const file = e.target.files[0]
      if (file) {
        this.form.imageFile = file
        this.form.imagePreview = URL.createObjectURL(file)
      }
    },
    onImagesChange(e) {
      const files = Array.from(e.target.files)
      files.forEach(file => {
        this.form.imagesFiles.push(file)
        this.form.imagesPreview.push({ url: URL.createObjectURL(file) })
      })
    },
    removeImage(idx) {
      this.form.imagesPreview.splice(idx, 1)
      this.form.imagesFiles.splice(idx, 1)
    },
    async onUnitChange() {
      this.form.unit_sale_id = ''
      this.form.unit_purchase_id = ''
      if (this.form.unit_id) await this.loadUnitSubs(this.form.unit_id)
    },
    async loadUnitSubs(unitId) {
      try {
        const uns = await this.unitApi.getAll({ per_page: -1 })
        const all = uns.data || uns
        this.unitSubs = all.filter(u => u.id === unitId || u.base_unit === unitId)
      } catch { this.unitSubs = [] }
    },
    async saveItem() {
      if (this.form.is_variant && this.variants.length === 0) {
          notify({ 
              text: 'Please add at least one variant when "Product Has Multi Variants" is enabled', 
              type: 'error' 
          });
          this.saving = false;
          return;
      }
      this.saving = true
      try {
        const fd = new FormData()
        fd.append('code', this.form.code)
        fd.append('Type_barcode', this.form.Type_barcode)
        fd.append('name', this.form.name)
        fd.append('cost', this.form.cost)
        fd.append('price', this.form.price)
        fd.append('category_id', this.form.category_id)
        if (this.form.brand_id) fd.append('brand_id', this.form.brand_id)
        if (this.form.unit_id) fd.append('unit_id', this.form.unit_id)
        if (this.form.unit_sale_id) fd.append('unit_sale_id', this.form.unit_sale_id)
        if (this.form.unit_purchase_id) fd.append('unit_purchase_id', this.form.unit_purchase_id)
        fd.append('TaxNet', this.form.TaxNet || 0)
        fd.append('tax_method', this.form.tax_method)
        if (this.form.note) fd.append('note', this.form.note)
        fd.append('stock_alert', this.form.stock_alert || 0)
        fd.append('is_variant', this.form.is_variant ? 'true' : 'false')
        if (this.variants.length) {
          this.variants.forEach((v, i) => {
            if (v.id) {
              fd.append('variants[' + i + '][id]', v.id)
              fd.append('variants[' + i + '][text]', v.text)
              fd.append('variants[' + i + '][qty]', v.qty || 0)
              fd.append('variants[' + i + '][product_id]', v.product_id || this.editingId || '')
            } else {
              fd.append('variants[' + i + '][text]', v.text || v)
            }
          })
        }
        if (this.form.imageFile) fd.append('image', this.form.imageFile)
        if (this.form.imagesFiles.length) {
          this.form.imagesFiles.forEach(f => fd.append('images[]', f))
        }
        if (this.editingId) await this.api.update(this.editingId, fd); else await this.api.insert(fd)
        notify({ text: this.editingId ? 'Product updated' : 'Product created', type: 'success' })
        this.resetFileInputs()
        this.modal.hide();
        this.saving = false;
        this.loadItems()
      } catch (e) {
        const errors = e.response?.data?.errors
        if (errors) { Object.entries(errors).forEach(([field, msgs]) => msgs.forEach(msg => notify({ text: field + ': ' + msg, type: 'error' }))) }
        else { notify({ text: e.response?.data?.message || 'Error saving product', type: 'error' }) }
        this.saving = false
      }
    },
    async deleteItem(id) {
      if ((await Swal.fire({ title: 'Delete Product?', text: 'This action cannot be undone.', icon: 'warning', showCancelButton: true, confirmButtonText: 'Delete' })).isConfirmed) {
        try { await this.api.delete(id); this.loadItems() }
        catch { notify({ text: 'Failed to delete', type: 'error' }) }
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
