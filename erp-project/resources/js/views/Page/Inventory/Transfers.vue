<template>
  <div class="card card-flush">
    <div class="card-header pt-7">
      <h3 class="card-title align-items-start flex-column">
        <span class="card-label fw-bold text-dark">Stock Transfers</span>
        <span class="text-gray-400 mt-1 fw-semibold fs-6">Transfer stock between warehouses</span>
      </h3>
      <div class="card-toolbar">
        <button type="button" class="btn btn-primary" @click="openForm()">
          <i class="ki-outline ki-plus fs-2"></i> Add Transfer
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
              <th class="min-w-150px">From</th>
              <th class="min-w-150px">To</th>
              <th class="min-w-100px">Status</th>
              <th class="min-w-50px text-end">Items</th>
              <th class="min-w-50px text-end">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in items" :key="item.id">
              <td>{{ item.date }}</td>
              <td>{{ item.Ref }}</td>
              <td>{{ item.from_warehouse_name }}</td>
              <td>{{ item.to_warehouse_name }}</td>
              <td>
                <span class="badge fs-7 px-3 py-2 rounded-pill" :class="statusBadge(item.status)">
                  {{ item.status }}
                </span>
              </td>
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
              <td colspan="7" class="text-center text-muted py-10">No transfers found</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <div class="modal fade" tabindex="-1" ref="detailModalEl" data-bs-backdrop="static">
      <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow-sm">
          <div class="modal-header border-bottom-0 pb-0">
            <div class="d-flex align-items-center">
              <span class="symbol symbol-40 symbol-circle bg-light-primary me-3">
                <i class="ki-outline ki-arrow-up-down fs-2 text-primary"></i>
              </span>
              <div>
                <h3 class="modal-title fw-bold text-gray-900 mb-0">Transfer Details</h3>
                <span class="text-gray-500 fs-7">Reference #{{ detailTransfer.Ref }}</span>
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
                  <span class="fw-bold fs-4 text-gray-900">{{ detailTransfer.date }}</span>
                </div>
              </div>
              <div class="col-md-4">
                <div class="card card-dashed card-flush bg-light rounded-3 p-5 h-100">
                  <div class="d-flex align-items-center mb-2">
                    <i class="ki-outline ki-shop fs-3 text-danger me-2"></i>
                    <span class="text-gray-600 fs-7 fw-semibold">From</span>
                  </div>
                  <span class="fw-bold fs-4 text-gray-900">{{ detailTransfer.from_warehouse_name }}</span>
                </div>
              </div>
              <div class="col-md-4">
                <div class="card card-dashed card-flush bg-light rounded-3 p-5 h-100">
                  <div class="d-flex align-items-center mb-2">
                    <i class="ki-outline ki-shop fs-3 text-success me-2"></i>
                    <span class="text-gray-600 fs-7 fw-semibold">To</span>
                  </div>
                  <span class="fw-bold fs-4 text-gray-900">{{ detailTransfer.to_warehouse_name }}</span>
                </div>
              </div>
              <div class="col-md-4">
                <div class="card card-dashed card-flush bg-light rounded-3 p-5 h-100">
                  <div class="d-flex align-items-center mb-2">
                    <i class="ki-outline ki-info-circle fs-3 text-primary me-2"></i>
                    <span class="text-gray-600 fs-7 fw-semibold">Status</span>
                  </div>
                  <span class="badge fs-6 px-3 py-2 rounded-pill" :class="statusBadge(detailTransfer.status)">{{ detailTransfer.status }}</span>
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
                      <span class="symbol-label fw-bold fs-8 text-info">{{ detailTransfer.user_name ? detailTransfer.user_name.charAt(0).toUpperCase() : '?' }}</span>
                    </span>
                    <span class="fw-bold fs-4 text-gray-900">{{ detailTransfer.user_name || 'N/A' }}</span>
                  </div>
                </div>
              </div>
              <div class="col-md-4">
                <div class="card card-dashed card-flush bg-light rounded-3 p-5 h-100">
                  <div class="d-flex align-items-center mb-2">
                    <i class="ki-outline ki-money fs-3 text-primary me-2"></i>
                    <span class="text-gray-600 fs-7 fw-semibold">Grand Total</span>
                  </div>
                  <span class="fw-bold fs-4 text-gray-900">{{ detailTransfer.GrandTotal }}</span>
                </div>
              </div>
              <div v-if="detailTransfer.notes" class="col-12">
                <div class="card card-dashed card-flush bg-light-warning rounded-3 p-5">
                  <div class="d-flex align-items-center mb-2">
                    <i class="ki-outline ki-notepad fs-3 text-warning me-2"></i>
                    <span class="text-warning fs-7 fw-semibold">Notes</span>
                  </div>
                  <span class="fw-semibold fs-5 text-gray-800">{{ detailTransfer.notes }}</span>
                </div>
              </div>
            </div>

            <div class="d-flex align-items-center justify-content-between mb-4">
              <h5 class="text-gray-800 fw-bold mb-0">
                <i class="ki-outline ki-element-11 fs-3 text-primary me-1"></i>
                Transferred Products
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
                    <th class="fw-bold text-gray-700 text-end pe-6 min-w-90px">Cost</th>
                    <th class="fw-bold text-gray-700 text-end pe-6 min-w-100px">Total</th>
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
                    <td class="text-end pe-6">{{ d.cost }}</td>
                    <td class="text-end pe-6">{{ d.total }}</td>
                  </tr>
                  <tr v-if="!detailRows.length">
                    <td colspan="6" class="text-center text-gray-500 py-8">
                      <i class="ki-outline ki-box fs-3x text-gray-300 d-block mb-3"></i>
                      No products in this transfer
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <div v-if="detailTransfer.GrandTotal > 0" class="row g-5 mt-4 justify-content-end">
              <div class="col-md-5">
                <div class="bg-light rounded-3 p-4">
                  <div class="d-flex justify-content-between mb-2">
                    <span class="text-gray-600">Discount</span>
                    <span class="fw-bold">{{ detailTransfer.discount }}</span>
                  </div>
                  <div class="d-flex justify-content-between mb-2">
                    <span class="text-gray-600">Tax</span>
                    <span class="fw-bold">{{ detailTransfer.TaxNet }}</span>
                  </div>
                  <div class="d-flex justify-content-between mb-2">
                    <span class="text-gray-600">Shipping</span>
                    <span class="fw-bold">{{ detailTransfer.shipping }}</span>
                  </div>
                  <div class="separator my-2"></div>
                  <div class="d-flex justify-content-between">
                    <span class="fw-bold text-gray-800">Grand Total</span>
                    <span class="fw-bold fs-5 text-gray-900">{{ detailTransfer.GrandTotal }}</span>
                  </div>
                </div>
              </div>
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
                <i class="ki-outline fs-2" :class="editingId ? 'ki-pencil text-warning' : 'ki-arrow-up-down text-success'"></i>
              </span>
              <div class="ms-3">
                <h3 class="modal-title fw-bold text-gray-900 mb-0">{{ editingId ? 'Edit Transfer' : 'Add Transfer' }}</h3>
                <span class="text-gray-500 fs-7">{{ editingId ? 'Update stock transfer details' : 'Create a new stock transfer' }}</span>
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
                  <label class="required fs-6 fw-semibold mb-3 text-gray-700">From Warehouse</label>
                  <div class="position-relative">
                    <i class="ki-outline ki-shop fs-2 position-absolute top-50 translate-middle-y ms-4 text-gray-500"></i>
                    <select v-model="form.from_warehouse_id" class="form-select form-select-solid ps-12" required :disabled="details.length > 0" @change="onFromWarehouseChange">
                      <option value="" disabled>Select source warehouse</option>
                      <option v-for="w in warehouses" :key="w.id" :value="w.id">{{ w.name }}</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-4">
                  <label class="required fs-6 fw-semibold mb-3 text-gray-700">To Warehouse</label>
                  <div class="position-relative">
                    <i class="ki-outline ki-shop fs-2 position-absolute top-50 translate-middle-y ms-4 text-gray-500"></i>
                    <select v-model="form.to_warehouse_id" class="form-select form-select-solid ps-12" required @change="validateWarehouses">
                      <option value="" disabled>Select destination warehouse</option>
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
                      <option value="sent">Sent</option>
                      <option value="completed">Completed</option>
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
                      <th class="fw-bold text-gray-700 text-center min-w-90px">Current Stock</th>
                      <th class="fw-bold text-gray-700 text-center min-w-70px">Cost</th>
                      <th class="fw-bold text-gray-700 text-center min-w-90px">Qty</th>
                      <th class="fw-bold text-gray-700 text-center min-w-80px">Unit</th>
                      <th class="fw-bold text-gray-700 text-center min-w-90px">Subtotal</th>
                      <th class="fw-bold text-center min-w-50px"></th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-if="!details.length">
                      <td colspan="9" class="text-center text-gray-500 py-8">
                        <i class="ki-outline ki-box fs-3x text-gray-300 d-block mb-3"></i>
                        Search and select products to transfer
                      </td>
                    </tr>
                    <tr v-for="(d, i) in details" :key="d.detail_id" :class="{ 'bg-light-danger': d.quantity > d.current }">
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
                        <select v-model="d.purchase_unit_id" class="form-select form-select-sm border-0 bg-light fw-semibold text-center" @change="updateUnitName(i)">
                          <option v-for="u in units" :key="u.id" :value="u.id">{{ u.shortName }}</option>
                        </select>
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
                      <span class="text-gray-600">Total (excl. tax)</span>
                      <span class="fw-bold" id="subtotalDisplay">{{ subtotal }}</span>
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
  </div>
