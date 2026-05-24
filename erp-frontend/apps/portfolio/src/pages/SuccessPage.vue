<script setup lang="ts">
import { ref } from "vue"
import { useRoute, RouterLink } from "vue-router"
import TheNavbar from "../components/TheNavbar.vue"
import TheFooter from "../components/TheFooter.vue"

const route   = useRoute()
const loginUrl = route.query.url as string ?? "http://yourcompany.erp.test"
const email    = route.query.email as string ?? ""
const copied   = ref(false)

function copyUrl() {
  navigator.clipboard.writeText(loginUrl)
  copied.value = true
  setTimeout(() => copied.value = false, 2000)
}
</script>

<template>
  <div class="min-h-screen bg-slate-950">
    <TheNavbar />
    <section class="min-h-screen flex items-center justify-center pt-20 pb-16" style="background: linear-gradient(135deg, #0F172A 0%, #1E3A5F 50%, #0F172A 100%);">
      <div class="max-w-3xl mx-auto px-4 text-center">

        <!-- Animated checkmark -->
        <div class="flex justify-center mb-8">
          <div class="relative">
            <div class="w-28 h-28 rounded-full bg-green-500/10 border-2 border-green-500/30 flex items-center justify-center" style="animation: pulse 2s infinite;">
              <div class="w-20 h-20 rounded-full bg-green-500/20 flex items-center justify-center">
                <svg class="w-10 h-10" viewBox="0 0 50 50" fill="none">
                  <polyline points="10,25 22,37 40,15" stroke="#22C55E" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" style="stroke-dasharray:100; stroke-dashoffset:0;"/>
                </svg>
              </div>
            </div>
          </div>
        </div>

        <div class="inline-flex items-center gap-2 glass rounded-full px-4 py-2 mb-4 border border-green-500/20">
          <i class="fa-solid fa-party-horn text-yellow-400 text-xs"></i>
          <span class="text-slate-300 text-xs font-medium">Account Created Successfully</span>
        </div>

        <h1 class="text-4xl sm:text-5xl font-black text-white mb-4">🎉 Congratulations!</h1>
        <h2 class="text-2xl font-bold gradient-text mb-4">Your ERP is Ready!</h2>
        <p class="text-slate-400 text-lg max-w-xl mx-auto mb-8">Your dedicated business management platform has been provisioned and is ready to use.</p>

        <!-- Login URL -->
        <div class="mb-6">
          <p class="text-slate-400 text-sm mb-3">Your unique login URL:</p>
          <div class="bg-blue-500/8 border border-blue-500/30 rounded-2xl p-4 flex items-center gap-3 max-w-lg mx-auto">
            <div class="flex-1 text-left">
              <p class="text-slate-500 text-xs mb-0.5">ERP Dashboard URL</p>
              <p class="gradient-text font-bold text-lg break-all">{{ loginUrl }}</p>
            </div>
            <button @click="copyUrl" :class="[\"flex-shrink-0 w-10 h-10 rounded-xl transition-all duration-300 flex items-center justify-center\", copied ? \"bg-green-500/20 border border-green-500/30\" : \"glass border border-slate-600 hover:border-blue-500/50\"]">
              <i :class="copied ? \"fa-solid fa-check text-green-400\" : \"fa-solid fa-copy text-slate-400\"" class="text-sm"></i>
            </button>
          </div>
          <p v-if="copied" class="text-green-400 text-xs mt-2">✓ URL copied to clipboard!</p>
          <p v-if="email" class="text-slate-500 text-sm mt-3">Login with: <span class="text-slate-300 font-medium">{{ email }}</span></p>
        </div>

        <a :href="loginUrl" target="_blank"
          class="btn-primary text-white font-bold px-10 py-4 rounded-xl inline-flex items-center gap-3 shadow-2xl shadow-blue-500/30 text-lg hover:scale-105 transition-transform duration-300 mb-10">
          <i class="fa-solid fa-gauge-high"></i>
          Go to My Dashboard
          <i class="fa-solid fa-arrow-up-right-from-square text-sm opacity-70"></i>
        </a>

        <!-- Next Steps -->
        <div class="grid sm:grid-cols-3 gap-4 mb-8">
          <div v-for="s in [{icon:\"fa-gauge\",title:\"Login to Dashboard\",desc:\"Access your ERP and explore all modules.\"},{icon:\"fa-users-gear\",title:\"Setup Your Team\",desc:\"Invite members and assign roles.\"},{icon:\"fa-compass\",title:\"Explore Features\",desc:\"Discover HR, Inventory, POS, CRM.\"}]" :key="s.title"
            class="glass rounded-2xl p-5 border border-slate-700/50 text-left hover:border-blue-500/30 hover:-translate-y-1 transition-all duration-300">
            <i :class="`fa-solid ${s.icon} text-blue-400 text-xl mb-3 block`"></i>
            <h3 class="text-white font-bold text-sm mb-2">{{ s.title }}</h3>
            <p class="text-slate-400 text-xs leading-relaxed">{{ s.desc }}</p>
          </div>
        </div>

        <RouterLink to="/" class="text-slate-500 hover:text-slate-300 text-sm transition-colors flex items-center justify-center gap-2">
          <i class="fa-solid fa-arrow-left text-xs"></i> Back to NexaERP Home
        </RouterLink>
      </div>
    </section>
    <TheFooter />
  </div>
</template>