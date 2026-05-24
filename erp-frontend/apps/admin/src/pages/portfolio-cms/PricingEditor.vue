<script setup lang="ts">
import { RouterLink } from "vue-router"
</script>
<template>
  <div class="space-y-4">
    <div class="flex items-center gap-3">
      <RouterLink to="/cms" class="text-slate-500 hover:text-white transition-colors text-sm">← CMS</RouterLink>
      <span class="text-slate-700">/</span>
      <h1 class="text-2xl font-black text-white">Pricing Section</h1>
    </div>
    <div class="bg-blue-500/10 border border-blue-500/20 rounded-2xl p-5 text-slate-300 text-sm">
      Pricing plans are managed in the <RouterLink to="/packages" class="text-blue-400 hover:underline font-semibold">Packages</RouterLink> section.
      Changes there are automatically reflected on the pricing page.
    </div>
  </div>
</template>