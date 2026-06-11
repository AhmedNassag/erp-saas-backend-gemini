<template>
  <div class="card card-flush">
    <div class="card-header pt-7">
      <h3 class="card-title align-items-start flex-column">
        <span class="card-label fw-bold text-dark">Product Details</span>
        <span class="text-gray-400 mt-1 fw-semibold fs-6">{{ product.name }}</span>
      </h3>
      <div class="card-toolbar">
        <button class="btn btn-primary btn-sm me-2" @click="printDetail">
          <i class="ki-outline ki-printer fs-2"></i> Print
        </button>
        <router-link to="/inventory/products" class="btn btn-light btn-sm">
          <i class="ki-outline ki-arrow-left fs-2"></i> Back
        </router-link>
      </div>
    </div>
    <div class="card-body pt-0">
      <div v-if="loading" class="text-center py-10">
        <div class="spinner-border text-primary"></div>
      </div>
      <template v-else-if="product.id">
        <div id="print_product">
          <div class="row mb-5">
            <div class="col-md-12 text-center mb-5">
              <canvas ref="barcodeCanvas"></canvas>
            </div>
          </div>
          <div class="row">
            <div class="col-md-8">
              <table class="table table-hover table-bordered table-md">
                <tbody>
                  <tr>
                    <td class="fw-semibold">Code</td>
                    <th>{{ product.code }}</th>
                  </tr>
                  <tr>
                    <td class="fw-semibold">Name</td>
                    <th>{{ product.name }}</th>
                  </tr>
                  <tr>
                    <td class="fw-semibold">Category</td>
                    <th>{{ product.category_name }}</th>
                  </tr>
                  <tr>
                    <td class="fw-semibold">Brand</td>
                    <th>{{ product.brand_name }}</th>
                  </tr>
                  <tr>
                    <td class="fw-semibold">Cost</td>
                    <th>{{ product.cost }}</th>
                  </tr>
                  <tr>
                    <td class="fw-semibold">Price</td>
                    <th>{{ product.price }}</th>
                  </tr>
                  <tr>
                    <td class="fw-semibold">Unit</td>
                    <th>{{ product.unit_name }}</th>
                  </tr>
                  <tr>
                    <td class="fw-semibold">Tax (%)</td>
                    <th>{{ product.TaxNet }}</th>
                  </tr>
                  <tr v-if="product.TaxNet != 0">
                    <td class="fw-semibold">Tax Method</td>
                    <th>{{ product.tax_method == '1' ? 'Exclusive' : 'Inclusive' }}</th>
                  </tr>
                  <tr>
                    <td class="fw-semibold">Stock Alert</td>
                    <th><span class="badge badge-warning">{{ product.stock_alert }}</span></th>
                  </tr>
                  <tr v-if="product.is_variant && product.variants && product.variants.length">
                    <td class="fw-semibold">Variants</td>
                    <th>
                      <span v-for="v in product.variants" :key="v.id" class="badge badge-light-primary me-1">{{ v.name }}</span>
                    </th>
                  </tr>
                </tbody>
              </table>
            </div>
            <div class="col-md-4">
              <div v-if="product.image" class="text-center mb-3">
                <img :src="product.image" class="rounded border" style="max-width:100%;max-height:300px;object-fit:contain" />
              </div>
              <div v-if="product.images && product.images.length" id="productImagesCarousel" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner rounded border">
                  <div v-for="(img, idx) in product.images" :key="idx" :class="['carousel-item', { active: idx === 0 }]">
                    <img :src="img.url" class="d-block w-100" style="max-height:250px;object-fit:contain" />
                  </div>
                </div>
                <button v-if="product.images.length > 1" class="carousel-control-prev" type="button" data-bs-target="#productImagesCarousel" data-bs-slide="prev" style="width:40px;height:40px;top:50%;transform:translateY(-50%);background:rgba(0,0,0,0.5);border-radius:50%">
                  <span class="carousel-control-prev-icon"></span>
                </button>
                <button v-if="product.images.length > 1" class="carousel-control-next" type="button" data-bs-target="#productImagesCarousel" data-bs-slide="next" style="width:40px;height:40px;top:50%;transform:translateY(-50%);background:rgba(0,0,0,0.5);border-radius:50%">
                  <span class="carousel-control-next-icon"></span>
                </button>
                <div class="carousel-indicators position-static mt-2" v-if="product.images.length > 1">
                  <button v-for="(img, idx) in product.images" :key="idx" type="button" data-bs-target="#productImagesCarousel" :data-bs-slide-to="idx" :class="['border-0 rounded-circle mx-1', { active: idx === 0 }]" style="width:10px;height:10px;background:#ccc" :aria-label="'Slide ' + (idx+1)"></button>
                </div>
              </div>
            </div>
          </div>
          <div class="row mt-4">
            <div class="col-md-5">
              <table class="table table-hover table-sm">
                <thead>
                  <tr>
                    <th class="fw-semibold">Warehouse</th>
                    <th class="fw-semibold">Quantity</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(pw, idx) in countQTY" :key="idx">
                    <td>{{ pw.mag }}</td>
                    <td>{{ pw.qte }} {{ product.unit_name }}</td>
                  </tr>
                  <tr v-if="!countQTY.length">
                    <td colspan="2" class="text-muted">No stock data</td>
                  </tr>
                </tbody>
              </table>
            </div>
            <div v-if="product.is_variant && countQTY_variants.length" class="col-md-7">
              <table class="table table-hover table-sm">
                <thead>
                  <tr>
                    <th class="fw-semibold">Warehouse</th>
                    <th class="fw-semibold">Variant</th>
                    <th class="fw-semibold">Quantity</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(pv, idx) in countQTY_variants" :key="idx">
                    <td>{{ pv.mag }}</td>
                    <td>{{ pv.variant }}</td>
                    <td>{{ pv.qte }} {{ product.unit_name }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </template>
    </div>
  </div>
</template>

<script>
import Product from '../../../API/Modules/Inventory/Product/Product'
import JsBarcode from 'jsbarcode'
import { notify } from '@kyvg/vue3-notification'

export default {
  name: 'ProductDetail',
  data() {
    return {
      api: new Product('api/v1/inventory/product'),
      loading: true,
      product: {},
      countQTY: [],
      countQTY_variants: [],
    }
  },
  mounted() {
    this.loadDetail()
  },
  methods: {
    async loadDetail() {
      try {
        const id = this.$route.params.id
        const res = await this.api.show(id)
        const d = res.data || res
        this.product = d.product || d
        this.countQTY = d.CountQTY || []
        this.countQTY_variants = d.CountQTY_variants || []
        this.loading = false
        this.$nextTick(() => this.renderBarcode())
      } catch {
        notify({ text: 'Failed to load product details', type: 'error' })
        this.loading = false
      }
    },
    renderBarcode() {
      if (this.$refs.barcodeCanvas && this.product.code) {
        JsBarcode(this.$refs.barcodeCanvas, this.product.code, {
          format: this.product.Type_barcode || 'CODE128',
          width: 2,
          height: 60,
          fontSize: 18,
          textMargin: 2,
          fontOptions: 'bold',
        })
      }
    },
    printDetail() {
      const div = document.getElementById('print_product')
      if (!div) return
      this.renderBarcode()
      setTimeout(() => {
        const win = window.open('', '', 'height=500,width=800')
        if (!win) return
        win.document.write('<html><head>')
        win.document.write('<link rel="stylesheet" href="' + window.location.origin + '/assets_setup/css/print_label.css">')
        win.document.write('</head><body>')
        const clone = div.cloneNode(true)
        clone.querySelectorAll('canvas').forEach(c => {
          const img = document.createElement('img')
          img.src = c.toDataURL()
          img.className = c.className
          img.style.cssText = c.style.cssText
          c.parentNode.replaceChild(img, c)
        })
        win.document.write(clone.innerHTML)
        win.document.write('</body></html>')
        win.document.close()
        setTimeout(() => win.print(), 300)
      }, 100)
    },
  },
}
</script>