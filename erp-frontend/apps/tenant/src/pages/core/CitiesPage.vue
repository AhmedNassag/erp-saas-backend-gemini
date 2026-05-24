<script setup lang="ts">
import { ref, onMounted } from "vue"
import { citiesApi } from "@nexaerp/api"

const items   = ref([])
const loading = ref(false)
const meta    = ref({ total: 0, current_page: 1, last_page: 1 })
const search  = ref("")

async function fetchAll(page = 1) {
  loading.value = true
  try {
    const res = await citiesApi.list({ page, per_page: 15, search: search.value })
    items.value = res.data.data ?? res.data
    if (res.data.meta) meta.value = res.data.meta
  } finally { loading.value = false }
}

async function remove(id: number) {
  if (!confirm("Delete this record?")) return
  await citiesApi.destroy(id)
  fetchAll()
}

onMounted(() => fetchAll())
</script>

<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-black text-white">Cities</h1>
    </div>
    <div class="flex gap-3">
      <input v-model="search" @input="fetchAll()" type="text" placeholder="Search..."
        class="flex-1 px-4 py-2.5 bg-slate-900 border border-slate-800 rounded-xl text-white text-sm placeholder-slate-600 focus:outline-none focus:border-blue-500 transition-all" />
    </div>
    <div class="bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden">
      <div v-if="loading" class="p-12 text-center text-slate-500">Loading...</div>
      <div v-else-if="!items.length" class="p-12 text-center text-slate-500">No records found</div>
      <table v-else class="w-full">
        <thead>
          <tr class="border-b border-slate-800">
            <th class="text-left px-6 py-3 text-slate-400 text-xs font-semibold uppercase">#</th>
            <th class="text-left px-6 py-3 text-slate-400 text-xs font-semibold uppercase">Name</th>
            <th class="text-left px-6 py-3 text-slate-400 text-xs font-semibold uppercase">Status</th>
            <th class="text-left px-6 py-3 text-slate-400 text-xs font-semibold uppercase">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="item in items" :key="item.id" class="border-b border-slate-800/50 hover:bg-white/2 transition-colors">
            <td class="px-6 py-3.5 text-slate-500 text-sm">{{ item.id }}</td>
            <td class="px-6 py-3.5 text-white text-sm font-medium">{{ typeof item.name === "object" ? (item.name?.en ?? item.name?.ar ?? JSON.stringify(item.name)) : item.name }}</td>
            <td class="px-6 py-3.5">
              <span :class="[\"inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold\", item.status == 1 ? \"bg-green-500/15 text-green-400\" : \"bg-slate-700 text-slate-400\"]">
                {{ item.status == 1 ? "Active" : "Inactive" }}
              </span>
            </td>
            <td class="px-6 py-3.5">
              <button @click="remove(item.id)" class="text-red-400 hover:text-red-300 text-xs font-medium transition-colors">Delete</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <div v-if="meta.last_page > 1" class="flex items-center justify-center gap-2">
      <button v-for="p in meta.last_page" :key="p" @click="fetchAll(p)"
        :class="[\"w-8 h-8 rounded-lg text-xs font-semibold transition-colors\", p === meta.current_page ? \"bg-blue-500 text-white\" : \"bg-slate-800 text-slate-400 hover:bg-slate-700\"]">
        {{ p }}
      </button>
    </div>
  </div>
</template>