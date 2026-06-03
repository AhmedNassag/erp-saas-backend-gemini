<template>
  <div class="card card-flush">
    <div class="card-header pt-7">
      <h3 class="card-title align-items-start flex-column">
        <span class="card-label fw-bold text-dark">Roles</span>
        <span class="text-gray-400 mt-1 fw-semibold fs-6">Manage system roles</span>
      </h3>
      <div class="card-toolbar">
        <button type="button" class="btn btn-primary" @click="openForm()">
          <i class="ki-outline ki-plus fs-2"></i> Add Role
        </button>
      </div>
    </div>
    <div class="card-body pt-0">
      <div class="table-responsive">
        <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
          <thead>
            <tr class="fw-bold text-muted">
              <th class="min-w-150px">Name</th>
              <th class="min-w-100px">Guard</th>
              <th class="min-w-100px">Users Count</th>
              <th class="min-w-50px text-end">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in items" :key="item.id">
              <td><span class="badge badge-light-primary">{{ item.name }}</span></td>
              <td>{{ item.guard_name }}</td>
              <td>{{ item.users_count || 0 }}</td>
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
              <td colspan="4" class="text-center text-muted py-10">No roles found</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <div class="position-fixed top-0 start-0 w-100 h-100 bg-dark bg-opacity-50 d-flex align-items-center justify-content-center" v-if="showWizard" style="z-index: 1050;">
      <div class="card card-flush mx-3" style="max-width: 900px; width: 100%; max-height: 95vh;">
        <div class="card-header border-0 pt-6 pb-3">
          <div class="d-flex justify-content-between align-items-center w-100">
            <h3 class="mb-0">{{ editingId ? 'Edit Role' : 'Add Role' }}</h3>
            <button type="button" class="btn btn-icon btn-sm btn-light" @click="closeWizard">
              <i class="ki-outline ki-cross fs-2"></i>
            </button>
          </div>
        </div>

        <div class="card-body pt-0 pb-4 px-7">
          <div class="fv-row mb-6">
            <label class="required fs-6 fw-semibold mb-2">Role Name</label>
            <input type="text" v-model="form.name" class="form-control form-control-solid" placeholder="Enter role name" required />
            <div v-if="form.errors?.name" class="text-danger fs-7 mt-1">{{ form.errors.name }}</div>
          </div>

          <div class="d-flex align-items-center gap-3 mb-6 pb-4 border-bottom">
            <template v-for="(step, si) in wizardSteps" :key="step.module">
              <div class="d-flex align-items-center gap-2 cursor-pointer" :class="currentStep === si ? 'text-primary' : 'text-muted'" @click="currentStep = si">
                <span class="d-flex align-items-center justify-content-center rounded-circle fw-bold fs-7" style="width: 28px; height: 28px;" :class="currentStep === si ? 'bg-primary text-white' : 'border border-muted text-muted'">{{ si + 1 }}</span>
                <span class="fw-semibold fs-7 text-nowrap">{{ step.module }}</span>
              </div>
              <div v-if="si < wizardSteps.length - 1" class="flex-grow-1 border-top border-2" :class="currentStep > si ? 'border-primary' : 'border-muted'"></div>
            </template>
          </div>

          <div v-if="wizardSteps.length === 0" class="text-center py-8 text-muted">No permissions available.</div>

          <div v-else class="overflow-auto" style="max-height: 50vh;">
            <div v-if="loadingPermissions" class="text-center py-10">
              <div class="spinner-border text-primary" role="status"></div>
              <p class="text-muted mt-2">Loading permissions...</p>
            </div>
            <template v-else>
              <div v-for="group in wizardSteps[currentStep]?.subGroups" :key="group.subModule" class="mb-5">
                <div class="d-flex align-items-center justify-content-between border-bottom pb-2 mb-3">
                  <h5 class="mb-0 text-capitalize">{{ group.subModule }}</h5>
                  <button class="btn btn-sm btn-link p-0" @click="toggleSelectAll(group)">
                    <i class="ki-outline" :class="group.allSelected ? 'ki-minus-square' : 'ki-check-square'"></i>
                    {{ group.allSelected ? 'Deselect All' : 'Select All' }}
                  </button>
                </div>
                <div class="row">
                  <div v-for="perm in group.permissions" :key="perm.id" class="col-md-4 col-sm-6 mb-2">
                    <label class="form-check form-check-custom form-check-solid form-check-sm">
                      <input type="checkbox" class="form-check-input" :value="perm.id" v-model="form.permission_ids" @change="updateAllSelected(group)" />
                      <span class="form-check-label">{{ perm.name }}</span>
                    </label>
                  </div>
                </div>
              </div>
            </template>
          </div>
        </div>

        <div class="card-footer d-flex justify-content-between border-top-0 pt-0 pb-6 px-7">
          <button class="btn btn-light" @click="currentStep === 0 ? closeWizard() : currentStep--">
            {{ currentStep === 0 ? 'Cancel' : 'Back' }}
          </button>
          <button v-if="currentStep < wizardSteps.length - 1" class="btn btn-primary" @click="currentStep++">
            Next
          </button>
          <button v-else class="btn btn-primary" @click="saveItem" :disabled="saving">
            <span v-if="saving" class="spinner-border spinner-border-sm me-2"></span>
            {{ editingId ? 'Update' : 'Save' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import API from '../../../API/API'
import Swal from 'sweetalert2'
import { notify } from '@kyvg/vue3-notification'

export default {
  name: 'RolesView',
  data() {
    return {
      api: new API('api/v1/core/roles'),
      permissionsApi: new API('api/v1/core/permissions'),
      items: [],
      allPermissions: {},
      editingId: null,
      showWizard: false,
      currentStep: 0,
      saving: false,
      loadingPermissions: false,
      form: {
        name: '',
        permission_ids: [],
        errors: {},
      },
    }
  },
  computed: {
    wizardSteps() {
      if (!this.allPermissions || typeof this.allPermissions !== 'object' || Array.isArray(this.allPermissions)) return []
      const modulesMap = {}
      for (const [subModule, actions] of Object.entries(this.allPermissions)) {
        const perms = Object.values(actions)
        perms.forEach(p => {
          const mod = p.module || 'Other'
          if (!modulesMap[mod]) modulesMap[mod] = {}
          if (!modulesMap[mod][subModule]) modulesMap[mod][subModule] = []
          modulesMap[mod][subModule].push(p)
        })
      }
      const result = []
      for (const [module, subModules] of Object.entries(modulesMap)) {
        const subGroups = Object.entries(subModules).map(([subModule, permissions]) => ({
          subModule,
          permissions,
          allSelected: permissions.every(p => this.form.permission_ids.includes(p.id)),
        }))
        result.push({ module, subGroups })
      }
      return result
    }
  },
  mounted() {
    this.loadItems()
  },
  methods: {
    async loadItems() {
      try { const d = await this.api.getAll(); this.items = d.data || d }
      catch { notify({ text: 'Failed to load roles', type: 'error' }) }
    },
    async loadPermissions() {
      this.loadingPermissions = true
      try {
        const d = await this.permissionsApi.getAll({ per_page: -1 })
        this.allPermissions = d || {}
      } catch {
        notify({ text: 'Failed to load permissions', type: 'error' })
        this.allPermissions = {}
      } finally { this.loadingPermissions = false }
    },
    openForm() {
      this.editingId = null
      this.form = { name: '', permission_ids: [], errors: {} }
      this.currentStep = 0
      this.loadPermissions()
      this.showWizard = true
    },
    async editItem(item) {
      this.editingId = item.id
      this.form = { name: item.name, permission_ids: [], errors: {} }
      this.currentStep = 0
      this.loadPermissions()
      this.showWizard = true
      try {
        const res = await this.api.show(item.id)
        const roleData = res.data
        if (roleData && roleData.permissions) {
          this.form.permission_ids = roleData.permissions.map(p => p.id)
        }
      } catch { notify({ text: 'Failed to load role details', type: 'error' }) }
    },
    closeWizard() {
      this.showWizard = false
      this.currentStep = 0
      this.form.errors = {}
    },
    toggleSelectAll(group) {
      if (group.allSelected) {
        this.form.permission_ids = this.form.permission_ids.filter(id => !group.permissions.some(p => p.id === id))
      } else {
        const ids = group.permissions.map(p => p.id)
        ids.forEach(id => { if (!this.form.permission_ids.includes(id)) this.form.permission_ids.push(id) })
      }
      group.allSelected = !group.allSelected
    },
    updateAllSelected(group) {
      const selected = group.permissions.every(p => this.form.permission_ids.includes(p.id))
      if (group.allSelected !== selected) group.allSelected = selected
    },
    async saveItem() {
      if (!this.form.name.trim()) {
        this.form.errors = { name: 'Role name is required' }
        return
      }
      this.saving = true
      try {
        const fd = new FormData()
        fd.append('name', this.form.name)
        this.form.permission_ids.forEach(id => fd.append('permission_ids[]', id))
        if (this.editingId) await this.api.update(this.editingId, fd)
        else await this.api.insert(fd)
        notify({ text: this.editingId ? 'Role updated' : 'Role created', type: 'success' })
        this.closeWizard()
        this.loadItems()
      } catch (e) {
        const msg = e.response?.data?.message || 'Error saving role'
        if (e.response?.data?.errors) this.form.errors = e.response.data.errors
        notify({ text: msg, type: 'error' })
      } finally { this.saving = false }
    },
    async deleteItem(id) {
      if ((await Swal.fire({ title: 'Delete Role?', text: 'This action cannot be undone.', icon: 'warning', showCancelButton: true, confirmButtonText: 'Delete' })).isConfirmed) {
        try { await this.api.delete(id); this.loadItems() }
        catch { notify({ text: 'Failed to delete', type: 'error' }) }
      }
    }
  }
}
</script>
