<script setup lang="ts">
import { onMounted } from "vue"
import { useStatsStore } from "../../stores/stats"
import { useTenantsStore } from "../../stores/tenants"
import { useI18n } from "vue-i18n"

const stats   = useStatsStore()
const tenants = useTenantsStore()
const { t }   = useI18n()

onMounted(async () => {
  await Promise.all([stats.fetchOverview(), tenants.fetchAll({ per_page: 5 })])
})

const statCards = [
  { key: "total_tenants",        label: "admin.totalTenants",        icon: "🏢", color: "from-blue-500 to-blue-600" },
  { key: "active_subscriptions", label: "admin.activeSubscriptions", icon: "💳", color: "from-green-500 to-green-600" },
  { key: "monthly_revenue",      label: "admin.monthlyRevenue",      icon: "💰", color: "from-purple-500 to-purple-600" },
  { key: "total_users",          label: "admin.totalUsers",          icon: "👥", color: "from-cyan-500 to-cyan-600" },
]
</script>

<template>
  <div class="space-y-6">
    <!-- Page Header -->
    <div>
      <h1 class="text-2xl font-black text-white">{{ t("admin.dashboard") }}</h1>
      <p class="text-slate-400 text-sm mt-1">Welcome back! Here is what is happening with NexaERP.</p>
    </div>

    <!-- Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <div v-for="card in statCards" :key="card.key"
        class="bg-slate-900 border border-slate-800 rounded-2xl p-5 hover:border-slate-700 transition-colors">
        <div class="flex items-center justify-between mb-4">
          <div :class="['w-10 h-10 rounded-xl bg-gradient-to-br flex items-center justify-center text-lg', card.color]">
            {{ card.icon }}
          </div>
          <span class="text-green-400 text-xs font-semibold bg-green-500/10 px-2 py-1 rounded-full">+12%</span>
        </div>
        <div class="text-2xl font-black text-white mb-1">
          {{ stats.loading ? "..." : (stats.overview?.[card.key] ?? "—") }}
        </div>
        <div class="text-slate-400 text-xs">{{ t(card.label) }}</div>
      </div>
    </div>

    <!-- Recent Tenants -->
    <div class="bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden">
      <div class="flex items-center justify-between px-6 py-4 border-b border-slate-800">
        <h2 class="text-white font-bold">Recent Tenants</h2>
        <RouterLink to="/tenants" class="text-blue-400 text-sm hover:underline">View all →</RouterLink>
      </div>
      <div v-if="tenants.loading" class="p-8 text-center text-slate-500 text-sm">Loading...</div>
      <div v-else-if="!tenants.tenants.length" class="p-8 text-center text-slate-500 text-sm">No tenants yet</div>
      <table v-else class="w-full">
        <thead>
          <tr class="border-b border-slate-800">
            <th class="text-left px-6 py-3 text-slate-400 text-xs font-semibold uppercase">Company</th>
            <th class="text-left px-6 py-3 text-slate-400 text-xs font-semibold uppercase">Domain</th>
            <th class="text-left px-6 py-3 text-slate-400 text-xs font-semibold uppercase">Status</th>
            <th class="text-left px-6 py-3 text-slate-400 text-xs font-semibold uppercase">Expires</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="tenant in tenants.tenants" :key="tenant.id"
            class="border-b border-slate-800/50 hover:bg-white/2 transition-colors">
            <td class="px-6 py-3.5 text-white text-sm font-medium">{{ tenant.name }}</td>
            <td class="px-6 py-3.5 text-slate-400 text-sm font-mono">{{ tenant.domain }}</td>
            <td class="px-6 py-3.5">
              <span :class="[
                'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold',
                tenant.status === 'active' ? 'bg-green-500/15 text-green-400' : 'bg-red-500/15 text-red-400'
              ]">{{ tenant.status }}</span>
            </td>
            <td class="px-6 py-3.5 text-slate-400 text-sm">{{ tenant.subscription_ends_at?.split("T")[0] ?? "—" }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>