<script setup lang="ts">
import { ref, reactive } from "vue"
import { useRouter } from "vue-router"
import { useAuthStore } from "../../stores/auth"
import { useI18n } from "vue-i18n"

const auth   = useAuthStore()
const router = useRouter()
const { t }  = useI18n()

const method  = ref<"password" | "email_otp" | "mobile_otp">("password")
const step    = ref<1 | 2>(1)
const loading = ref(false)
const error   = ref("")

const form = reactive({ email: "", password: "", mobile: "", otp: "" })

async function submit() {
  loading.value = true
  error.value   = ""
  try {
    if (method.value === "password") {
      await auth.loginWithPassword(form.email, form.password)
      router.push("/dashboard")
    } else if (method.value === "email_otp") {
      if (step.value === 1) { await auth.sendEmailOtp(form.email); step.value = 2 }
      else { await auth.verifyEmailOtp(form.email, form.otp); router.push("/dashboard") }
    } else {
      if (step.value === 1) { await auth.sendMobileOtp(form.mobile); step.value = 2 }
      else { await auth.verifyMobileOtp(form.mobile, form.otp); router.push("/dashboard") }
    }
  } catch (e: any) {
    error.value = e?.response?.data?.message ?? "Authentication failed"
  } finally { loading.value = false }
}
</script>

<template>
  <div class="min-h-screen bg-slate-950 flex items-center justify-center p-4">
    <div class="w-full max-w-md">
      <!-- Logo -->
      <div class="text-center mb-8">
        <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-blue-500 to-cyan-500 flex items-center justify-center mx-auto mb-4 shadow-xl shadow-blue-500/20">
          <span class="text-white font-black text-xl">N</span>
        </div>
        <h1 class="text-2xl font-black text-white">NexaERP <span class="text-slate-400 font-normal text-lg">Admin</span></h1>
        <p class="text-slate-500 text-sm mt-1">Sign in to your admin panel</p>
      </div>

      <!-- Method Tabs -->
      <div class="flex gap-1 p-1 bg-slate-900 rounded-xl border border-slate-800 mb-6">
        <button v-for="m in [
          { key: 'password',   label: 'Password' },
          { key: 'email_otp',  label: 'Email OTP' },
          { key: 'mobile_otp', label: 'Mobile OTP' },
        ]" :key="m.key"
          @click="method = m.key as any; step = 1; error = ''"
          :class="[
            'flex-1 py-2 text-xs font-semibold rounded-lg transition-all duration-200',
            method === m.key ? 'bg-blue-500 text-white shadow-lg' : 'text-slate-400 hover:text-white'
          ]">
          {{ m.label }}
        </button>
      </div>

      <!-- Form Card -->
      <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6">
        <form @submit.prevent="submit" class="space-y-4">

          <!-- Password method -->
          <template v-if="method === 'password'">
            <div>
              <label class="block text-slate-300 text-sm font-medium mb-2">Email</label>
              <input v-model="form.email" type="email" required placeholder="admin@erp.test"
                class="w-full px-4 py-3 bg-white/4 border border-white/10 rounded-xl text-white text-sm placeholder-slate-600 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500/30 transition-all" />
            </div>
            <div>
              <label class="block text-slate-300 text-sm font-medium mb-2">Password</label>
              <input v-model="form.password" type="password" required placeholder="••••••••"
                class="w-full px-4 py-3 bg-white/4 border border-white/10 rounded-xl text-white text-sm placeholder-slate-600 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500/30 transition-all" />
            </div>
          </template>

          <!-- Email OTP method -->
          <template v-else-if="method === 'email_otp'">
            <div v-if="step === 1">
              <label class="block text-slate-300 text-sm font-medium mb-2">Email Address</label>
              <input v-model="form.email" type="email" required placeholder="admin@erp.test"
                class="w-full px-4 py-3 bg-white/4 border border-white/10 rounded-xl text-white text-sm placeholder-slate-600 focus:outline-none focus:border-blue-500 transition-all" />
            </div>
            <div v-else>
              <p class="text-slate-400 text-sm mb-3">Code sent to <span class="text-white font-medium">{{ form.email }}</span></p>
              <label class="block text-slate-300 text-sm font-medium mb-2">Verification Code</label>
              <input v-model="form.otp" type="text" required placeholder="123456" maxlength="6"
                class="w-full px-4 py-3 bg-white/4 border border-white/10 rounded-xl text-white text-sm text-center tracking-widest text-lg placeholder-slate-600 focus:outline-none focus:border-blue-500 transition-all" />
            </div>
          </template>

          <!-- Mobile OTP method -->
          <template v-else>
            <div v-if="step === 1">
              <label class="block text-slate-300 text-sm font-medium mb-2">Mobile Number</label>
              <input v-model="form.mobile" type="tel" required placeholder="01000000000"
                class="w-full px-4 py-3 bg-white/4 border border-white/10 rounded-xl text-white text-sm placeholder-slate-600 focus:outline-none focus:border-blue-500 transition-all" />
            </div>
            <div v-else>
              <p class="text-slate-400 text-sm mb-3">Code sent to <span class="text-white font-medium">{{ form.mobile }}</span></p>
              <label class="block text-slate-300 text-sm font-medium mb-2">Verification Code</label>
              <input v-model="form.otp" type="text" required placeholder="123456" maxlength="6"
                class="w-full px-4 py-3 bg-white/4 border border-white/10 rounded-xl text-white text-sm text-center tracking-widest text-lg placeholder-slate-600 focus:outline-none focus:border-blue-500 transition-all" />
            </div>
          </template>

          <!-- Error -->
          <div v-if="error" class="bg-red-500/10 border border-red-500/20 rounded-xl px-4 py-3 text-red-400 text-sm">
            {{ error }}
          </div>

          <!-- Submit -->
          <button type="submit" :disabled="loading"
            class="w-full bg-gradient-to-r from-blue-500 to-cyan-500 text-white font-bold py-3.5 rounded-xl shadow-lg shadow-blue-500/20 hover:from-blue-600 hover:to-cyan-600 transition-all duration-200 disabled:opacity-60 flex items-center justify-center gap-2">
            <svg v-if="loading" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
            </svg>
            <span>{{ step === 2 ? "Verify Code" : (method === "password" ? "Sign In" : "Send Code") }}</span>
          </button>

          <button v-if="step === 2" type="button" @click="step = 1"
            class="w-full text-slate-500 hover:text-slate-300 text-sm transition-colors">
            ← Back
          </button>
        </form>
      </div>
    </div>
  </div>
</template>