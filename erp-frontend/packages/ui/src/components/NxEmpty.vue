<script setup lang="ts">
defineProps<{ message?: string }>()
</script>
<template>
  <div class="flex flex-col items-center justify-center py-16 text-slate-500">
    <svg class="w-12 h-12 mb-4 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0H4"/>
    </svg>
    <p class="text-sm">{{ message ?? 'No data found' }}</p>
  </div>
</template>
