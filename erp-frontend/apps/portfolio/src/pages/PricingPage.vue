<script setup lang="ts">
import { ref, onMounted, computed } from "vue"
import { RouterLink } from "vue-router"
import { usePortfolioStore } from "../stores/portfolio"
import { useI18n } from "vue-i18n"
import TheNavbar from "../components/TheNavbar.vue"
import TheFooter from "../components/TheFooter.vue"

const store = usePortfolioStore()
const { t } = useI18n()
const annual = ref(false)

onMounted(() => store.fetchPackages())

const plans = computed(() => store.packages.length ? store.packages : [
  { id: 1, name: "Starter", price: 29, annual_price: 23, description: "Perfect for small businesses.", popular: false, limit_users: 10, features: ["Up to 10 Users","HR Management","Basic Inventory","POS System","Email Support"] },
  { id: 2, name: "Professional", price: 79, annual_price: 63, description: "For growing businesses.", popular: true, limit_users: 50, features: ["Up to 50 Users","Full HR Suite","Advanced Inventory","POS + Multi-Register","CRM Module","Payroll","Full Accounting","Priority Support"] },
  { id: 3, name: "Enterprise", price: 199, annual_price: 159, description: "For large organizations.", popular: false, limit_users: -1, features: ["Unlimited Users","Everything in Pro","Custom Integrations","Dedicated Manager","24/7 Phone Support","SLA Guarantee"] },
])
</script>

<template>
  <div class="min-h-screen bg-slate-950">
    <TheNavbar />

    <!-- Hero -->
    <section class="relative pt-32 pb-20 overflow-hidden" style="background: linear-gradient(135deg, #0F172A 0%, #1E3A5F 50%, #0F172A 100%);">
      <div class="max-w-7xl mx-auto px-4 text-center">
        <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black text-white mb-6">Plans for Every <span class="gradient-text">Business Size</span></h1>
        <p class="text-slate-400 text-lg max-w-2xl mx-auto">No hidden fees. No long-term contracts. Start free, scale as you grow.</p>
      </div>
    </section>

    <!-- Toggle + Cards -->
    <section class="py-20 bg-slate-950">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Toggle -->
        <div class="flex items-center justify-center gap-4 mb-14">
          <span :class="!annual ? \"text-white font-semibold\" : \"text-slate-500\"" class="text-sm transition-colors">{{ t("pricing.monthly") }}</span>
          <button @click="annual = !annual" :class="[\"relative w-14 h-7 rounded-full border transition-all duration-300 focus:outline-none\", annual ? \"bg-blue-500/20 border-blue-500/50\" : \"bg-white/8 border-slate-600\"]">
            <div :class="[\"absolute top-0.5 left-0.5 w-6 h-6 rounded-full transition-all duration-300 shadow-md\", annual ? \"translate-x-7 bg-gradient-to-r from-blue-500 to-cyan-500\" : \"bg-slate-400\"]"></div>
          </button>
          <span :class="annual ? \"text-white font-semibold\" : \"text-slate-500\"" class="text-sm transition-colors">
            {{ t("pricing.annual") }}
            <span class="ml-1.5 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-green-500/20 text-green-400 border border-green-500/30">{{ t("pricing.save") }}</span>
          </span>
        </div>

        <!-- Cards -->
        <div class="grid md:grid-cols-3 gap-6 items-start">
          <div v-for="plan in plans" :key="plan.id"
            :class="[\"rounded-2xl border overflow-hidden relative transition-all duration-300 hover:-translate-y-1\", plan.popular ? \"bg-blue-500/10 border-blue-500/40 shadow-2xl shadow-blue-500/10\" : \"glass border-slate-700/50\"]">
            <div v-if="plan.popular" class="h-1 bg-gradient-to-r from-blue-500 to-cyan-500"></div>
            <div class="p-7">
              <div v-if="plan.popular" class="absolute top-4 right-4">
                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-blue-500 text-white">
                  <i class="fa-solid fa-star text-xs"></i> {{ t("pricing.mostPopular") }}
                </span>
              </div>
              <h3 class="text-white font-black text-xl mb-2">{{ plan.name }}</h3>
              <p class="text-slate-400 text-sm mb-5">{{ plan.description }}</p>
              <div class="mb-6">
                <div class="flex items-end gap-1">
                  <span class="text-slate-400 text-lg">$</span>
                  <span class="text-5xl font-black text-white leading-none">{{ annual ? (plan.annual_price ?? Math.round(plan.price * 0.8)) : plan.price }}</span>
                  <span class="text-slate-400 text-sm mb-1">{{ t("pricing.perMonth") }}</span>
                </div>
                <p v-if="annual" class="text-green-400 text-xs mt-1">Billed annually</p>
              </div>
              <RouterLink :to="`/subscribe/${plan.id}`"
                :class="[\"block text-white font-bold py-3.5 rounded-xl text-center transition-all duration-300 mb-7\", plan.popular ? \"bg-gradient-to-r from-blue-500 to-cyan-500 shadow-xl shadow-blue-500/20 hover:from-blue-600 hover:to-cyan-600\" : \"glass border border-slate-600 hover:border-blue-500/50 hover:bg-white/5\"]">
                {{ t("pricing.getStarted") }}
              </RouterLink>
              <div class="space-y-2.5">
                <div v-for="feat in (Array.isArray(plan.features) ? plan.features : Object.values(plan.features ?? {}))" :key="feat" class="flex items-center gap-3">
                  <div class="w-5 h-5 rounded-full bg-blue-500/10 flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-check text-blue-400" style="font-size:9px"></i>
                  </div>
                  <span class="text-slate-300 text-sm">{{ feat }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="text-center mt-10">
          <div class="inline-flex items-center gap-3 glass rounded-full px-6 py-3 border border-green-500/20">
            <i class="fa-solid fa-shield-halved text-green-400"></i>
            <span class="text-slate-300 text-sm">30-day money-back guarantee</span>
          </div>
        </div>
      </div>
    </section>

    <TheFooter />
  </div>
</template>