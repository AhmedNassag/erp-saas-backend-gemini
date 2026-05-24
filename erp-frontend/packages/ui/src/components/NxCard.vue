<script setup lang="ts">
defineProps<{ padding?: string }>()
</script>
<template>
  <div :class="['bg-white/5 backdrop-blur-sm border border-white/10 rounded-2xl', padding ?? 'p-6']">
    <slot />
  </div>
</template>
