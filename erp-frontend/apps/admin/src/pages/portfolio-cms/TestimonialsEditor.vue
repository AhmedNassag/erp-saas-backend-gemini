<script setup lang="ts">
import { ref, onMounted } from "vue"
import { cmsApi } from "@nexaerp/api"

const loading = ref(false)
const saving  = ref(false)
const saved   = ref(false)
const items   = ref<any[]>([])

onMounted(async () => {
  loading.value = true
  try {
    const res = await cmsApi.getTestimonials()
    items.value = res.data.data ?? res.data
  } catch {} finally { loading.value = false }
})

function add() { items.value.push({ name: "", role: "", quote_en: "", quote_ar: "", rating: 5 }) }
function remove(i: number) { items.value.splice(i, 1) }

async function save() {
  saving.value = true
  try {
    await cmsApi.updateTestimonials({ testimonials: items.value })
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
        <h1 class="text-2xl font-black text-white">Testimonials</h1>
      </div>
      <button @click="add" class="bg-white/5 border border-slate-700 text-white text-sm font-semibold px-4 py-2 rounded-xl hover:bg-white/10 transition-colors">
        + Add Testimonial
      </button>
    </div>

    <div v-if="loading" class="text-center text-slate-500 py-12">Loading...</div>
    <form v-else @submit.prevent="save" class="space-y-4">
      <div v-for="(item, i) in items" :key="i" class="bg-slate-900 border border-slate-800 rounded-2xl p-5">
        <div class="flex items-center justify-between mb-4">
          <span class="text-slate-400 text-sm font-semibold">Testimonial {{ i + 1 }}</span>
          <button type="button" @click="remove(i)" class="text-red-400 hover:text-red-300 text-xs transition-colors">Remove</button>
        </div>
        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block text-slate-400 text-xs mb-1.5">Name</label>
            <input v-model="item.name" class="w-full px-3 py-2 bg-white/4 border border-white/10 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500 transition-all" />
          </div>
          <div>
            <label class="block text-slate-400 text-xs mb-1.5">Role / Company</label>
            <input v-model="item.role" class="w-full px-3 py-2 bg-white/4 border border-white/10 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500 transition-all" />
          </div>
          <div>
            <label class="block text-slate-400 text-xs mb-1.5">Quote (EN)</label>
            <textarea v-model="item.quote_en" rows="3" class="w-full px-3 py-2 bg-white/4 border border-white/10 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500 transition-all resize-none"></textarea>
          </div>
          <div>
            <label class="block text-slate-400 text-xs mb-1.5">Quote (AR)</label>
            <textarea v-model="item.quote_ar" dir="rtl" rows="3" class="w-full px-3 py-2 bg-white/4 border border-white/10 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500 transition-all resize-none"></textarea>
          </div>
          <div>
            <label class="block text-slate-400 text-xs mb-1.5">Rating (1-5)</label>
            <input v-model.number="item.rating" type="number" min="1" max="5" class="w-full px-3 py-2 bg-white/4 border border-white/10 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500 transition-all" />
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