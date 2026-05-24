<script setup lang="ts">
import { ref, reactive, computed, onMounted } from "vue"
import { useRoute, useRouter } from "vue-router"
import { usePortfolioStore } from "../stores/portfolio"
import { portfolioApi } from "@nexaerp/api"
import { useI18n } from "vue-i18n"
import TheNavbar from "../components/TheNavbar.vue"
import TheFooter from "../components/TheFooter.vue"

const route  = useRoute()
const router = useRouter()
const store  = usePortfolioStore()
const { t }  = useI18n()

const step    = ref(1)
const loading = ref(false)
const error   = ref("")

const form = reactive({ company_name: "", subdomain: "", admin_name: "", admin_email: "", admin_password: "", password_confirmation: "" })

const plan = computed(() => store.packages.find((p: any) => p.id == route.params.id) ?? { name: "Professional", price: 79, id: route.params.id })

onMounted(() => { if (!store.packages.length) store.fetchPackages() })

const subdomainPreview = computed(() => form.subdomain ? `${form.subdomain}.erp.test` : "yourname.erp.test")

async function submit() {
  if (form.admin_password !== form.password_confirmation) { error.value = "Passwords do not match"; return }
  loading.value = true; error.value = ""
  try {
    const res = await portfolioApi.subscribe(Number(route.params.id), form)
    router.push({ name: "success", query: { url: res.data.login_url, email: form.admin_email } })
  } catch (e: any) {
    error.value = e?.response?.data?.message ?? "Something went wrong. Please try again."
  } finally { loading.value = false }
}
</script>

