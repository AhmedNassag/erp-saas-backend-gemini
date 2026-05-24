<script setup lang="ts">
defineProps<{ variant?: 'success' | 'warning' | 'danger' | 'info' | 'default' }>()
</script>
<template>
  <span :class="[
    'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold',
    variant === 'success' ? 'bg-green-500/15 text-green-400 border border-green-500/20' : '',
    variant === 'warning' ? 'bg-yellow-500/15 text-yellow-400 border border-yellow-500/20' : '',
    variant === 'danger'  ? 'bg-red-500/15 text-red-400 border border-red-500/20' : '',
    variant === 'info'    ? 'bg-blue-500/15 text-blue-400 border border-blue-500/20' : '',
    !variant || variant === 'default' ? 'bg-slate-500/15 text-slate-400 border border-slate-500/20' : '',
  ]"><slot /></span>
</template>
