<script setup lang="ts">
defineProps<{
  modelValue?: string | number
  label?: string
  placeholder?: string
  type?: string
  error?: string
  required?: boolean
  disabled?: boolean
}>()
defineEmits<{ 'update:modelValue': [value: string] }>()
</script>

<template>
  <div class="flex flex-col gap-1.5">
    <label v-if="label" class="text-slate-300 text-sm font-medium">
      {{ label }} <span v-if="required" class="text-red-400">*</span>
    </label>
    <input
      :type="type ?? 'text'"
      :value="modelValue"
      :placeholder="placeholder"
      :disabled="disabled"
      :required="required"
      @input="$emit('update:modelValue', ($event.target as HTMLInputElement).value)"
      :class="[
        'w-full px-4 py-3 rounded-xl text-sm text-white placeholder-slate-500 transition-all duration-200',
        'bg-white/4 border focus:outline-none focus:ring-2 focus:ring-offset-0',
        error
          ? 'border-red-500/50 focus:border-red-500 focus:ring-red-500/20'
          : 'border-white/10 focus:border-blue-500 focus:ring-blue-500/20 focus:bg-blue-500/5',
        disabled ? 'opacity-50 cursor-not-allowed' : '',
      ]"
    />
    <p v-if="error" class="text-red-400 text-xs flex items-center gap-1">
      <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
      </svg>
      {{ error }}
    </p>
  </div>
</template>
