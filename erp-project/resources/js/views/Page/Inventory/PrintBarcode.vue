<template>
  <div class="card card-flush">
    <div class="card-header pt-7">
      <h3 class="card-title align-items-start flex-column">
        <span class="card-label fw-bold text-dark">Print Barcode</span>
        <span class="text-gray-400 mt-1 fw-semibold fs-6">Generate and print product barcodes</span>
      </h3>
    </div>
    <div class="card-body pt-0">
      <div v-if="loading" class="text-center py-10">
        <div class="spinner-border text-primary"></div>
      </div>
      <template v-else>
        <div class="row">
          <div class="col-md-6 mb-7">
            <div class="fv-row">
              <label class="required fs-6 fw-semibold mb-2">Warehouse</label>
              <select v-model="barcode.warehouse_id" class="form-select form-select-solid" required @change="onWarehouseChange">
                <option value="" disabled>Select warehouse</option>
                <option v-for="w in warehouses" :key="w.id" :value="w.id">{{ w.name }}</option>
              </select>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-12 mb-7">
            <div class="fv-row">
              <label class="fs-6 fw-semibold mb-2">Product</label>
              <div class="position-relative">
                <input type="text" v-model="searchInput" class="form-control form-control-solid" placeholder="Search product by code or name..." @input="onSearch" @focus="focused = true" @blur="onBlur" />
                <ul v-if="focused && filteredProducts.length" class="dropdown-menu show w-100 position-absolute mt-1" style="max-height:200px;overflow-y:auto">
                  <li v-for="p in filteredProducts" :key="p.id" class="dropdown-item" style="cursor:pointer" @mousedown.prevent="selectProduct(p)">
                    {{ p.code }} ({{ p.name }})
                  </li>
                </ul>
              </div>
            </div>
          </div>
        </div>

        <div v-if="product.id" class="row">
          <div class="col-md-12 mb-7">
            <div class="table-responsive">
              <table class="table table-hover table-md">
                <thead>
                  <tr>
                    <th>Product Name</th>
                    <th>Code</th>
                    <!-- <th>Quantity</th> -->
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>{{ product.name }}</td>
                    <td>{{ product.code }}</td>
                    <!-- <td>
                      <input type="number" v-model.number="barcode.qte" class="form-control w-50" min="1" />
                    </td> -->
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-6 mb-7">
            <div class="fv-row">
              <label class="required fs-6 fw-semibold mb-2">Paper Size</label>
              <select v-model="paperSize" class="form-select form-select-solid" @change="onPaperSizeChange">
                <option value="" disabled>Select paper size</option>
                <option value="style40">40 per sheet (A4) (1.799 * 1.003)</option>
                <option value="style30">30 per sheet (2.625 * 1)</option>
                <option value="style24">24 per sheet (A4) (2.48 * 1.334)</option>
                <option value="style20">20 per sheet (4 * 1)</option>
                <option value="style18">18 per sheet (A4) (2.5 * 1.835)</option>
                <option value="style14">14 per sheet (4 * 1.33)</option>
                <option value="style12">12 per sheet (A4) (2.5 * 2.834)</option>
                <option value="style10">10 per sheet (4 * 2)</option>
              </select>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-12 mb-7">
            <button class="btn btn-primary btn-sm m-1" @click="generateBarcodes">
              <i class="ki-outline ki-edit fs-2"></i> Preview
            </button>
            <button class="btn btn-danger btn-sm m-1" @click="reset">
              <i class="ki-outline ki-cross fs-2"></i> Reset
            </button>
            <button class="btn btn-light btn-sm pull-right m-1" @click="printBarcodes" :disabled="!showCard">
              <i class="ki-outline ki-printer fs-2"></i> Print
            </button>
          </div>
        </div>

        <div class="row" v-if="showCard">
          <div class="col-md-12">
            <div ref="barcodeContainer" id="print_barcode_label" class="barcode-container">
              <div v-for="(page, pIdx) in pages" :key="pIdx" :class="['barcode-page', pageClass]">
                <div v-for="(item, idx) in page" :key="idx" :class="['barcode-item', sheetClass]">
                  <span class="barcode-name">{{ product.name }}</span>
                  <canvas class="barcode-canvas" :data-code="product.code" :data-type="product.Type_barcode"></canvas>
                </div>
              </div>
              <div v-if="rest > 0" :class="['barcode-page', pageClass]">
                <div v-for="(item, idx) in rest" :key="'r'+idx" :class="['barcode-item', sheetClass]">
                  <span class="barcode-name">{{ product.name }}</span>
                  <canvas class="barcode-canvas" :data-code="product.code" :data-type="product.Type_barcode"></canvas>
                </div>
              </div>
            </div>
          </div>
        </div>
      </template>
    </div>
  </div>
</template>

<script>
import Warehouse from '../../../API/Modules/Core/Warehouse/Warehouse'
import Product from '../../../API/Modules/Inventory/Product/Product'
import JsBarcode from 'jsbarcode'
import { notify } from '@kyvg/vue3-notification'

