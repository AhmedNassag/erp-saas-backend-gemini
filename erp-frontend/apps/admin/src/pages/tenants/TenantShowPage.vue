<script setup lang="ts">
import { onMounted } from "vue"
import { useRoute } from "vue-router"
import { useTenantsStore } from "../../stores/tenants"

const route = useRoute()
const store = useTenantsStore()
onMounted(() => store.fetchOne(Number(route.params.id)))
</script>
<template>
  <div class="space-y-6 max-w-3xl">
    <div class="flex items-center gap-3">
      <RouterLink to="/tenants" class="text-slate-500 hover:text-white transition-colors text-sm">← Tenants</RouterLink>
      <span class="text-slate-700">/</span>
      <h1 class="text-2xl font-black text-white">Tenant Details</h1>
    </div>
    <div v-if="store.loading" class="text-center text-slate-500 py-12">Loading...</div>
    <div v-else-if="store.tenant" class="bg-slate-900 border border-slate-800 rounded-2xl p-6 space-y-4">
      <div v-for="[key, val] in Object.entries(store.tenant)" :key="key" class="flex items-start gap-4 py-3 border-b border-slate-800/50 last:border-0">
        <span class="text-slate-500 text-sm w-40 flex-shrink-0 font-mono">{{ key }}</span>
        <span class="text-white text-sm break-all">{{ val ?? "—" }}</span>
      </div>
    </div>
  </div>
</template>