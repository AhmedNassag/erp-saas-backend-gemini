<script setup lang="ts">
import { useToast } from "@nexaerp/ui"
const { toasts } = useToast()
</script>
<template>
  <div class="fixed bottom-4 right-4 z-50 space-y-2 pointer-events-none">
    <TransitionGroup name="toast">
      <div v-for="toast in toasts" :key="toast.id"
        :class="[
          \"pointer-events-auto px-4 py-3 rounded-xl text-sm font-medium shadow-xl border flex items-center gap-2 min-w-64\",
          toast.type === \"success\" ? \"bg-green-500/15 border-green-500/30 text-green-400\" :
          toast.type === \"error\"   ? \"bg-red-500/15 border-red-500/30 text-red-400\" :
          toast.type === \"warning\" ? \"bg-yellow-500/15 border-yellow-500/30 text-yellow-400\" :
          \"bg-blue-500/15 border-blue-500/30 text-blue-400\"
        ]">
        <span>{{ toast.message }}</span>
      </div>
    </TransitionGroup>
  </div>
</template>
<style scoped>
.toast-enter-active, .toast-leave-active { transition: all 0.3s ease; }
.toast-enter-from { opacity: 0; transform: translateX(20px); }
.toast-leave-to   { opacity: 0; transform: translateX(20px); }
</style>