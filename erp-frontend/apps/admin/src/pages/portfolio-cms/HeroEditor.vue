<script setup lang="ts">
import { ref, onMounted } from "vue"
import { cmsApi } from "@nexaerp/api"

const loading = ref(false)
const saving  = ref(false)
const saved   = ref(false)

const form = ref({
  badge_en: "", badge_ar: "",
  title_en: "", title_ar: "",
  subtitle_en: "", subtitle_ar: "",
  cta_primary_en: "", cta_primary_ar: "",
  cta_secondary_en: "", cta_secondary_ar: "",
})

onMounted(async () => {
  loading.value = true
  try {
    const res = await cmsApi.getHero()
    Object.assign(form.value, res.data.data ?? res.data)
  } catch {} finally { loading.value = false }
})

async function save() {
  saving.value = true
  try {
    await cmsApi.updateHero(form.value)
    saved.value = true
    setTimeout(() => saved.value = false, 2500)
  } finally { saving.value = false }
}
</script>

<template>
  <div class="space-y-6 max-w-3xl">
    <div class="flex items-center gap-3">
      <RouterLink to="/cms" class="text-slate-500 hover:text-white transition-colors text-sm">← CMS</RouterLink>
      <span class="text-slate-700">/</span>
      <h1 class="text-2xl font-black text-white">Hero Section</h1>
    </div>

    <div v-if="loading" class="text-center text-slate-500 py-12">Loading...</div>
    <form v-else @submit.prevent="save" class="space-y-5">

      <div v-for="field in [
        { key: 'badge',         label: 'Badge Text' },
        { key: 'title',         label: 'Main Title' },
        { key: 'subtitle',      label: 'Subtitle' },
        { key: 'cta_primary',   label: 'Primary CTA Button' },
        { key: 'cta_secondary', label: 'Secondary CTA Button' },
      ]" :key="field.key" class="bg-slate-900 border border-slate-800 rounded-2xl p-5">
        <h3 class="text-white font-semibold text-sm mb-4">{{ field.label }}</h3>
        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block text-slate-400 text-xs mb-1.5">English 🇬🇧</label>
            <input v-model="form[field.key + '_en']" type="text"
              class="w-full px-3 py-2.5 bg-white/4 border border-white/10 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500 transition-all" />
          </div>
          <div>
            <label class="block text-slate-400 text-xs mb-1.5">Arabic 🇸🇦</label>
            <input v-model="form[field.key + '_ar']" type="text" dir="rtl"
              class="w-full px-3 py-2.5 bg-white/4 border border-white/10 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500 transition-all" />
          </div>
        </div>
      </div>

      <div class="flex items-center gap-3">
        <button type="submit" :disabled="saving"
          class="bg-gradient-to-r from-blue-500 to-cyan-500 text-white font-bold px-8 py-3 rounded-xl shadow-lg hover:from-blue-600 hover:to-cyan-600 transition-all disabled:opacity-60 flex items-center gap-2">
          <svg v-if="saving" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
          </svg>
          {{ saving ? "Saving..." : "Save Changes" }}
        </button>
        <span v-if="saved" class="text-green-400 text-sm font-medium flex items-center gap-1">
          ✓ Saved successfully
        </span>
      </div>
    </form>
  </div>
</template>