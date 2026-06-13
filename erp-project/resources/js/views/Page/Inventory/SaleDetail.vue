<template>
  <div class="card card-flush">
    <div class="card-header pt-7">
      <h3 class="card-title align-items-start flex-column">
        <span class="card-label fw-bold text-dark">Sale Details</span>
        <span class="text-gray-400 mt-1 fw-semibold fs-6">#{{ sale.Ref }}</span>
      </h3>
      <div class="card-toolbar">
        <div class="dropdown d-inline-block me-2">
          <button class="btn btn-primary btn-sm dropdown-toggle" @click.stop="printOpen = !printOpen">
            <i class="ki-outline ki-printer fs-2"></i> Print
          </button>
          <div v-if="printOpen" class="dropdown-menu show" style="position:absolute;inset:0 auto auto 0;transform:translate(0, 38px)" @click.stop>
            <a class="dropdown-item" href="#" @click.prevent="printDetail('a4')">A4</a>
            <a class="dropdown-item" href="#" @click.prevent="printDetail('a5')">A5</a>
            <a class="dropdown-item" href="#" @click.prevent="printDetail('letter')">Letter</a>
          </div>
        </div>
        <div class="dropdown d-inline-block me-2">
          <button class="btn btn-success btn-sm dropdown-toggle" @click.stop="downloadOpen = !downloadOpen">
            <i class="ki-outline ki-file-cloud fs-2"></i> Download
          </button>
          <div v-if="downloadOpen" class="dropdown-menu show" style="position:absolute;inset:0 auto auto 0;transform:translate(0, 38px)" @click.stop>
            <a class="dropdown-item" href="#" @click.prevent="downloadPDF">PDF</a>
            <a class="dropdown-item" href="#" @click.prevent="downloadDOCS">DOCS</a>
            <a class="dropdown-item" href="#" @click.prevent="downloadCSV">CSV</a>
          </div>
        </div>
        <router-link to="/inventory/sales" class="btn btn-light btn-sm">
          <i class="ki-outline ki-arrow-left fs-2"></i> Back
        </router-link>
      </div>
    </div>
    <div class="card-body pt-0">
      <div v-if="loading" class="text-center py-10">
        <div class="spinner-border text-primary"></div>
      </div>
      <template v-else-if="sale.id">
        <div id="print_sale">
          <div class="row g-5 mb-8" id="sale-info-cards">
            <div class="col-md-3">
              <div class="card card-dashed card-flush bg-light rounded-3 p-5 h-100">
                <div class="d-flex align-items-center mb-2">
                  <i class="ki-outline ki-calendar fs-3 text-primary me-2"></i>
                  <span class="text-gray-600 fs-7 fw-semibold">Date</span>
                </div>
                <span class="fw-bold fs-4 text-gray-900">{{ sale.date }}</span>
              </div>
            </div>
            <div class="col-md-3">
              <div class="card card-dashed card-flush bg-light rounded-3 p-5 h-100">
                <div class="d-flex align-items-center mb-2">
                  <i class="ki-outline ki-shop fs-3 text-primary me-2"></i>
                  <span class="text-gray-600 fs-7 fw-semibold">Client</span>
                </div>
                <span class="fw-bold fs-4 text-gray-900">{{ sale.client_name }}</span>
              </div>
            </div>
            <div class="col-md-3">
              <div class="card card-dashed card-flush bg-light rounded-3 p-5 h-100">
                <div class="d-flex align-items-center mb-2">
                  <i class="ki-outline ki-home fs-3 text-primary me-2"></i>
                  <span class="text-gray-600 fs-7 fw-semibold">Warehouse</span>
                </div>
                <span class="fw-bold fs-4 text-gray-900">{{ sale.warehouse_name }}</span>
              </div>
            </div>
            <div class="col-md-3">
              <div class="card card-dashed card-flush bg-light rounded-3 p-5 h-100">
                <div class="d-flex align-items-center mb-2">
                  <i class="ki-outline ki-status fs-3 text-primary me-2"></i>
                  <span class="text-gray-600 fs-7 fw-semibold">Status</span>
                </div>
                <span class="fw-bold fs-4">
                  <span class="badge fs-6 px-3 py-2 rounded-pill" :class="statusBadge(sale.status)">
                    {{ sale.status }}
                  </span>
                </span>
              </div>
            </div>
            <div class="col-md-3">
              <div class="card card-dashed card-flush bg-light rounded-3 p-5 h-100">
                <div class="d-flex align-items-center mb-2">
                  <i class="ki-outline ki-credit-cart fs-3 text-primary me-2"></i>
                  <span class="text-gray-600 fs-7 fw-semibold">Payment</span>
                </div>
                <span class="fw-bold fs-4">
                  <span class="badge fs-6 px-3 py-2 rounded-pill" :class="paymentBadge(sale.payment_status)">
                    {{ sale.payment_status }}
                  </span>
                </span>
              </div>
            </div>
            <div class="col-md-3">
              <div class="card card-dashed card-flush bg-light rounded-3 p-5 h-100">
                <div class="d-flex align-items-center mb-2">
                  <i class="ki-outline ki-profile-circle fs-3 text-primary me-2"></i>
                  <span class="text-gray-600 fs-7 fw-semibold">Created By</span>
                </div>
                <span class="fw-bold fs-4 text-gray-900">{{ sale.user_name || '-' }}</span>
              </div>
            </div>
          </div>

          <div class="d-flex align-items-center justify-content-between mb-4">
            <h4 class="fw-bold text-gray-900 mb-0">Products ({{ detailRows.length }} items)</h4>
          </div>

          <div class="table-responsive mb-5">
            <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
              <thead>
                <tr class="fw-bold text-muted">
                  <th class="min-w-40px">#</th>
                  <th class="min-w-150px">Product</th>
                  <th class="min-w-100px">Code</th>
                  <th class="min-w-100px text-end">Price</th>
                  <th class="min-w-80px text-end">Qty</th>
                  <th class="min-w-80px text-end">Discount</th>
                  <th class="min-w-80px text-end">Tax</th>
                  <th class="min-w-100px text-end">Total</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(d, idx) in detailRows" :key="d.id">
                  <td>{{ idx + 1 }}</td>
                  <td>
                    <span class="fw-semibold">{{ d.name }}</span>
                    <span v-if="d.product_variant_id" class="badge badge-light-primary ms-1">Variant</span>
                  </td>
                  <td>{{ d.code }}</td>
                  <td class="text-end">{{ d.price }}</td>
                  <td class="text-end">{{ d.quantity }} {{ d.unit || '' }}</td>
                  <td class="text-end">{{ d.discount }}</td>
                  <td class="text-end">{{ d.TaxNet }}</td>
                  <td class="text-end fw-bold">{{ d.total }}</td>
                </tr>
              </tbody>
            </table>
          </div>

          <div class="row justify-content-end mb-5">
            <div class="col-md-5">
              <div class="bg-light rounded-3 p-5">
                <div class="d-flex justify-content-between mb-2">
                  <span class="text-gray-600">Subtotal</span>
                  <span class="fw-semibold">{{ subtotal }}</span>
                </div>
                <div v-if="parseFloat(sale.discount)" class="d-flex justify-content-between mb-2">
                  <span class="text-gray-600">Discount</span>
                  <span class="fw-semibold text-danger">- {{ sale.discount }}</span>
                </div>
                <div v-if="parseFloat(sale.shipping)" class="d-flex justify-content-between mb-2">
                  <span class="text-gray-600">Shipping</span>
                  <span class="fw-semibold">+ {{ sale.shipping }}</span>
                </div>
                <div v-if="parseFloat(sale.TaxNet)" class="d-flex justify-content-between mb-2">
                  <span class="text-gray-600">Tax ({{ sale.tax_rate }}%)</span>
                  <span class="fw-semibold">+ {{ sale.TaxNet }}</span>
                </div>
                <hr class="my-2" />
                <div class="d-flex justify-content-between mb-1">
                  <span class="fw-bold text-gray-900 fs-5">Grand Total</span>
                  <span class="fw-bold text-gray-900 fs-5">{{ sale.GrandTotal }}</span>
                </div>
                <div class="d-flex justify-content-between mb-1">
                  <span class="text-gray-600">Paid</span>
                  <span class="fw-semibold text-success">{{ sale.paid_amount }}</span>
                </div>
                <div class="d-flex justify-content-between">
                  <span class="text-gray-600">Due</span>
                  <span class="fw-bold" :class="dueClass">{{ due }}</span>
                </div>
              </div>
            </div>
          </div>

          <div v-if="payments.length" class="mb-5">
            <h5 class="fw-bold text-gray-900 mb-3">Payments</h5>
            <div class="table-responsive">
              <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                <thead>
                  <tr class="fw-bold text-muted">
                    <th class="min-w-100px">Date</th>
                    <th class="min-w-120px">Reference</th>
                    <th class="min-w-100px">Method</th>
                    <th class="min-w-100px text-end">Amount</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="p in payments" :key="p.id">
                    <td>{{ p.date }}</td>
                    <td>{{ p.Ref }}</td>
                    <td>{{ p.Reglement }}</td>
                    <td class="text-end fw-semibold">{{ p.montant }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <div v-if="sale.notes" class="mb-5">
            <h5 class="fw-bold text-gray-900 mb-2">Notes</h5>
            <p class="text-gray-700 bg-light rounded-3 p-4">{{ sale.notes }}</p>
          </div>
        </div>
      </template>
    </div>
  </div>
</template>

<script>
import Sale from '../../../API/Modules/Inventory/Sale/Sale'
import { notify } from '@kyvg/vue3-notification'

export default {
  name: 'SaleDetail',
  data() {
    return {
      api: new Sale('api/v1/inventory/sale'),
      loading: true,
      sale: {},
      detailRows: [],
      payments: [],
      printOpen: false,
      downloadOpen: false,
    }
  },
  computed: {
    subtotal() {
      return this.detailRows.reduce((sum, d) => sum + (parseFloat(d.total) || 0), 0)
    },
    due() {
      return (parseFloat(this.sale.GrandTotal) || 0) - (parseFloat(this.sale.paid_amount) || 0)
    },
    dueClass() {
      const d = this.due
      return d > 0 ? 'text-danger' : 'text-success'
    },
  },
  mounted() {
    this.loadDetail()
    document.addEventListener('click', this.closeDropdowns)
  },
  beforeUnmount() {
    document.removeEventListener('click', this.closeDropdowns)
  },
  methods: {
    async loadDetail() {
      try {
        const id = this.$route.params.id
        const res = await this.api.show(id)
        const d = res.data || res
        this.sale = d.sale || d
        this.detailRows = d.details || []
        this.payments = this.sale.payment_sales || []
        this.loading = false
      } catch {
        notify({ text: 'Failed to load sale details', type: 'error' })
        this.loading = false
      }
    },
    closeDropdowns() {
      this.printOpen = false
      this.downloadOpen = false
    },
    statusBadge(s) {
      if (s === 'completed') return 'badge-light-success'
      if (s === 'ordered') return 'badge-light-primary'
      return 'badge-light-warning'
    },
    paymentBadge(s) {
      if (s === 'paid') return 'badge-light-success'
      if (s === 'partial') return 'badge-light-primary'
      return 'badge-light-danger'
    },
    printDetail(size) {
      this.closeDropdowns()
      const div = document.getElementById('print_sale')
      if (!div) return
      setTimeout(() => {
        const win = window.open('', '', 'height=500,width=800')
        if (!win) return
        win.document.write('<html><head>')
        win.document.write('<style>')
        win.document.write('body { font-family: Arial, sans-serif; padding: 20px; color: #333; }')
        win.document.write('table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }')
        win.document.write('th, td { padding: 8px 10px; border: 1px solid #ddd; text-align: left; font-size: 12px; }')
        win.document.write('th { background: #f5f5f5; font-weight: bold; }')
        win.document.write('.text-end { text-align: right; }')
        win.document.write('.fw-bold { font-weight: bold; }')
        win.document.write('.fw-semibold { font-weight: 600; }')
        win.document.write('.text-gray-900 { color: #1e1e1e; }')
        win.document.write('.text-gray-600 { color: #888; }')
        win.document.write('.text-success { color: #28a745; }')
        win.document.write('.text-danger { color: #dc3545; }')
        win.document.write('.mb-2 { margin-bottom: 8px; }')
        win.document.write('.mb-3 { margin-bottom: 15px; }')
        win.document.write('.mb-4 { margin-bottom: 20px; }')
        win.document.write('.mb-5 { margin-bottom: 25px; }')
        win.document.write('.mt-4 { margin-top: 20px; }')
        win.document.write('h4, h5 { margin: 0 0 10px 0; }')
        win.document.write('hr { border: none; border-top: 1px solid #ddd; }')
        win.document.write('.d-flex { display: flex; }')
        win.document.write('.justify-content-between { justify-content: space-between; }')
        win.document.write('.justify-content-end { justify-content: flex-end; }')
        win.document.write('.row { display: flex; flex-wrap: wrap; }')
        win.document.write('.col-md-3 { width: 25%; box-sizing: border-box; padding: 0 8px; }')
        win.document.write('.col-md-5 { width: 41.666%; box-sizing: border-box; padding: 0 8px; }')
        win.document.write('.bg-light { background: #f9f9f9; }')
        win.document.write('.p-4 { padding: 16px; }')
        win.document.write('.p-5 { padding: 20px; }')
        win.document.write('.rounded-3 { border-radius: 8px; }')
        win.document.write('.badge { display: inline-block; padding: 4px 10px; border-radius: 20px; font-size: 12px; }')
        win.document.write('.badge-light-success { background: #d4edda; color: #155724; }')
        win.document.write('.badge-light-primary { background: #cce5ff; color: #004085; }')
        win.document.write('.badge-light-warning { background: #fff3cd; color: #856404; }')
        win.document.write('.min-w-40px, .min-w-80px, .min-w-100px, .min-w-120px, .min-w-150px { }')
        win.document.write('.g-5 > * { padding: 0 8px; }')
        win.document.write('.mb-8 { margin-bottom: 40px; }')
        win.document.write('.h-100 { height: 100%; }')
        if (size === 'a4') {
          win.document.write('@page { size: A4; margin: 15mm; }')
        } else if (size === 'a5') {
          win.document.write('@page { size: A5; margin: 10mm; }')
        } else if (size === 'letter') {
          win.document.write('@page { size: Letter; margin: 15mm; }')
        }
        win.document.write('</style>')
        win.document.write('</head><body>')
        const clone = div.cloneNode(true)
        win.document.write(clone.innerHTML)
        win.document.write('</body></html>')
        win.document.close()
        setTimeout(() => win.print(), 300)
      }, 100)
    },
    async downloadPDF() {
      this.closeDropdowns()
      try {
        await this.api.downloadPdf(this.$route.params.id, `sale_${this.sale.Ref}.pdf`)
      } catch {
        notify({ text: 'Failed to download PDF', type: 'error' })
      }
    },
    downloadDOCS() {
      this.closeDropdowns()
      const rows = this.detailRows.map((d, i) =>
        `<tr>
          <td>${i + 1}</td>
          <td>${d.name || ''}</td>
          <td>${d.code || ''}</td>
          <td>${d.price || 0}</td>
          <td>${d.quantity || 0} ${d.unit || ''}</td>
          <td>${d.total || 0}</td>
        </tr>`
      ).join('')
      const html = `<!DOCTYPE html>
<html>
<head><meta charset="utf-8"><title>Sale #${this.sale.Ref}</title></head>
<body style="font-family:Arial,sans-serif;padding:20px">
  <h2>Sale Invoice</h2>
  <p><strong>Ref:</strong> ${this.sale.Ref} | <strong>Date:</strong> ${this.sale.date} | <strong>Client:</strong> ${this.sale.client_name} | <strong>Warehouse:</strong> ${this.sale.warehouse_name}</p>
  <table border="1" cellpadding="6" cellspacing="0" style="width:100%;border-collapse:collapse">
    <thead style="background:#f5f5f5"><tr><th>#</th><th>Product</th><th>Code</th><th>Price</th><th>Qty</th><th>Total</th></tr></thead>
    <tbody>${rows}</tbody>
  </table>
  <p><strong>Grand Total:</strong> ${this.sale.GrandTotal} | <strong>Paid:</strong> ${this.sale.paid_amount} | <strong>Due:</strong> ${this.due}</p>
</body></html>`
      this.downloadFile(html, `sale_${this.sale.Ref}.doc`, 'application/msword')
    },
    downloadCSV() {
      this.closeDropdowns()
      const header = 'Date,Reference,Client,Warehouse,Product,Code,Price,Quantity,Unit,Total,GrandTotal,Paid,Due,Status\n'
      const rows = this.detailRows.map(d =>
        `"${this.sale.date}","${this.sale.Ref}","${this.sale.client_name}","${this.sale.warehouse_name}","${d.name || ''}","${d.code || ''}",${d.price || 0},${d.quantity || 0},"${d.unit || ''}",${d.total || 0},${this.sale.GrandTotal},${this.sale.paid_amount},${this.due},"${this.sale.status || ''}"`
      ).join('\n')
      this.downloadFile('\uFEFF' + header + rows, `sale_${this.sale.Ref}.csv`, 'text/csv;charset=utf-8')
    },
    downloadFile(content, filename, mime) {
      const blob = new Blob([content], { type: mime })
      const url = URL.createObjectURL(blob)
      const a = document.createElement('a')
      a.href = url
      a.download = filename
      document.body.appendChild(a)
      a.click()
      document.body.removeChild(a)
      URL.revokeObjectURL(url)
    },
  },
}
</script>