export default {
  name: 'PrintBarcode',
  data() {
    return {
      warehouseApi: new Warehouse('api/v1/core/warehouse'),
      productApi: new Product('api/v1/inventory/product'),
      loading: true,
      warehouses: [],
      products: [],
      filteredProducts: [],
      focused: false,
      searchInput: '',
      searchTimer: null,
      paperSize: '',
      sheets: 0,
      sheetClass: '',
      pageClass: '',
      pages: [],
      rest: 0,
      showCard: false,
      barcode: {
        warehouse_id: '',
        qte: 10,
      },
      product: {
        id: null,
        name: '',
        code: '',
        Type_barcode: 'CODE128',
      },
    }
  },
  mounted() {
    this.loadWarehouses()
  },
  methods: {
    async loadWarehouses() {
      try {
        const d = await this.warehouseApi.getAll({ per_page: -1 })
        this.warehouses = d.data || d
        this.loading = false
      } catch {
        notify({ text: 'Failed to load warehouses', type: 'error' })
        this.loading = false
      }
    },
    onWarehouseChange() {
      this.searchInput = ''
      this.filteredProducts = []
      this.product = { id: null, name: '', code: '', Type_barcode: 'CODE128' }
      this.showCard = false
      this.loadProducts()
    },
    async loadProducts() {
      if (!this.barcode.warehouse_id) return
      try {
        const res = await this.productApi.byWarehouse(this.barcode.warehouse_id)
        this.products = Array.isArray(res) ? res : (res.data || res)
      } catch {
        this.products = []
      }
    },
    onSearch() {
      if (this.searchTimer) clearTimeout(this.searchTimer)
      if (this.searchInput.length < 1) {
        this.filteredProducts = []
        return
      }
      if (!this.barcode.warehouse_id) {
        notify({ text: 'Please select a warehouse first', type: 'warning' })
        return
      }
      this.searchTimer = setTimeout(() => {
        const q = this.searchInput.toLowerCase()
        this.filteredProducts = this.products.filter(p =>
          p.name.toLowerCase().includes(q) || p.code.toLowerCase().includes(q)
        )
      }, 500)
    },
    onBlur() {
      setTimeout(() => { this.focused = false }, 200)
    },
    selectProduct(p) {
      this.product = { id: p.id, name: p.name, code: p.code, Type_barcode: p.Type_barcode || 'CODE128' }
      this.searchInput = ''
      this.filteredProducts = []
      this.showCard = false
    },
    onPaperSizeChange() {
      const sizes = {
        style40: { sheets: 40, sheetClass: 'style40', pageClass: 'barcodea4' },
        style30: { sheets: 30, sheetClass: 'style30', pageClass: 'barcode_non_a4' },
        style24: { sheets: 24, sheetClass: 'style24', pageClass: 'barcodea4' },
        style20: { sheets: 20, sheetClass: 'style20', pageClass: 'barcode_non_a4' },
        style18: { sheets: 18, sheetClass: 'style18', pageClass: 'barcodea4' },
        style14: { sheets: 14, sheetClass: 'style14', pageClass: 'barcode_non_a4' },
        style12: { sheets: 12, sheetClass: 'style12', pageClass: 'barcodea4' },
        style10: { sheets: 10, sheetClass: 'style10', pageClass: 'barcode_non_a4' },
      }
      const cfg = sizes[this.paperSize]
      if (cfg) {
        this.sheets = cfg.sheets
        this.sheetClass = cfg.sheetClass
        this.pageClass = cfg.pageClass
        this.barcode.qte = cfg.sheets
      }
    },
    calcPages() {
      this.pages = []
      if (!this.sheets) return
      const total = this.barcode.qte
      const fullPages = Math.floor(total / this.sheets)
      this.rest = total % this.sheets
      for (let i = 0; i < fullPages; i++) {
        this.pages.push(Array.from({ length: this.sheets }, (_, idx) => ({ id: i * this.sheets + idx })))
      }
    },
    generateBarcodes() {
      if (!this.product.id) {
        notify({ text: 'Please select a product', type: 'warning' })
        return
      }
      if (!this.paperSize) {
        notify({ text: 'Please select a paper size', type: 'warning' })
        return
      }
      this.calcPages()
      this.showCard = true
      this.$nextTick(() => this.renderAllBarcodes())
    },
    renderAllBarcodes() {
      const container = this.$refs.barcodeContainer
      if (!container) return
      container.querySelectorAll('.barcode-canvas').forEach(canvas => {
        JsBarcode(canvas, canvas.dataset.code, {
          format: canvas.dataset.type || 'CODE128',
          width: 1,
          height: 25,
          fontSize: 15,
          textMargin: 0,
          fontOptions: 'bold',
        })
      })
    },
    printBarcodes() {
      const div = this.$refs.barcodeContainer
      if (!div) return
      const win = window.open('', '', 'height=500,width=500')
      if (!win) return
      win.document.write('<html><head>')
      win.document.write('<link rel="stylesheet" href="/assets_setup/css/print_label.css">')
      win.document.write('</head><body>')
      win.document.write('<div class="barcode-container">')

      const pages = div.querySelectorAll('.barcode-page')
      pages.forEach(page => {
        win.document.write('<div class="' + page.className + '">')
        const items = page.querySelectorAll('.barcode-item')
        items.forEach(item => {
          const name = item.querySelector('.barcode-name')?.textContent || ''
          const canvas = item.querySelector('.barcode-canvas')
          let imgSrc = ''
          if (canvas) imgSrc = canvas.toDataURL()
          win.document.write('<div class="' + (item.className || 'barcode-item') + '">')
          win.document.write('<span class="barcode-name">' + name + '</span>')
          if (imgSrc) win.document.write('<img src="' + imgSrc + '" class="barcode-canvas" />')
          win.document.write('</div>')
        })
        win.document.write('</div>')
      })

      win.document.write('</div></body></html>')
      win.document.close()
      setTimeout(() => { win.print() }, 200)
    },
    reset() {
      this.showCard = false
      this.product = { id: null, name: '', code: '', Type_barcode: 'CODE128' }
      this.barcode.qte = 10
      this.barcode.warehouse_id = ''
      this.searchInput = ''
      this.filteredProducts = []
      this.products = []
      this.paperSize = ''
      this.sheets = 0
      this.pages = []
      this.rest = 0
    },
  },
}
</script>
