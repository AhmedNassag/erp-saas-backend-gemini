<script setup lang="ts">
import { ref, onMounted } from "vue"
import { useTenantsStore } from "../../stores/tenants"

const store   = useTenantsStore()
const search  = ref("")
const confirm = ref<{ show: boolean; id: number | null; action: string }>({ show: false, id: null, action: "" })

onMounted(() => store.fetchAll())

async function handleAction(action: string, id: number) {
  confirm.value = { show: true, id, action }
}

async function executeAction() {
  if (!confirm.value.id) return
  if (confirm.value.action === "suspend")  await store.suspend(confirm.value.id)
  if (confirm.value.action === "activate") await store.activate(confirm.value.id)
  if (confirm.value.action === "delete")   await store.destroy(confirm.value.id)
  confirm.value = { show: false, id: null, action: "" }
}
</script>

<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-black text-white">Tenants</h1>
        <p class="text-slate-400 text-sm mt-1">Manage all subscribed companies</p>
      </div>
    </div>

    <!-- Search -->
    <div class="flex gap-3">
      <input v-model="search" @input="store.fetchAll({ search })" type="text" placeholder="Search tenants..."
        class="flex-1 px-4 py-2.5 bg-slate-900 border border-slate-800 rounded-xl text-white text-sm placeholder-slate-600 focus:outline-none focus:border-blue-500 transition-all" />
    </div>

    <!-- Table -->
    <div class="bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden">
      <div v-if="store.loading" class="p-12 text-center text-slate-500">Loading...</div>
      <div v-else-if="!store.tenants.length" class="p-12 text-center text-slate-500">No tenants found</div>
      <table v-else class="w-full">
        <thead>
          <tr class="border-b border-slate-800">
            <th class="text-left px-6 py-3 text-slate-400 text-xs font-semibold uppercase">#</th>
            <th class="text-left px-6 py-3 text-slate-400 text-xs font-semibold uppercase">Company</th>
            <th class="text-left px-6 py-3 text-slate-400 text-xs font-semibold uppercase">Domain</th>
            <th class="text-left px-6 py-3 text-slate-400 text-xs font-semibold uppercase">Package</th>
            <th class="text-left px-6 py-3 text-slate-400 text-xs font-semibold uppercase">Status</th>
            <th class="text-left px-6 py-3 text-slate-400 text-xs font-semibold uppercase">Expires</th>
            <th class="text-left px-6 py-3 text-slate-400 text-xs font-semibold uppercase">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="tenant in store.tenants" :key="tenant.id"
            class="border-b border-slate-800/50 hover:bg-white/2 transition-colors">
            <td class="px-6 py-3.5 text-slate-500 text-sm">{{ tenant.id }}</td>
            <td class="px-6 py-3.5 text-white text-sm font-medium">{{ tenant.name }}</td>
            <td class="px-6 py-3.5 text-slate-400 text-sm font-mono text-xs">{{ tenant.domain }}</td>
            <td class="px-6 py-3.5 text-slate-300 text-sm">{{ tenant.package?.name ?? "—" }}</td>
            <td class="px-6 py-3.5">
              <span :class="[
                'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold',
                tenant.status === 'active'    ? 'bg-green-500/15 text-green-400' :
                tenant.status === 'suspended' ? 'bg-yellow-500/15 text-yellow-400' :
                'bg-red-500/15 text-red-400'
              ]">{{ tenant.status }}</span>
            </td>
            <td class="px-6 py-3.5 text-slate-400 text-sm">{{ tenant.subscription_ends_at?.split("T")[0] ?? "—" }}</td>
            <td class="px-6 py-3.5">
              <div class="flex items-center gap-2">
                <RouterLink :to="`/tenants/${tenant.id}`" class="text-blue-400 hover:text-blue-300 text-xs font-medium transition-colors">View</RouterLink>
                <button v-if="tenant.status === 'active'" @click="handleAction('suspend', tenant.id)"
                  class="text-yellow-400 hover:text-yellow-300 text-xs font-medium transition-colors">Suspend</button>
                <button v-else @click="handleAction('activate', tenant.id)"
                  class="text-green-400 hover:text-green-300 text-xs font-medium transition-colors">Activate</button>
                <button @click="handleAction('delete', tenant.id)"
                  class="text-red-400 hover:text-red-300 text-xs font-medium transition-colors">Delete</button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Confirm Modal -->
    <div v-if="confirm.show" class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-4">
      <div class="bg-slate-900 border border-slate-700 rounded-2xl p-6 w-full max-w-sm">
        <h3 class="text-white font-bold text-lg mb-2">Confirm Action</h3>
        <p class="text-slate-400 text-sm mb-6">Are you sure you want to <span class="text-white font-semibold">{{ confirm.action }}</span> this tenant?</p>
        <div class="flex gap-3">
          <button @click="confirm.show = false" class="flex-1 py-2.5 bg-white/5 border border-slate-700 rounded-xl text-white text-sm font-semibold hover:bg-white/10 transition-colors">Cancel</button>
          <button @click="executeAction" class="flex-1 py-2.5 bg-red-500 rounded-xl text-white text-sm font-semibold hover:bg-red-600 transition-colors">Confirm</button>
        </div>
      </div>
    </div>
  </div>
</template>