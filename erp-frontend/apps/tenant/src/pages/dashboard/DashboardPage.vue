<script setup lang="ts">
import { ref, onMounted } from "vue"
import { dashboardApi } from "@nexaerp/api"
const data = ref<any>(null)
const loading = ref(false)
onMounted(async () => { loading.value = true; try { const res = await dashboardApi.index(); data.value = res.data.data ?? res.data } finally { loading.value = false } })
</script>
<template>
  <div class="space-y-6">
    <div>
      <h1 class="text-2xl font-black text-white">Dashboard</h1>
      <p class="text-slate-400 text-sm mt-1">Welcome to your ERP dashboard</p>
    </div>
    <div v-if="loading" class="text-center text-slate-500 py-12">Loading...</div>
    <div v-else-if="data" class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5">
        <p class="text-slate-400 text-xs mb-1">Company</p>
        <p class="text-white font-bold text-lg">{{ data.company }}</p>
      </div>
      <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5">
        <p class="text-slate-400 text-xs mb-1">Database</p>
        <p class="text-white font-mono text-sm">{{ data.connected_database }}</p>
      </div>
      <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5">
        <p class="text-slate-400 text-xs mb-1">Active Users</p>
        <p class="text-white font-bold text-2xl">{{ data.stats?.active_users_count ?? 1 }}</p>
      </div>
      <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5">
        <p class="text-slate-400 text-xs mb-1">Package Status</p>
        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-green-500/15 text-green-400">{{ data.stats?.package_status ?? "active" }}</span>
      </div>
    </div>
  </div>
</template>