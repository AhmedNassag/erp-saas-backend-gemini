<script setup lang="ts">
import { ref, onMounted } from "vue"
import { subscriptionsApi } from "@nexaerp/api"

const subscriptions = ref([])
const loading = ref(false)
const meta = ref({ total: 0, current_page: 1, last_page: 1 })

async function fetchAll(page = 1) {
  loading.value = true
  try {
    const res = await subscriptionsApi.list({ page, per_page: 15 })
    subscriptions.value = res.data.data ?? res.data
    if (res.data.meta) meta.value = res.data.meta
  } finally { loading.value = false }
}

async function cancel(id: number) {
  if (!confirm("Cancel this subscription?")) return
  await subscriptionsApi.cancel(id)
  fetchAll()
}

onMounted(() => fetchAll())
</script>

<template>
  <div class="space-y-6">
    <div>
      <h1 class="text-2xl font-black text-white">Subscriptions</h1>
      <p class="text-slate-400 text-sm mt-1">Track all active and past subscriptions</p>
    </div>

    <div class="bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden">
      <div v-if="loading" class="p-12 text-center text-slate-500">Loading...</div>
      <div v-else-if="!subscriptions.length" class="p-12 text-center text-slate-500">No subscriptions found</div>
      <table v-else class="w-full">
        <thead>
          <tr class="border-b border-slate-800">
            <th class="text-left px-6 py-3 text-slate-400 text-xs font-semibold uppercase">Tenant</th>
            <th class="text-left px-6 py-3 text-slate-400 text-xs font-semibold uppercase">Package</th>
            <th class="text-left px-6 py-3 text-slate-400 text-xs font-semibold uppercase">Amount</th>
            <th class="text-left px-6 py-3 text-slate-400 text-xs font-semibold uppercase">Status</th>
            <th class="text-left px-6 py-3 text-slate-400 text-xs font-semibold uppercase">Expires</th>
            <th class="text-left px-6 py-3 text-slate-400 text-xs font-semibold uppercase">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="sub in subscriptions" :key="sub.id" class="border-b border-slate-800/50 hover:bg-white/2 transition-colors">
            <td class="px-6 py-3.5 text-white text-sm font-medium">{{ sub.tenant?.name ?? sub.tenant_id }}</td>
            <td class="px-6 py-3.5 text-slate-300 text-sm">{{ sub.package?.name ?? "—" }}</td>
            <td class="px-6 py-3.5 text-white text-sm font-semibold">${{ sub.amount ?? "—" }}</td>
            <td class="px-6 py-3.5">
              <span :class="[
                'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold',
                sub.status === 'active'    ? 'bg-green-500/15 text-green-400' :
                sub.status === 'cancelled' ? 'bg-red-500/15 text-red-400' :
                'bg-yellow-500/15 text-yellow-400'
              ]">{{ sub.status }}</span>
            </td>
            <td class="px-6 py-3.5 text-slate-400 text-sm">{{ sub.ends_at?.split("T")[0] ?? "—" }}</td>
            <td class="px-6 py-3.5">
              <button v-if="sub.status === 'active'" @click="cancel(sub.id)"
                class="text-red-400 hover:text-red-300 text-xs font-medium transition-colors">Cancel</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div v-if="meta.last_page > 1" class="flex items-center justify-center gap-2">
      <button v-for="p in meta.last_page" :key="p" @click="fetchAll(p)"
        :class="['w-8 h-8 rounded-lg text-xs font-semibold transition-colors',
          p === meta.current_page ? 'bg-blue-500 text-white' : 'bg-slate-800 text-slate-400 hover:bg-slate-700']">
        {{ p }}
      </button>
    </div>
  </div>
</template>