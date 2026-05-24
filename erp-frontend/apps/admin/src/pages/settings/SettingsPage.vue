<script setup lang="ts">
import { ref } from "vue"
const tab = ref("general")
</script>
<template>
  <div class="space-y-6 max-w-2xl">
    <h1 class="text-2xl font-black text-white">Settings</h1>
    <div class="flex gap-1 p-1 bg-slate-900 rounded-xl border border-slate-800 w-fit">
      <button v-for="t in [{ key: \"general\", label: \"General\" }, { key: \"security\", label: \"Security\" }]" :key="t.key"
        @click="tab = t.key"
        :class="[\"px-4 py-2 text-sm font-semibold rounded-lg transition-all\", tab === t.key ? \"bg-blue-500 text-white\" : \"text-slate-400 hover:text-white\"]">
        {{ t.label }}
      </button>
    </div>
    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6">
      <p class="text-slate-400 text-sm">Settings panel — coming soon.</p>
    </div>
  </div>
</template>