</template>

<script>
import { Modal } from 'bootstrap'
import Transfer from '../../../API/Modules/Inventory/Transfer/Transfer'
import Warehouse from '../../../API/Modules/Core/Warehouse/Warehouse'
import Product from '../../../API/Modules/Inventory/Product/Product'
import Unit from '../../../API/Modules/Inventory/Unit/Unit'
import Swal from 'sweetalert2'
import { notify } from '@kyvg/vue3-notification'

export default {
  name: 'TransfersView',
  data() {
    return {
      api: new Transfer('api/v1/inventory/transfer'),
      warehouseApi: new Warehouse('api/v1/core/warehouse'),
      productApi: new Product('api/v1/inventory/product'),
      unitApi: new Unit('api/v1/inventory/unit'),
      items: [], warehouses: [], products: [], units: [],
      editingId: null, saving: false, modal: null, detailModal: null,
      searchInput: '', searchFocused: false, searchResults: [],
      timer: null,
      form: { from_warehouse_id: '', to_warehouse_id: '', date: new Date().toISOString().slice(0, 10), status: 'pending', tax_rate: 0, TaxNet: 0, discount: 0, shipping: 0, GrandTotal: 0, notes: '' },
      details: [],
      detailTransfer: {}, detailRows: [],
    }
  },
  computed: {
    subtotal() {
      return this.details.reduce((sum, d) => sum + (parseFloat(d.total) || 0), 0)
    },
    totalQty() {
      return this.details.reduce((sum, d) => sum + (parseInt(d.quantity) || 0), 0)
    },
  },
  mounted() {
    this.loadItems()
    this.modal = new Modal(this.$refs.modalEl)
    this.detailModal = new Modal(this.$refs.detailModalEl)
  },
  methods: {
    async loadItems() {
      try { const d = await this.api.getAll(); this.items = d.data || d || [] }
      catch { notify({ text: 'Failed to load transfers', type: 'error' }) }
    },
    statusBadge(s) {
      if (s === 'completed') return 'badge-light-success'
      if (s === 'sent') return 'badge-light-primary'
      return 'badge-light-warning'
    },
    resetForm() {
      this.form = { from_warehouse_id: '', to_warehouse_id: '', date: new Date().toISOString().slice(0, 10), status: 'pending', tax_rate: 0, TaxNet: 0, discount: 0, shipping: 0, GrandTotal: 0, notes: '' }
      this.details = []
      this.searchInput = ''
      this.searchResults = []
      this.products = []
    },
    async openForm() {
      this.editingId = null
      this.resetForm()
      await this.loadWarehouses()
      await this.loadUnits()
      this.modal.show()
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
    validateWarehouses() {
      if (this.form.from_warehouse_id && this.form.to_warehouse_id && this.form.from_warehouse_id === this.form.to_warehouse_id) {
        notify({ text: 'Source and destination warehouses must be different', type: 'warning' })
        this.form.to_warehouse_id = ''
      }
    },
    async onFromWarehouseChange() {
      this.searchInput = ''
      this.searchResults = []
      this.products = []
      if (!this.form.from_warehouse_id) return
      try {
        const res = await this.productApi.byWarehouse(this.form.from_warehouse_id)
        this.products = Array.isArray(res) ? res : (res.data || [])
      } catch { notify({ text: 'Failed to load products', type: 'error' }) }
    },
    onSearch() {
      if (this.timer) clearTimeout(this.timer)
      if (this.searchInput.length < 1) { this.searchResults = []; return }
      if (!this.form.from_warehouse_id) { notify({ text: 'Please select source warehouse first', type: 'warning' }); return }
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
      const defaultUnitId = p.unit_purchase_id || (this.units.length ? this.units[0].id : null)
      const currentStock = p.qty || 0
      this.details.push({
        id: 0,
        detail_id: maxId + 1,
        product_id: p.id,
        product_variant_id: p.product_variant_id || null,
        code: p.code,
        name: p.name,
        current: currentStock,
        cost: p.cost || 0,
        quantity: 1,
        purchase_unit_id: defaultUnitId,
        unit_name: defaultUnitId ? (this.units.find(u => u.id === defaultUnitId)?.shortName || '') : '',
        total: p.cost || 0,
        TaxNet: 0,
        tax_method: '1',
        discount: 0,
        discount_method: '1',
      })
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
      if (d.quantity > d.current) {
        notify({ text: `الكمية المطلوبة (${d.quantity}) تتجاوز المخزون المتاح (${d.current})`, type: 'warning' })
        d.quantity = d.current
      }
      if (d.quantity < 1) d.quantity = 1
    },
    incrementQty(i) {
      const d = this.details[i]
      if (d.quantity + 1 > d.current) { notify({ text: 'المخزون غير كافي', type: 'warning' }); return }
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
      if (!this.form.from_warehouse_id) { notify({ text: 'Please select source warehouse', type: 'warning' }); return false }
      if (!this.form.to_warehouse_id) { notify({ text: 'Please select destination warehouse', type: 'warning' }); return false }
      if (this.form.from_warehouse_id === this.form.to_warehouse_id) { notify({ text: 'Source and destination warehouses must be different', type: 'warning' }); return false }
      return true
    },
    async editItem(id) {
      this.editingId = id
      this.resetForm()
      try {
        const res = await this.api.show(id)
        const d = res.data
        this.warehouses = d.warehouses || []
        this.form.from_warehouse_id = d.transfer?.from_warehouse_id || ''
        this.form.to_warehouse_id = d.transfer?.to_warehouse_id || ''
        this.form.date = d.transfer?.date || ''
        this.form.status = d.transfer?.status || 'pending'
        this.form.notes = d.transfer?.notes || ''
        this.form.tax_rate = d.transfer?.tax_rate || 0
        this.form.TaxNet = d.transfer?.TaxNet || 0
        this.form.discount = d.transfer?.discount || 0
        this.form.shipping = d.transfer?.shipping || 0
        this.form.GrandTotal = d.transfer?.GrandTotal || 0
        await this.loadUnits()
        this.details = (d.details || []).map((item, idx) => {
          // Find current stock from products data
          const prod = (this.products || []).find(p => p.id === item.product_id && p.product_variant_id === (item.product_variant_id || null))
          return { ...item, detail_id: idx + 1, unit_name: item.unit || '', current: prod?.qty || 0 }
        })
        if (this.form.from_warehouse_id) {
          const prodRes = await this.productApi.byWarehouse(this.form.from_warehouse_id)
          this.products = Array.isArray(prodRes) ? prodRes : (prodRes.data || [])
        }
        this.modal.show()
      } catch { notify({ text: 'Failed to load transfer', type: 'error' }) }
    },
    async showDetail(id) {
      try {
        const res = await this.api.show(id)
        const d = res.data
        this.detailTransfer = d.transfer || {}
        this.detailRows = d.details || []
        this.detailModal.show()
      } catch { notify({ text: 'Failed to load details', type: 'error' }) }
    },
    async saveItem() {
      if (!this.verifiedForm()) return
      this.saving = true
      try {
        const fd = new FormData()
        fd.append('from_warehouse_id', this.form.from_warehouse_id)
        fd.append('to_warehouse_id', this.form.to_warehouse_id)
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
        if (this.editingId) await this.api.update(this.editingId, fd); else await this.api.insert(fd)
        notify({ text: this.editingId ? 'Transfer updated' : 'Transfer created', type: 'success' })
        this.modal.hide()
        this.saving = false
        this.loadItems()
      } catch (e) {
        const errors = e.response?.data?.errors
        if (errors) { Object.entries(errors).forEach(([field, msgs]) => msgs.forEach(msg => notify({ text: field + ': ' + msg, type: 'error' }))) }
        else { notify({ text: e.response?.data?.message || 'Error saving transfer', type: 'error' }) }
        this.saving = false
      }
    },
    async deleteItem(id) {
      if ((await Swal.fire({ title: 'Delete Transfer?', text: 'This action cannot be undone.', icon: 'warning', showCancelButton: true, confirmButtonText: 'Delete' })).isConfirmed) {
        try { await this.api.delete(id); this.loadItems() }
        catch { notify({ text: 'Failed to delete', type: 'error' }) }
      }
    },
  },
}
</script>
