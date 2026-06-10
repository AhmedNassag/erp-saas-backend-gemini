<template>
  <div class="card card-flush">
    <div class="card-header pt-7">
      <h3 class="card-title align-items-start flex-column">
        <span class="card-label fw-bold text-dark">Users</span>
        <span class="text-gray-400 mt-1 fw-semibold fs-6">Manage system users</span>
      </h3>
      <div class="card-toolbar">
        <button type="button" class="btn btn-primary" @click="openForm()">
          <i class="ki-outline ki-plus fs-2"></i> Add User
        </button>
      </div>
    </div>
    <div class="card-body pt-0">
      <div class="table-responsive">
        <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
          <thead>
            <tr class="fw-bold text-muted">
              <th class="min-w-150px">Name</th>
              <th class="min-w-200px">Email</th>
              <th class="min-w-150px">Roles</th>
              <th class="min-w-100px">Status</th>
              <th class="min-w-50px text-end">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in items" :key="item.id">
              <td class="fw-semibold">{{ item.name }}</td>
              <td>{{ item.email }}</td>
              <td>
                <span v-if="item.roles && item.roles.length" v-for="(r, ri) in item.roles" :key="r.id" class="badge badge-light-primary me-1">{{ r.name }}</span>
                <span v-else class="text-muted">—</span>
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
              <td colspan="5" class="text-center text-muted py-10">No users found</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    <div class="modal fade" tabindex="-1" ref="modalEl" data-bs-backdrop="static">
      <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h3 class="modal-title">{{ editingId ? 'Edit User' : 'Add User' }}</h3>
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
                    <label class="required fs-6 fw-semibold mb-2">Email</label>
                    <input type="email" v-model="form.email" class="form-control form-control-solid" required />
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-6">
                  <div class="fv-row mb-7">
                    <label class="fs-6 fw-semibold mb-2" :class="{ required: !editingId }">Password</label>
                    <input type="password" v-model="form.password" class="form-control form-control-solid" :required="!editingId" :placeholder="editingId ? 'Leave empty to keep current' : ''" />
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="fv-row mb-7">
                    <label class="fs-6 fw-semibold mb-2">Department</label>
                    <select v-model="form.department_id" class="form-select form-select-solid">
                      <option value="">Select department</option>
                      <option v-for="d in departments" :key="d.id" :value="d.id">{{ d.name }}</option>
                    </select>
                  </div>
                </div>
              </div>
              <div class="fv-row mb-4">
                <label class="required fs-6 fw-semibold mb-2">Roles</label>
                <div v-if="loadingRoles" class="text-muted fs-7">Loading roles...</div>
                <div v-else-if="roles.length === 0" class="text-muted fs-7">No roles available</div>
                <div v-else class="d-flex flex-wrap gap-3">
                  <label v-for="r in roles" :key="r.id" class="form-check form-check-custom form-check-solid form-check-sm">
                    <input type="checkbox" class="form-check-input" :value="r.id" v-model="form.role_ids" />
                    <span class="form-check-label">{{ r.name }}</span>
                  </label>
                </div>
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
import User from '../../../API/Modules/Core/User/User'
import API from '../../../API/API'
import Swal from 'sweetalert2'
import { notify } from '@kyvg/vue3-notification'

export default {
  name: 'UsersView',
  data() {
    return {
      api: new User('api/v1/core/user'),
      rolesApi: new API('api/v1/core/roles'),
      departmentsApi: new API('api/v1/core/department'),
      items: [], roles: [], departments: [],
      editingId: null,
      form: { name: '', email: '', password: '', role_ids: [], department_id: '' },
      saving: false, modal: null, loadingRoles: false,
    }
  },
  mounted() { this.loadItems(); this.modal = new Modal(this.$refs.modalEl) },
  methods: {
    async loadItems() { try { const d = await this.api.getAll(); this.items = d.data || d } catch { notify({ text: 'Failed to load users', type: 'error' }) } },
    async loadRoles() {
      this.loadingRoles = true
      try { const d = await this.rolesApi.getAll({ per_page: -1 }); this.roles = d.data || d || [] }
      catch { notify({ text: 'Failed to load roles', type: 'error' }); this.roles = [] }
      finally { this.loadingRoles = false }
    },
    async loadDepartments() {
      try { const d = await this.departmentsApi.getAll({ per_page: -1 }); this.departments = d.data || d || [] }
      catch { this.departments = [] }
    },
    openForm() {
      this.editingId = null
      this.form = { name: '', email: '', password: '', role_ids: [], department_id: '' }
      this.loadRoles()
      this.loadDepartments()
      this.modal.show()
    },
    async editItem(item) {
      this.editingId = item.id
      this.form = { name: item.name, email: item.email, password: '', role_ids: [], department_id: '' }
      this.loadRoles()
      this.loadDepartments()
      this.modal.show()
      try {
        const res = await this.api.show(item.id)
        const data = res.data
        if (data) {
          if (data.roles) this.form.role_ids = data.roles.map(r => r.id)
          if (data.department_id) this.form.department_id = data.department_id
        }
      } catch { notify({ text: 'Failed to load user details', type: 'error' }) }
    },
    async saveItem() {
      this.saving = true
      try {
        const fd = new FormData()
        fd.append('name', this.form.name)
        fd.append('email', this.form.email)
        if (this.form.password) {
          fd.append('password', this.form.password)
        }
        this.form.role_ids.forEach(id => fd.append('role_ids[]', id))
        if (this.form.department_id) fd.append('department_id', this.form.department_id)
        if (this.editingId) await this.api.update(this.editingId, fd)
        else await this.api.insert(fd)
        notify({ text: this.editingId ? 'User updated' : 'User created', type: 'success' })
        this.modal.hide();
        this.saving = false;
        this.loadItems()
      } catch (e) {
        const errors = e.response?.data?.errors
        if (errors) { Object.entries(errors).forEach(([field, msgs]) => msgs.forEach(msg => notify({ text: field + ': ' + msg, type: 'error' }))) }
        else { notify({ text: e.response?.data?.message || 'Error saving user', type: 'error' }) }
      }
      finally {
        this.saving = false
      }
    },
    async deleteItem(id) {
      if ((await Swal.fire({ title: 'Delete User?', text: 'This action cannot be undone.', icon: 'warning', showCancelButton: true, confirmButtonText: 'Delete' })).isConfirmed) {
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