<template>
  <div class="min-h-screen bg-slate-950">
    <TheNavbar />
    <section class="pt-32 pb-12" style="background: linear-gradient(135deg, #0F172A 0%, #1E3A5F 50%, #0F172A 100%);">
      <div class="max-w-7xl mx-auto px-4 text-center">
        <h1 class="text-3xl sm:text-4xl font-black text-white mb-3">Subscribe to <span class="gradient-text">{{ plan.name }}</span> Plan</h1>
        <p class="text-slate-400">Complete the form to create your dedicated ERP environment.</p>
      </div>
    </section>

    <section class="py-12 bg-slate-950">
      <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-3 gap-8 items-start">

          <!-- Form -->
          <div class="lg:col-span-2">
            <!-- Steps -->
            <div class="flex items-center mb-8">
              <div v-for="(s, i) in [\"Company Info\",\"Admin Account\",\"Confirm\"]" :key="i" class="flex items-center" :class="i < 2 ? \"flex-1\" : \"\"">
                <div class="flex flex-col items-center">
                  <div :class="[\"w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold transition-all duration-300\", step > i+1 ? \"bg-green-500 text-white\" : step === i+1 ? \"bg-gradient-to-r from-blue-500 to-cyan-500 text-white\" : \"bg-slate-800 text-slate-500\"]">
                    <span v-if="step <= i+1">{{ i+1 }}</span>
                    <i v-else class="fa-solid fa-check text-xs"></i>
                  </div>
                  <span :class="[\"text-xs mt-1.5 font-medium whitespace-nowrap\", step === i+1 ? \"text-white\" : step > i+1 ? \"text-green-400\" : \"text-slate-600\"]">{{ s }}</span>
                </div>
                <div v-if="i < 2" :class="[\"flex-1 h-0.5 mx-3 mb-5 rounded-full transition-all duration-500\", step > i+1 ? \"bg-gradient-to-r from-blue-500 to-cyan-500\" : \"bg-slate-800\"]"></div>
              </div>
            </div>

            <div class="glass rounded-2xl border border-slate-700/50 overflow-hidden">
              <form @submit.prevent="submit">

                <!-- Step 1 -->
                <div v-if="step === 1" class="p-8">
                  <h2 class="text-white font-black text-xl mb-6">Company Information</h2>
                  <div class="space-y-5">
                    <div>
                      <label class="block text-slate-300 text-sm font-medium mb-2">Company Name <span class="text-red-400">*</span></label>
                      <input v-model="form.company_name" type="text" required placeholder="Acme Corporation" class="w-full px-4 py-3 bg-white/4 border border-white/10 rounded-xl text-white text-sm placeholder-slate-600 focus:outline-none focus:border-blue-500 transition-all" />
                    </div>
                    <div>
                      <label class="block text-slate-300 text-sm font-medium mb-2">Subdomain <span class="text-red-400">*</span></label>
                      <div class="relative">
                        <input v-model="form.subdomain" type="text" required placeholder="acme" pattern="[a-zA-Z]+" minlength="3" maxlength="20" class="w-full px-4 py-3 bg-white/4 border border-white/10 rounded-xl text-white text-sm placeholder-slate-600 focus:outline-none focus:border-blue-500 transition-all pr-28" />
                        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-500 text-sm">.erp.test</span>
                      </div>
                      <div v-if="form.subdomain.length >= 3" class="mt-2 bg-blue-500/8 border border-blue-500/20 rounded-lg px-4 py-2.5 flex items-center gap-2">
                        <i class="fa-solid fa-globe text-blue-400 text-xs"></i>
                        <span class="text-slate-300 text-sm">Your URL: <span class="text-white font-semibold">{{ subdomainPreview }}</span></span>
                      </div>
                    </div>
                  </div>
                  <div class="mt-8 flex justify-end">
                    <button type="button" @click="step = 2" :disabled="!form.company_name || form.subdomain.length < 3"
                      class="bg-gradient-to-r from-blue-500 to-cyan-500 text-white font-bold px-8 py-3 rounded-xl flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed shadow-lg">
                      Next Step <i class="fa-solid fa-arrow-right text-sm"></i>
                    </button>
                  </div>
                </div>

                <!-- Step 2 -->
                <div v-if="step === 2" class="p-8">
                  <h2 class="text-white font-black text-xl mb-6">Admin Account</h2>
                  <div class="space-y-5">
                    <div v-for="f in [{key:\"admin_name\",label:\"Full Name\",type:\"text\",ph:\"John Smith\"},{key:\"admin_email\",label:\"Email Address\",type:\"email\",ph:\"admin@company.com\"},{key:\"admin_password\",label:\"Password\",type:\"password\",ph:\"Min. 6 characters\"},{key:\"password_confirmation\",label:\"Confirm Password\",type:\"password\",ph:\"Repeat password\"}]" :key="f.key">
                      <label class="block text-slate-300 text-sm font-medium mb-2">{{ f.label }} <span class="text-red-400">*</span></label>
                      <input v-model="form[f.key]" :type="f.type" :placeholder="f.ph" required class="w-full px-4 py-3 bg-white/4 border border-white/10 rounded-xl text-white text-sm placeholder-slate-600 focus:outline-none focus:border-blue-500 transition-all" />
                    </div>
                  </div>
                  <div class="mt-8 flex justify-between">
                    <button type="button" @click="step = 1" class="glass border border-slate-600 text-white font-semibold px-6 py-3 rounded-xl flex items-center gap-2 hover:bg-white/5 transition-all">
                      <i class="fa-solid fa-arrow-left text-sm"></i> Back
                    </button>
                    <button type="button" @click="step = 3" :disabled="!form.admin_name || !form.admin_email || !form.admin_password"
                      class="bg-gradient-to-r from-blue-500 to-cyan-500 text-white font-bold px-8 py-3 rounded-xl flex items-center gap-2 disabled:opacity-50 shadow-lg">
                      Review Order <i class="fa-solid fa-arrow-right text-sm"></i>
                    </button>
                  </div>
                </div>

                <!-- Step 3 -->
                <div v-if="step === 3" class="p-8">
                  <h2 class="text-white font-black text-xl mb-6">Confirm & Launch</h2>
                  <div class="space-y-3 mb-6">
                    <div v-for="item in [{label:\"Company\",val:form.company_name},{label:\"Your ERP URL\",val:subdomainPreview},{label:\"Admin\",val:form.admin_name},{label:\"Email\",val:form.admin_email}]" :key="item.label"
                      class="glass rounded-xl p-4 border border-slate-700/50">
                      <p class="text-slate-500 text-xs mb-0.5">{{ item.label }}</p>
                      <p :class="item.label === \"Your ERP URL\" ? \"gradient-text font-bold\" : \"text-white font-semibold\"">{{ item.val }}</p>
                    </div>
                  </div>
                  <div v-if="error" class="bg-red-500/10 border border-red-500/20 rounded-xl px-4 py-3 text-red-400 text-sm mb-4">{{ error }}</div>
                  <div class="flex justify-between">
                    <button type="button" @click="step = 2" class="glass border border-slate-600 text-white font-semibold px-6 py-3 rounded-xl flex items-center gap-2 hover:bg-white/5 transition-all">
                      <i class="fa-solid fa-arrow-left text-sm"></i> Back
                    </button>
                    <button type="submit" :disabled="loading"
                      class="bg-gradient-to-r from-blue-500 to-cyan-500 text-white font-bold px-8 py-3 rounded-xl flex items-center gap-2 shadow-xl disabled:opacity-70">
                      <i v-if="loading" class="fa-solid fa-spinner fa-spin"></i>
                      <i v-else class="fa-solid fa-rocket"></i>
                      {{ loading ? "Creating your ERP..." : "Launch My ERP" }}
                    </button>
                  </div>
                </div>

              </form>
            </div>
          </div>

          <!-- Order Summary -->
          <div class="glass rounded-2xl border border-blue-500/20 p-6 sticky top-24" style="background: linear-gradient(135deg, rgba(59,130,246,0.1), rgba(6,182,212,0.05));">
            <h3 class="text-white font-black text-lg mb-4">Order Summary</h3>
            <div class="flex items-center gap-3 p-4 rounded-xl bg-blue-500/10 border border-blue-500/20 mb-5">
              <div class="w-10 h-10 rounded-xl bg-gradient-to-r from-blue-500 to-cyan-500 flex items-center justify-center flex-shrink-0">
                <i class="fa-solid fa-rocket text-white text-sm"></i>
              </div>
              <div><p class="text-white font-bold">{{ plan.name }} Plan</p><p class="text-slate-400 text-xs">Monthly billing</p></div>
            </div>
            <div class="flex items-end justify-between mb-5 pb-5 border-b border-slate-700/50">
              <span class="text-slate-400 text-sm">Monthly Total</span>
              <div class="text-right"><span class="text-3xl font-black gradient-text">${{ plan.price }}</span><span class="text-slate-400 text-sm">/mo</span></div>
            </div>
            <div class="space-y-2 pt-4 border-t border-slate-700/50">
              <div class="flex items-center gap-2 text-xs text-slate-400"><i class="fa-solid fa-shield-halved text-green-400"></i> 30-day money-back guarantee</div>
              <div class="flex items-center gap-2 text-xs text-slate-400"><i class="fa-solid fa-lock text-blue-400"></i> Secure SSL encrypted checkout</div>
              <div class="flex items-center gap-2 text-xs text-slate-400"><i class="fa-solid fa-bolt text-yellow-400"></i> Instant account provisioning</div>
            </div>
          </div>
        </div>
      </div>
    </section>
    <TheFooter />
  </div>
</template>