<script setup lang="ts">
defineProps<{
  variant?: 'primary' | 'secondary' | 'danger' | 'ghost'
  size?: 'sm' | 'md' | 'lg'
  loading?: boolean
  disabled?: boolean
  type?: 'button' | 'submit' | 'reset'
}>()
</script>

<template>
  <button
    :type="type ?? 'button'"
    :disabled="disabled || loading"
    :class="[
      'inline-flex items-center justify-center gap-2 font-semibold rounded-xl transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-slate-900',
      size === 'sm'  ? 'px-4 py-2 text-sm'  : '',
      size === 'lg'  ? 'px-8 py-4 text-lg'  : '',
      !size || size === 'md' ? 'px-6 py-3 text-sm' : '',
      variant === 'primary' || !variant
        ? 'bg-gradient-to-r from-blue-500 to-cyan-500 text-white hover:from-blue-600 hover:to-cyan-600 shadow-lg shadow-blue-500/20 focus:ring-blue-500'
        : '',
      variant === 'secondary'
        ? 'bg-white/5 border border-slate-600 text-white hover:bg-white/10 hover:border-blue-500/50 focus:ring-slate-500'
        : '',
      variant === 'danger'
        ? 'bg-red-500/10 border border-red-500/30 text-red-400 hover:bg-red-500/20 focus:ring-red-500'
        : '',
      variant === 'ghost'
        ? 'text-slate-400 hover:text-white hover:bg-white/5 focus:ring-slate-500'
        : '',
      (disabled || loading) ? 'opacity-50 cursor-not-allowed' : '',
    ]"
  >
    <svg v-if="loading" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
    </svg>
    <slot />
  </button>
</template>
