<script setup lang="ts">
import { ref, onMounted } from "vue"
import { cmsApi } from "@nexaerp/api"

const loading = ref(false)
const saving  = ref(false)
const saved   = ref(false)
const features = ref<any[]>([])

onMounted(async () => {
  loading.value = true
  try {
    const res = await cmsApi.getFeatures()
    features.value = res.data.data ?? res.data
  } catch {} finally { loading.value = false }
})

function addFeature() {
  features.value.push({ icon: "fa-star", title_en: "", title_ar: "", desc_en: "", desc_ar: "", color: "blue" })
}

function removeFeature(i: number) { features.value.splice(i, 1) }

async function save() {
  saving.value = true
  try {
    await cmsApi.updateFeatures({ features: features.value })
    saved.value = true
    setTimeout(() => saved.value = false, 2500)
  } finally { saving.value = false }
}
</script>

<template>
  <div class="space-y-6 max-w-4xl">
    <div class="flex items-center justify-between">
      <div class="flex items-center gap-3">
        <RouterLink to="/cms" class="text-slate-500 hover:text-white transition-colors text-sm">← CMS</RouterLink>
        <span class="text-slate-700">/</span>
        <h1 class="text-2xl font-black text-white">Features Section</h1>
      </div>
      <button @click="addFeature" class="bg-white/5 border border-slate-700 text-white text-sm font-semibold px-4 py-2 rounded-xl hover:bg-white/10 transition-colors">
        + Add Feature
      </button>
    </div>

    <div v-if="loading" class="text-center text-slate-500 py-12">Loading...</div>
    <form v-else @submit.prevent="save" class="space-y-4">
      <div v-for="(feat, i) in features" :key="i" class="bg-slate-900 border border-slate-800 rounded-2xl p-5">
        <div class="flex items-center justify-between mb-4">
          <span class="text-slate-400 text-sm font-semibold">Feature {{ i + 1 }}</span>
          <button type="button" @click="removeFeature(i)" class="text-red-400 hover:text-red-300 text-xs transition-colors">Remove</button>
        </div>
        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block text-slate-400 text-xs mb-1.5">Title (EN)</label>
            <input v-model="feat.title_en" class="w-full px-3 py-2 bg-white/4 border border-white/10 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500 transition-all" />
          </div>
          <div>
            <label class="block text-slate-400 text-xs mb-1.5">Title (AR)</label>
            <input v-model="feat.title_ar" dir="rtl" class="w-full px-3 py-2 bg-white/4 border border-white/10 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500 transition-all" />
          </div>
          <div>
            <label class="block text-slate-400 text-xs mb-1.5">Description (EN)</label>
            <textarea v-model="feat.desc_en" rows="2" class="w-full px-3 py-2 bg-white/4 border border-white/10 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500 transition-all resize-none"></textarea>
          </div>
          <div>
            <label class="block text-slate-400 text-xs mb-1.5">Description (AR)</label>
            <textarea v-model="feat.desc_ar" dir="rtl" rows="2" class="w-full px-3 py-2 bg-white/4 border border-white/10 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500 transition-all resize-none"></textarea>
          </div>
          <div>
            <label class="block text-slate-400 text-xs mb-1.5">Icon (Font Awesome class)</label>
            <input v-model="feat.icon" placeholder="fa-star" class="w-full px-3 py-2 bg-white/4 border border-white/10 rounded-xl text-white text-sm font-mono focus:outline-none focus:border-blue-500 transition-all" />
          </div>
          <div>
            <label class="block text-slate-400 text-xs mb-1.5">Color</label>
            <select v-model="feat.color" class="w-full px-3 py-2 bg-slate-800 border border-white/10 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500 transition-all">
              <option v-for="c in ['blue','cyan','purple','green','yellow','red']" :key="c" :value="c">{{ c }}</option>
            </select>
          </div>
        </div>
      </div>

      <div class="flex items-center gap-3">
        <button type="submit" :disabled="saving"
          class="bg-gradient-to-r from-blue-500 to-cyan-500 text-white font-bold px-8 py-3 rounded-xl shadow-lg hover:from-blue-600 hover:to-cyan-600 transition-all disabled:opacity-60">
          {{ saving ? "Saving..." : "Save Changes" }}
        </button>
        <span v-if="saved" class="text-green-400 text-sm">✓ Saved</span>
      </div>
    </form>
  </div>
</template>