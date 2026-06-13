<template>
  <div class="card card-flush">
    <div class="card-header pt-7">
      <h3 class="card-title align-items-start flex-column">
        <span class="card-label fw-bold text-dark">Purchase Returns</span>
        <span class="text-gray-400 mt-1 fw-semibold fs-6">Manage purchase return orders and payments</span>
      </h3>
      <div class="card-toolbar">
        <button type="button" class="btn btn-primary" @click="openForm()">
          <i class="ki-outline ki-plus fs-2"></i> Add Purchase Return
        </button>
      </div>
    </div>

    <div class="card-body pt-0">
      <div class="table-responsive">
        <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
          <thead>
            <tr class="fw-bold text-muted">
              <th class="min-w-100px">Date</th>
              <th class="min-w-120px">Reference</th>
              <th class="min-w-150px">Supplier</th>
              <th class="min-w-150px">Warehouse</th>
              <th class="min-w-100px">Status</th>
              <th class="min-w-100px text-end">Grand Total</th>
              <th class="min-w-100px text-end">Paid</th>
              <th class="min-w-100px">Payment</th>
              <th class="min-w-120px">Created By</th>
              <th class="min-w-50px text-end">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in items" :key="item.id">
              <td>{{ item.date }}</td>
              <td>{{ item.Ref }}</td>
              <td>{{ item.provider_name }}</td>
              <td>{{ item.warehouse_name }}</td>
              <td>
                <span class="badge fs-7 px-3 py-2 rounded-pill" :class="statusBadge(item.status)">
                  {{ item.status }}
                </span>
              </td>
              <td class="text-end">{{ item.GrandTotal }}</td>
              <td class="text-end">{{ item.paid_amount }}</td>
              <td>
                <span class="badge fs-7 px-3 py-2 rounded-pill" :class="paymentBadge(item.payment_status)">
                  {{ item.payment_status }}
                </span>
              </td>
              <td>
                <span class="text-gray-700 fw-semibold">{{ item.user_name || '-' }}</span>
              </td>
              <td class="text-end">
                <router-link class="btn btn-icon btn-bg-light btn-active-color-info btn-sm me-1" :to="`/inventory/purchase-returns/${item.id}/detail`">
                  <i class="ki-outline ki-eye fs-2"></i>
                </router-link>
                <button class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1" @click="editItem(item.id)">
                  <i class="ki-outline ki-pencil fs-2"></i>
                </button>
                <button class="btn btn-icon btn-bg-light btn-active-color-success btn-sm me-1" @click="openPaymentsModal(item)" title="Manage Payments">
                  <i class="ki-outline ki-credit-cart fs-2"></i>
                </button>
                <button class="btn btn-icon btn-bg-light btn-active-color-danger btn-sm" @click="deleteItem(item.id)">
                  <i class="ki-outline ki-trash fs-2"></i>
                </button>
              </td>
            </tr>
            <tr v-if="!items.length">
              <td colspan="10" class="text-center text-muted py-10">No purchase returns found</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Detail Modal -->
    <div class="modal fade" tabindex="-1" ref="detailModalEl" data-bs-backdrop="static">
      <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow-sm">
          <div class="modal-header border-bottom-0 pb-0">
            <div class="d-flex align-items-center">
              <span class="symbol symbol-40 symbol-circle bg-light-primary me-3">
                <i class="ki-outline ki-box fs-2 text-primary"></i>
              </span>
              <div>
                <h3 class="modal-title fw-bold text-gray-900 mb-0">Purchase Return Details</h3>
                <span class="text-gray-500 fs-7">Reference #{{ detailPurchase.Ref }}</span>
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
                  <span class="fw-bold fs-4 text-gray-900">{{ detailPurchase.date }}</span>
                </div>
              </div>
              <div class="col-md-4">
                <div class="card card-dashed card-flush bg-light rounded-3 p-5 h-100">
                  <div class="d-flex align-items-center mb-2">
                    <i class="ki-outline ki-shop fs-3 text-primary me-2"></i>
                    <span class="text-gray-600 fs-7 fw-semibold">Warehouse</span>
                  </div>
                  <span class="fw-bold fs-4 text-gray-900">{{ detailPurchase.warehouse_name }}</span>
                </div>
              </div>
              <div class="col-md-4">
                <div class="card card-dashed card-flush bg-light rounded-3 p-5 h-100">
                  <div class="d-flex align-items-center mb-2">
                    <i class="ki-outline ki-profile-circle fs-3 text-primary me-2"></i>
                    <span class="text-gray-600 fs-7 fw-semibold">Supplier</span>
                  </div>
                  <span class="fw-bold fs-4 text-gray-900">{{ detailPurchase.provider_name }}</span>
                </div>
              </div>
              <div class="col-md-3">
                <div class="card card-dashed card-flush bg-light rounded-3 p-5 h-100">
                  <div class="d-flex align-items-center mb-2">
                    <i class="ki-outline ki-profile-circle fs-3 text-primary me-2"></i>
                    <span class="text-gray-600 fs-7 fw-semibold">Created By</span>
                  </div>
                  <span class="fw-bold fs-4 text-gray-900">{{ detailPurchase.user_name || '-' }}</span>
                </div>
              </div>
              <div v-if="detailPurchase.notes" class="col-12">
                <div class="card card-dashed card-flush bg-light-warning rounded-3 p-5">
                  <span class="fw-semibold fs-5 text-gray-800">{{ detailPurchase.notes }}</span>
                </div>
              </div>
            </div>

            <div class="d-flex align-items-center justify-content-between mb-4">
              <h5 class="text-gray-800 fw-bold mb-0">Products</h5>
              <span class="badge badge-primary fs-7 px-3 py-2 rounded-pill">{{ detailRows.length }} item{{ detailRows.length !== 1 ? 's' : '' }}</span>
            </div>

            <div class="table-responsive rounded-3 border">
              <table class="table table-striped align-middle mb-0">
                <thead class="bg-gray-100">
                  <tr>
                    <th class="ps-6 fw-bold text-gray-700 min-w-40px">#</th>
                    <th class="fw-bold text-gray-700 min-w-160px">Product</th>
                    <th class="fw-bold text-gray-700 min-w-110px">Code</th>
                    <th class="fw-bold text-gray-700 text-end pe-6 min-w-70px">Cost</th>
                    <th class="fw-bold text-gray-700 text-end pe-6 min-w-70px">Qty</th>
                    <th class="fw-bold text-gray-700 text-end pe-6 min-w-100px">Total</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(d, idx) in detailRows" :key="idx">
                    <td class="ps-6 text-gray-500">{{ idx + 1 }}</td>
                    <td>
                      <span class="fw-semibold text-gray-800">{{ d.name }}</span>
                    </td>
                    <td><span class="badge badge-light-info fs-8 px-2 py-1">{{ d.code }}</span></td>
                    <td class="text-end pe-6">{{ d.cost }}</td>
                    <td class="text-end pe-6">{{ d.quantity }} <span class="text-gray-500 fs-7">{{ d.unit }}</span></td>
                    <td class="text-end pe-6">{{ d.total }}</td>
                  </tr>
                  <tr v-if="!detailRows.length">
                    <td colspan="6" class="text-center text-gray-500 py-8">No products</td>
                  </tr>
                </tbody>
              </table>
            </div>

            <div v-if="detailPurchase.GrandTotal > 0" class="row g-5 mt-4 justify-content-end">
              <div class="col-md-5">
                <div class="bg-light rounded-3 p-4">
                  <div class="d-flex justify-content-between mb-2">
                    <span class="text-gray-600">Discount</span>
                    <span class="fw-bold">{{ detailPurchase.discount }}</span>
                  </div>
                  <div class="d-flex justify-content-between mb-2">
                    <span class="text-gray-600">Tax</span>
                    <span class="fw-bold">{{ detailPurchase.TaxNet }}</span>
                  </div>
                  <div class="d-flex justify-content-between mb-2">
                    <span class="text-gray-600">Shipping</span>
                    <span class="fw-bold">{{ detailPurchase.shipping }}</span>
                  </div>
                  <div class="separator my-2"></div>
                  <div class="d-flex justify-content-between mb-2">
                    <span class="fw-bold text-gray-800">Grand Total</span>
                    <span class="fw-bold fs-5 text-gray-900">{{ detailPurchase.GrandTotal }}</span>
                  </div>
                  <div class="d-flex justify-content-between">
                    <span class="text-gray-600">Paid</span>
                    <span class="fw-bold text-success">{{ detailPurchase.paid_amount }}</span>
                  </div>
                  <div class="d-flex justify-content-between">
                    <span class="text-gray-600">Due</span>
                    <span class="fw-bold" :class="dueClass(detailPurchase)">{{ detailPurchase.GrandTotal - detailPurchase.paid_amount }}</span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Payments List -->
            <div v-if="payments.length" class="mt-6">
              <h5 class="text-gray-800 fw-bold mb-3">Payments</h5>
              <div class="table-responsive rounded-3 border">
                <table class="table table-striped align-middle mb-0">
                  <thead class="bg-gray-100">
                    <tr>
                      <th class="ps-6 fw-bold text-gray-700">Date</th>
                      <th class="fw-bold text-gray-700">Ref</th>
                      <th class="fw-bold text-gray-700">Method</th>
                      <th class="fw-bold text-gray-700 text-end pe-6">Amount</th>
                      <th class="fw-bold text-center">Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="p in payments" :key="p.id">
                      <td class="ps-6">{{ p.date }}</td>
                      <td>{{ p.Ref }}</td>
                      <td>{{ p.Reglement }}</td>
                      <td class="text-end pe-6">{{ p.montant }}</td>
                      <td class="text-center">
                        <button class="btn btn-icon btn-sm btn-bg-light btn-active-color-danger" @click="deletePayment(p.id)">
                          <i class="ki-outline ki-trash fs-3"></i>
                        </button>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
          <div class="modal-footer border-top-0 pt-0">
            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Add/Edit Purchase Return Modal -->
    <div class="modal fade" tabindex="-1" ref="modalEl" data-bs-backdrop="static">
      <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content shadow-sm">
          <div class="modal-header border-bottom-0 pb-0">
            <div class="d-flex align-items-center">
              <span class="symbol symbol-40 symbol-circle" :class="editingId ? 'bg-light-warning' : 'bg-light-success'">
                <i class="ki-outline fs-2" :class="editingId ? 'ki-pencil text-warning' : 'ki-arrow-left text-success'"></i>
              </span>
              <div class="ms-3">
                <h3 class="modal-title fw-bold text-gray-900 mb-0">{{ editingId ? 'Edit Purchase Return' : 'Add Purchase Return' }}</h3>
                <span class="text-gray-500 fs-7">{{ editingId ? 'Update purchase return order' : 'Create a new purchase return order' }}</span>
              </div>
            </div>
            <button type="button" class="btn btn-sm btn-icon btn-bg-light btn-active-color-primary" data-bs-dismiss="modal">
              <i class="ki-outline ki-cross fs-2"></i>
            </button>
          </div>
          <form @submit.prevent="saveItem">
            <div class="modal-body pt-6">
              <div class="row g-5 mb-6">
                <div class="col-md-4">
                  <label class="required fs-6 fw-semibold mb-3 text-gray-700">Supplier</label>
                  <div class="position-relative">
                    <i class="ki-outline ki-user fs-2 position-absolute top-50 translate-middle-y ms-4 text-gray-500"></i>
                    <select v-model="form.provider_id" class="form-select form-select-solid ps-12" required>
                      <option value="" disabled>Select supplier</option>
                      <option v-for="s in suppliers" :key="s.id" :value="s.id">{{ s.name }}</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-4">
                  <label class="required fs-6 fw-semibold mb-3 text-gray-700">Warehouse</label>
                  <div class="position-relative">
                    <i class="ki-outline ki-shop fs-2 position-absolute top-50 translate-middle-y ms-4 text-gray-500"></i>
                    <select v-model="form.warehouse_id" class="form-select form-select-solid ps-12" required :disabled="details.length > 0" @change="onWarehouseChange">
                      <option value="" disabled>Select warehouse</option>
                      <option v-for="w in warehouses" :key="w.id" :value="w.id">{{ w.name }}</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-4">
                  <label class="required fs-6 fw-semibold mb-3 text-gray-700">Date</label>
                  <div class="position-relative">
                    <i class="ki-outline ki-calendar fs-2 position-absolute top-50 translate-middle-y ms-4 text-gray-500"></i>
                    <input type="date" v-model="form.date" class="form-control form-control-solid ps-12" required />
                  </div>
                </div>
              </div>

              <div class="row g-5 mb-6">
                <div class="col-md-4">
                  <label class="required fs-6 fw-semibold mb-3 text-gray-700">Status</label>
                  <div class="position-relative">
                    <i class="ki-outline ki-info-circle fs-2 position-absolute top-50 translate-middle-y ms-4 text-gray-500"></i>
                    <select v-model="form.status" class="form-select form-select-solid ps-12" required>
                      <option value="pending">Pending</option>
                      <option value="received">Received</option>
                    </select>
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
                <h5 class="text-gray-800 fw-bold mb-0">Products</h5>
                <span class="badge badge-primary fs-7 px-3 py-2 rounded-pill">{{ details.length }} item{{ details.length !== 1 ? 's' : '' }}</span>
              </div>

              <div class="table-responsive rounded-3 border mb-6">
                <table class="table table-striped align-middle mb-0">
                  <thead class="bg-gray-100">
                    <tr>
                      <th class="ps-6 fw-bold text-gray-700 min-w-40px">#</th>
                      <th class="fw-bold text-gray-700 min-w-130px">Code</th>
                      <th class="fw-bold text-gray-700 min-w-160px">Product</th>
                      <th class="fw-bold text-gray-700 text-center min-w-70px">Cost</th>
                      <th class="fw-bold text-gray-700 text-center min-w-90px">Qty</th>
                      <th class="fw-bold text-gray-700 text-center min-w-80px">Unit</th>
                      <th class="fw-bold text-gray-700 text-center min-w-90px">Subtotal</th>
                      <th class="fw-bold text-center min-w-50px"></th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-if="!details.length">
                      <td colspan="8" class="text-center text-gray-500 py-8">Search and select products to add</td>
                    </tr>
                    <tr v-for="(d, i) in details" :key="d.detail_id">
                      <td class="ps-6 text-gray-500">{{ d.detail_id }}</td>
                      <td><span class="badge badge-light-info fs-8 px-2 py-1">{{ d.code }}</span></td>
                      <td>
                        <span class="fw-semibold text-gray-800">{{ d.name }}</span>
                      </td>
                      <td class="text-center">
                        <input type="number" min="0" step="0.01" v-model.number="d.cost" class="form-control form-control-sm text-center" style="max-width:90px" @input="calcRowTotal(i)" />
                      </td>
                      <td class="text-center">
                        <div class="input-group input-group-sm justify-content-center flex-nowrap" style="max-width:130px;margin:0 auto">
                          <button class="btn btn-icon btn-sm btn-primary" type="button" @click="decrementQty(i)">
                            <i class="ki-outline ki-minus fs-3"></i>
                          </button>
                          <input type="number" min="1" step="1" v-model.number="d.quantity" class="form-control text-center fw-bold border-primary" style="max-width:55px" @input="verifyQty(i); calcRowTotal(i)" />
                          <button class="btn btn-icon btn-sm btn-primary" type="button" @click="incrementQty(i)">
                            <i class="ki-outline ki-plus fs-3"></i>
                          </button>
                        </div>
                      </td>
                      <td class="text-center">
                        <span class="fw-semibold text-gray-700">{{ d.unit_name }}</span>
                      </td>
                      <td class="text-center">
                        <span class="fw-bold">{{ d.total }}</span>
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

              <div class="row g-5 mb-6">
                <div class="col-md-3">
                  <label class="fs-6 fw-semibold mb-3 text-gray-700">Tax Rate (%)</label>
                  <input type="number" min="0" step="0.01" v-model.number="form.tax_rate" class="form-control form-control-solid" @input="calcTotals" />
                </div>
                <div class="col-md-3">
                  <label class="fs-6 fw-semibold mb-3 text-gray-700">Tax Net</label>
                  <input type="number" min="0" step="0.01" v-model.number="form.TaxNet" class="form-control form-control-solid" readonly />
                </div>
                <div class="col-md-3">
                  <label class="fs-6 fw-semibold mb-3 text-gray-700">Discount</label>
                  <input type="number" min="0" step="0.01" v-model.number="form.discount" class="form-control form-control-solid" @input="calcTotals" />
                </div>
                <div class="col-md-3">
                  <label class="fs-6 fw-semibold mb-3 text-gray-700">Shipping</label>
                  <input type="number" min="0" step="0.01" v-model.number="form.shipping" class="form-control form-control-solid" @input="calcTotals" />
                </div>
              </div>

              <div class="row g-5 mb-6 justify-content-end">
                <div class="col-md-4">
                  <div class="bg-light-primary rounded-3 p-4">
                    <div class="d-flex justify-content-between mb-2">
                      <span class="text-gray-600">Subtotal</span>
                      <span class="fw-bold">{{ subtotal }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                      <span class="text-gray-600">Discount</span>
                      <span class="fw-bold">{{ form.discount }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                      <span class="text-gray-600">Tax</span>
                      <span class="fw-bold">{{ form.TaxNet }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                      <span class="text-gray-600">Shipping</span>
                      <span class="fw-bold">{{ form.shipping }}</span>
                    </div>
                    <div class="separator my-2"></div>
                    <div class="d-flex justify-content-between">
                      <span class="fw-bold text-gray-800">Grand Total</span>
                      <span class="fw-bold fs-5 text-gray-900" id="grandTotalDisplay">{{ form.GrandTotal }}</span>
                    </div>
                  </div>
                </div>
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

    <!-- Manage Payments Modal -->
    <div class="modal fade" tabindex="-1" ref="paymentModalEl" data-bs-backdrop="static">
      <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow-sm">
          <div class="modal-header border-bottom-0 pb-0">
            <div class="d-flex align-items-center">
              <span class="symbol symbol-40 symbol-circle bg-light-success me-3">
                <i class="ki-outline ki-credit-cart fs-2 text-success"></i>
              </span>
              <div>
                <h3 class="modal-title fw-bold text-gray-900 mb-0">Manage Payments</h3>
                <span class="text-gray-500 fs-7">{{ selectedPurchase?.Ref }}</span>
              </div>
            </div>
            <button type="button" class="btn btn-sm btn-icon btn-bg-light btn-active-color-primary" data-bs-dismiss="modal">
              <i class="ki-outline ki-cross fs-2"></i>
            </button>
          </div>
          <div class="modal-body pt-6">
            <!-- Purchase Return Summary -->
            <div class="row g-3 mb-5">
              <div class="col-md-3">
                <div class="bg-light rounded-3 p-3 text-center">
                  <span class="text-gray-600 fs-7 fw-semibold d-block">Grand Total</span>
                  <span class="fw-bold fs-5 text-gray-900">{{ selectedPurchase?.GrandTotal }}</span>
                </div>
              </div>
              <div class="col-md-3">
                <div class="bg-light rounded-3 p-3 text-center">
                  <span class="text-gray-600 fs-7 fw-semibold d-block">Paid</span>
                  <span class="fw-bold fs-5 text-success">{{ selectedPurchase?.paid_amount }}</span>
                </div>
              </div>
              <div class="col-md-3">
                <div class="bg-light rounded-3 p-3 text-center">
                  <span class="text-gray-600 fs-7 fw-semibold d-block">Due</span>
                  <span class="fw-bold fs-5" :class="selectedPurchase && (selectedPurchase.GrandTotal - selectedPurchase.paid_amount) > 0 ? 'text-danger' : 'text-success'">{{ selectedPurchase?.GrandTotal - selectedPurchase?.paid_amount }}</span>
                </div>
              </div>
              <div class="col-md-3">
                <div class="bg-light rounded-3 p-3 text-center">
                  <span class="text-gray-600 fs-7 fw-semibold d-block">Status</span>
                  <span class="fw-bold fs-5">
                    <span class="badge fs-7 px-3 py-2 rounded-pill" :class="paymentBadge(selectedPurchase?.payment_status)">{{ selectedPurchase?.payment_status }}</span>
                  </span>
                </div>
              </div>
            </div>

            <!-- Payments Table -->
            <div class="mb-5">
              <h5 class="text-gray-800 fw-bold mb-3">Payment History ({{ paymentHistory.length }})</h5>
              <div class="table-responsive rounded-3 border">
                <table class="table table-striped align-middle mb-0">
                  <thead class="bg-gray-100">
                    <tr>
                      <th class="ps-6 fw-bold text-gray-700">Date</th>
                      <th class="fw-bold text-gray-700">Ref</th>
                      <th class="fw-bold text-gray-700">Method</th>
                      <th class="fw-bold text-gray-700 text-end pe-6">Amount</th>
                      <th class="fw-bold text-center">Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-if="!paymentHistory.length">
                      <td colspan="5" class="text-center text-gray-500 py-6">No payments yet</td>
                    </tr>
                    <tr v-for="p in paymentHistory" :key="p.id">
                      <td class="ps-6">{{ p.date }}</td>
                      <td>{{ p.Ref }}</td>
                      <td>{{ p.Reglement }}</td>
                      <td class="text-end pe-6">{{ p.montant }}</td>
                      <td class="text-center">
                        <button class="btn btn-icon btn-sm btn-bg-light btn-active-color-primary me-1" @click="openEditPayment(p)" title="Edit Payment">
                          <i class="ki-outline ki-pencil fs-3"></i>
                        </button>
                        <button class="btn btn-icon btn-sm btn-bg-light btn-active-color-danger" @click="deletePayment(p.id)" title="Delete Payment">
                          <i class="ki-outline ki-trash fs-3"></i>
                        </button>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>

            <!-- Add Payment Form (hidden when fully paid) -->
            <div v-if="selectedPurchase && (selectedPurchase.GrandTotal - selectedPurchase.paid_amount) > 0">
              <div class="separator separator-dashed my-5"></div>
              <h5 class="text-gray-800 fw-bold mb-3">Add New Payment</h5>
              <form @submit.prevent="savePayment">
                <div class="row g-5 mb-6">
                  <div class="col-md-6">
                    <label class="required fs-6 fw-semibold mb-3 text-gray-700">Date</label>
                    <input type="date" v-model="paymentForm.date" class="form-control form-control-solid" required />
                  </div>
                  <div class="col-md-6">
                    <label class="required fs-6 fw-semibold mb-3 text-gray-700">Payment Method</label>
                    <select v-model="paymentForm.Reglement" class="form-select form-select-solid" required>
                      <option value="Cash">Cash</option>
                      <option value="credit card">Credit Card</option>
                      <option value="cheque">Cheque</option>
                      <option value="bank transfer">Bank Transfer</option>
                      <option value="other">Other</option>
                    </select>
                  </div>
                  <div class="col-md-6">
                    <label class="required fs-6 fw-semibold mb-3 text-gray-700">Amount</label>
                    <input type="number" min="0.01" step="0.01" v-model.number="paymentForm.montant" class="form-control form-control-solid" required />
                    <div class="text-muted fs-7 mt-1">Due: {{ selectedPurchase?.GrandTotal - selectedPurchase?.paid_amount }}</div>
                  </div>
                  <div class="col-md-6">
                    <label class="fs-6 fw-semibold mb-3 text-gray-700">Notes</label>
                    <textarea v-model="paymentForm.notes" class="form-control form-control-solid" rows="2"></textarea>
                  </div>
                </div>
                <div class="text-end">
                  <button type="submit" class="btn btn-success" :disabled="paymentSaving">
                    <span v-if="paymentSaving" class="spinner-border spinner-border-sm me-2"></span>
                    <i v-else class="ki-outline ki-check-circle fs-6 me-1"></i>
                    Add Payment
                  </button>
                </div>
              </form>
            </div>
            <div v-else class="alert alert-success mb-0">
              <i class="ki-outline ki-check-circle fs-2 me-2"></i> This invoice is fully paid.
            </div>
          </div>
          <div class="modal-footer border-top-0 pt-0">
            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Edit Payment Modal -->
    <div class="modal fade" tabindex="-1" ref="editPaymentModalEl" data-bs-backdrop="static">
      <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content shadow-sm">
          <div class="modal-header border-bottom-0 pb-0">
            <div class="d-flex align-items-center">
              <span class="symbol symbol-40 symbol-circle bg-light-warning me-3">
                <i class="ki-outline ki-pencil fs-2 text-warning"></i>
              </span>
              <div>
                <h3 class="modal-title fw-bold text-gray-900 mb-0">Edit Payment</h3>
                <span class="text-gray-500 fs-7">{{ editPaymentForm.Ref }}</span>
              </div>
            </div>
            <button type="button" class="btn btn-sm btn-icon btn-bg-light btn-active-color-primary" data-bs-dismiss="modal">
              <i class="ki-outline ki-cross fs-2"></i>
            </button>
          </div>
          <form @submit.prevent="saveEditPayment">
            <div class="modal-body pt-6">
              <div class="row g-5 mb-6">
                <div class="col-12">
                  <label class="required fs-6 fw-semibold mb-3 text-gray-700">Date</label>
                  <input type="date" v-model="editPaymentForm.date" class="form-control form-control-solid" required />
                </div>
                <div class="col-12">
                  <label class="required fs-6 fw-semibold mb-3 text-gray-700">Payment Method</label>
                  <select v-model="editPaymentForm.Reglement" class="form-select form-select-solid" required>
                    <option value="Cash">Cash</option>
                    <option value="credit card">Credit Card</option>
                    <option value="cheque">Cheque</option>
                    <option value="bank transfer">Bank Transfer</option>
                    <option value="other">Other</option>
                  </select>
                </div>
                <div class="col-12">
                  <label class="required fs-6 fw-semibold mb-3 text-gray-700">Amount</label>
                  <input type="number" min="0.01" step="0.01" v-model.number="editPaymentForm.montant" class="form-control form-control-solid" required />
                  <div class="text-muted fs-7 mt-1">Original: {{ editPaymentForm.original_montant }}</div>
                </div>
                <div class="col-12">
                  <label class="fs-6 fw-semibold mb-3 text-gray-700">Notes</label>
                  <textarea v-model="editPaymentForm.notes" class="form-control form-control-solid" rows="2"></textarea>
                </div>
              </div>
            </div>
            <div class="modal-footer border-top-0 pt-0">
              <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
              <button type="submit" class="btn btn-warning" :disabled="editPaymentSaving">
                <span v-if="editPaymentSaving" class="spinner-border spinner-border-sm me-2"></span>
                <i v-else class="ki-outline ki-check-circle fs-6 me-1"></i>
                Update Payment
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
import PurchaseReturn from '../../../API/Modules/Inventory/PurchaseReturn/PurchaseReturn'
import PaymentPurchaseReturn from '../../../API/Modules/Inventory/PaymentPurchaseReturn/PaymentPurchaseReturn'
import Provider from '../../../API/Modules/Inventory/Provider/Provider'
import Warehouse from '../../../API/Modules/Core/Warehouse/Warehouse'
import Product from '../../../API/Modules/Inventory/Product/Product'
import Unit from '../../../API/Modules/Inventory/Unit/Unit'
import Swal from 'sweetalert2'
import { notify } from '@kyvg/vue3-notification'

export default {
  name: 'PurchaseReturnsView',
  data() {
    return {
      purchaseReturnApi: new PurchaseReturn('api/v1/inventory/purchase-return'),
      paymentApi: new PaymentPurchaseReturn('api/v1/inventory/payment-purchase-return'),
      providerApi: new Provider('api/v1/inventory/provider'),
      warehouseApi: new Warehouse('api/v1/core/warehouse'),
      productApi: new Product('api/v1/inventory/product'),
      unitApi: new Unit('api/v1/inventory/unit'),
      items: [], warehouses: [], products: [], units: [], suppliers: [],
      editingId: null, saving: false, modal: null, detailModal: null, paymentModal: null, editPaymentModal: null,
      searchInput: '', searchFocused: false, searchResults: [],
      timer: null,
      form: { provider_id: '', warehouse_id: '', date: new Date().toISOString().slice(0, 10), status: 'pending', tax_rate: 0, TaxNet: 0, discount: 0, shipping: 0, GrandTotal: 0, notes: '' },
      details: [],
      detailPurchase: {}, detailRows: [], payments: [],
      selectedPurchase: null,
      paymentHistory: [],
      paymentForm: { date: new Date().toISOString().slice(0, 10), Reglement: 'Cash', montant: 0, notes: '' },
      paymentSaving: false,
      editPaymentForm: { id: null, date: '', Reglement: 'Cash', montant: 0, notes: '', Ref: '', original_montant: 0 },
      editPaymentSaving: false,
    }
  },
  computed: {
    subtotal() {
      return this.details.reduce((sum, d) => sum + (parseFloat(d.total) || 0), 0)
    },
  },
  mounted() {
    this.loadItems()
    this.modal = new Modal(this.$refs.modalEl)
    this.detailModal = new Modal(this.$refs.detailModalEl)
    this.paymentModal = new Modal(this.$refs.paymentModalEl)
    this.editPaymentModal = new Modal(this.$refs.editPaymentModalEl)
  },
  methods: {
    async loadItems() {
      try { const d = await this.purchaseReturnApi.getAll(); this.items = d.data || d || [] }
      catch { notify({ text: 'Failed to load purchase returns', type: 'error' }) }
    },
    statusBadge(s) {
      if (s === 'received') return 'badge-light-success'
      if (s === 'ordered') return 'badge-light-primary'
      return 'badge-light-warning'
    },
    paymentBadge(s) {
      if (s === 'paid') return 'badge-light-success'
      if (s === 'partial') return 'badge-light-primary'
      return 'badge-light-danger'
    },
    dueClass(item) {
      const due = item.GrandTotal - item.paid_amount
      return due > 0 ? 'text-danger' : 'text-success'
    },
    resetForm() {
      this.form = { provider_id: '', warehouse_id: '', date: new Date().toISOString().slice(0, 10), status: 'pending', tax_rate: 0, TaxNet: 0, discount: 0, shipping: 0, GrandTotal: 0, notes: '' }
      this.details = []
      this.searchInput = ''
      this.searchResults = []
      this.products = []
    },
    async openForm() {
      this.editingId = null
      this.resetForm()
      await this.loadSuppliers()
      await this.loadWarehouses()
      await this.loadUnits()
      this.modal.show()
    },
    async loadSuppliers() {
      try {
        const d = await this.providerApi.getAll({ per_page: 1000 })
        this.suppliers = Array.isArray(d) ? d : (d.data || [])
      } catch { notify({ text: 'Failed to load suppliers', type: 'error' }) }
    },
    async loadWarehouses() {
      try {
        const d = await this.warehouseApi.getAll({ per_page: 1000 })
        this.warehouses = Array.isArray(d) ? d : (d.data || [])
      } catch { notify({ text: 'Failed to load warehouses', type: 'error' }) }
    },
    async loadUnits() {
      try {
        const d = await this.unitApi.getAll({ per_page: 1000 })
        this.units = Array.isArray(d) ? d : (d.data || [])
      } catch { notify({ text: 'Failed to load units', type: 'error' }) }
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
        cost: p.cost || 0,
        quantity: 1,
        purchase_unit_id: p.unit_purchase_id || (this.units.length ? this.units[0].id : null),
        unit_name: '',
        total: p.cost || 0,
        TaxNet: 0,
        tax_method: '1',
        discount: 0,
        discount_method: '1',
      })
      this.updateUnitName(this.details.length - 1)
      this.calcRowTotal(this.details.length - 1)
      this.calcTotals()
    },
    updateUnitName(i) {
      const d = this.details[i]
      const unit = this.units.find(u => u.id === d.purchase_unit_id)
      d.unit_name = unit ? unit.shortName : ''
    },
    verifyQty(i) {
      const d = this.details[i]
      d.quantity = Math.floor(Math.abs(d.quantity)) || 1
    },
    incrementQty(i) {
      const d = this.details[i]
      d.quantity = (d.quantity || 0) + 1
      this.calcRowTotal(i)
    },
    decrementQty(i) {
      const d = this.details[i]
      if (d.quantity > 1) { d.quantity--; this.calcRowTotal(i) }
    },
    removeDetail(i) { this.details.splice(i, 1); this.calcTotals() },
    calcRowTotal(i) {
      const d = this.details[i]
      d.total = ((parseFloat(d.cost) || 0) * (parseInt(d.quantity) || 0)).toFixed(2)
      this.calcTotals()
    },
    calcTotals() {
      const sub = this.details.reduce((s, d) => s + (parseFloat(d.total) || 0), 0)
      const disc = parseFloat(this.form.discount) || 0
      const taxRate = parseFloat(this.form.tax_rate) || 0
      const shipping = parseFloat(this.form.shipping) || 0
      const afterDiscount = sub - disc
      const tax = afterDiscount * (taxRate / 100)
      this.form.TaxNet = parseFloat(tax.toFixed(2))
      this.form.GrandTotal = parseFloat((afterDiscount + tax + shipping).toFixed(2))
    },
    verifiedForm() {
      if (!this.details.length) { notify({ text: 'Please add at least one product', type: 'warning' }); return false }
      if (this.details.some(d => !d.quantity || d.quantity <= 0)) { notify({ text: 'All products must have a valid quantity', type: 'warning' }); return false }
      if (!this.form.provider_id) { notify({ text: 'Please select a supplier', type: 'warning' }); return false }
      if (!this.form.warehouse_id) { notify({ text: 'Please select a warehouse', type: 'warning' }); return false }
      return true
    },
    async editItem(id) {
      this.editingId = id
      this.resetForm()
      try {
        const res = await this.purchaseReturnApi.show(id)
        const d = res.data
        this.suppliers = d.providers || []
        this.warehouses = d.warehouses || []
        await this.loadUnits()
        this.form.provider_id = d.purchase_return?.provider_id || ''
        this.form.warehouse_id = d.purchase_return?.warehouse_id || ''
        this.form.date = d.purchase_return?.date || ''
        this.form.status = d.purchase_return?.status || 'pending'
        this.form.notes = d.purchase_return?.notes || ''
        this.form.tax_rate = d.purchase_return?.tax_rate || 0
        this.form.TaxNet = d.purchase_return?.TaxNet || 0
        this.form.discount = d.purchase_return?.discount || 0
        this.form.shipping = d.purchase_return?.shipping || 0
        this.form.GrandTotal = d.purchase_return?.GrandTotal || 0
        if (this.form.warehouse_id) {
          const prodRes = await this.productApi.byWarehouse(this.form.warehouse_id)
          this.products = Array.isArray(prodRes) ? prodRes : (prodRes.data || [])
        }
        this.details = (d.details || []).map((item, idx) => {
          const prod = (this.products || []).find(p => p.id === item.product_id && p.product_variant_id === (item.product_variant_id || null))
          return { ...item, detail_id: idx + 1, unit_name: item.unit || '', current: prod?.qty || 0 }
        })
        this.modal.show()
      } catch { notify({ text: 'Failed to load purchase return', type: 'error' }) }
    },
    async showDetail(id) {
      try {
        const res = await this.purchaseReturnApi.show(id)
        const d = res.data
        this.detailPurchase = d.purchase_return || {}
        this.detailRows = d.details || []
        this.payments = d.purchase_return?.payment_purchase_returns || []
        this.detailModal.show()
      } catch { notify({ text: 'Failed to load details', type: 'error' }) }
    },
    async openPaymentsModal(item) {
      this.selectedPurchase = item
      this.paymentForm = { date: new Date().toISOString().slice(0, 10), Reglement: 'Cash', montant: 0, notes: '' }
      this.paymentHistory = []
      try {
        const res = await this.purchaseReturnApi.show(item.id)
        this.paymentHistory = res.data?.purchase_return?.payment_purchase_returns || []
        this.selectedPurchase = { ...this.selectedPurchase, ...res.data?.purchase_return }
      } catch { /* ignore */ }
      this.paymentModal.show()
    },
    async savePayment() {
      if (!this.paymentForm.montant || this.paymentForm.montant <= 0) {
        notify({ text: 'Please enter a valid payment amount', type: 'warning' }); return
      }
      this.paymentSaving = true
      try {
        await this.paymentApi.insert({
          purchase_return_id: this.selectedPurchase.id,
          date: this.paymentForm.date,
          Reglement: this.paymentForm.Reglement,
          montant: this.paymentForm.montant,
          change: 0,
          notes: this.paymentForm.notes,
        })
        notify({ text: 'Payment added successfully', type: 'success' })
        this.paymentModal.hide()
        this.paymentSaving = false
        this.loadItems()
      } catch (e) {
        const errors = e.response?.data?.errors
        if (errors && Object.keys(errors).length) { Object.entries(errors).forEach(([field, msgs]) => msgs.forEach(msg => notify({ text: field + ': ' + msg, type: 'error' }))) }
        else { notify({ text: e.response?.data?.message || 'Error saving payment', type: 'error' }) }
        this.paymentSaving = false
      }
    },
    async openEditPayment(payment) {
      this.editPaymentForm = {
        id: payment.id,
        date: payment.date,
        Reglement: payment.Reglement,
        montant: payment.montant,
        notes: payment.notes || '',
        Ref: payment.Ref,
        original_montant: payment.montant,
      }
      this.editPaymentModal.show()
    },
    async saveEditPayment() {
      if (!this.editPaymentForm.montant || this.editPaymentForm.montant <= 0) {
        notify({ text: 'Please enter a valid payment amount', type: 'warning' }); return
      }
      this.editPaymentSaving = true
      try {
        await this.paymentApi.updatePut(this.editPaymentForm.id, {
          date: this.editPaymentForm.date,
          Reglement: this.editPaymentForm.Reglement,
          montant: this.editPaymentForm.montant,
          notes: this.editPaymentForm.notes,
        })
        notify({ text: 'Payment updated successfully', type: 'success' })
        this.editPaymentModal.hide()
        this.editPaymentSaving = false
        if (this.selectedPurchase) this.openPaymentsModal(this.selectedPurchase)
        this.loadItems()
      } catch (e) {
        const errors = e.response?.data?.errors
        if (errors && Object.keys(errors).length) { Object.entries(errors).forEach(([field, msgs]) => msgs.forEach(msg => notify({ text: field + ': ' + msg, type: 'error' }))) }
        else { notify({ text: e.response?.data?.message || 'Error updating payment', type: 'error' }) }
        this.editPaymentSaving = false
      }
    },
    async deletePayment(id) {
      if ((await Swal.fire({ title: 'Delete Payment?', text: 'This action cannot be undone.', icon: 'warning', showCancelButton: true, confirmButtonText: 'Delete' })).isConfirmed) {
        try {
          await this.paymentApi.delete(id)
          this.loadItems()
          if (this.selectedPurchase) this.openPaymentsModal(this.selectedPurchase)
          if (this.detailPurchase.id) this.showDetail(this.detailPurchase.id)
        }
        catch { notify({ text: 'Failed to delete payment', type: 'error' }) }
      }
    },
    async saveItem() {
      if (!this.verifiedForm()) return
      this.saving = true
      try {
        const fd = new FormData()
        fd.append('provider_id', this.form.provider_id)
        fd.append('warehouse_id', this.form.warehouse_id)
        fd.append('date', this.form.date)
        fd.append('status', this.form.status)
        fd.append('tax_rate', this.form.tax_rate || 0)
        fd.append('TaxNet', this.form.TaxNet || 0)
        fd.append('discount', this.form.discount || 0)
        fd.append('shipping', this.form.shipping || 0)
        fd.append('GrandTotal', this.form.GrandTotal || 0)
        if (this.form.notes) fd.append('notes', this.form.notes)
        this.details.forEach((d, i) => {
          fd.append(`details[${i}][product_id]`, d.product_id)
          fd.append(`details[${i}][quantity]`, d.quantity)
          fd.append(`details[${i}][cost]`, d.cost || 0)
          fd.append(`details[${i}][TaxNet]`, d.TaxNet || 0)
          fd.append(`details[${i}][tax_method]`, d.tax_method || '1')
          fd.append(`details[${i}][discount]`, d.discount || 0)
          fd.append(`details[${i}][discount_method]`, d.discount_method || '1')
          fd.append(`details[${i}][total]`, d.total || 0)
          if (d.purchase_unit_id) fd.append(`details[${i}][purchase_unit_id]`, d.purchase_unit_id)
          if (d.product_variant_id) fd.append(`details[${i}][product_variant_id]`, d.product_variant_id)
          if (d.id) fd.append(`details[${i}][id]`, d.id)
        })
        if (this.editingId) await this.purchaseReturnApi.update(this.editingId, fd); else await this.purchaseReturnApi.insert(fd)
        notify({ text: this.editingId ? 'Purchase return updated' : 'Purchase return created', type: 'success' })
        this.modal.hide()
        this.saving = false
        this.loadItems()
      } catch (e) {
        const errors = e.response?.data?.errors
        if (errors && Object.keys(errors).length) { Object.entries(errors).forEach(([field, msgs]) => msgs.forEach(msg => notify({ text: field + ': ' + msg, type: 'error' }))) }
        else { notify({ text: e.response?.data?.message || 'Error saving purchase return', type: 'error' }) }
        this.saving = false
      }
    },
    async deleteItem(id) {
      if ((await Swal.fire({ title: 'Delete Purchase Return?', text: 'This action cannot be undone.', icon: 'warning', showCancelButton: true, confirmButtonText: 'Delete' })).isConfirmed) {
        try { await this.purchaseReturnApi.delete(id); this.loadItems() }
        catch { notify({ text: 'Failed to delete', type: 'error' }) }
      }
    },
  },
}
</script>
