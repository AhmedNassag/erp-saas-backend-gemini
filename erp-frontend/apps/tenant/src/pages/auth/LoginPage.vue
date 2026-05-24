<script setup lang="ts">
import { ref, reactive } from "vue"
import { useRouter } from "vue-router"
import { useTenantAuthStore } from "../../stores/auth"
const auth = useTenantAuthStore()
const router = useRouter()
const loading = ref(false)
const error = ref("")
const form = reactive({ email: "", password: "" })
async function submit() {
  loading.value = true; error.value = ""
  try { await auth.login(form.email, form.password); router.push("/dashboard") }
  catch (e: any) { error.value = e?.response?.data?.message ?? "Invalid credentials" }
  finally { loading.value = false }
}
</script>
<template>
  <div class="min-h-screen bg-slate-950 flex items-center justify-center p-4">
    <div class="w-full max-w-md">
      <div class="text-center mb-8">
        <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-blue-500 to-cyan-500 flex items-center justify-center mx-auto mb-4 shadow-xl shadow-blue-500/20">
          <span class="text-white font-black text-xl">N</span>
        </div>
        <h1 class="text-2xl font-black text-white">Company Login</h1>
        <p class="text-slate-500 text-sm mt-1">Sign in to your ERP dashboard</p>
      </div>
      <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6">
        <form @submit.prevent="submit" class="space-y-4">
          <div>
            <label class="block text-slate-300 text-sm font-medium mb-2">Email</label>
            <input v-model="form.email" type="email" required placeholder="admin@company.com" class="w-full px-4 py-3 bg-white/4 border border-white/10 rounded-xl text-white text-sm placeholder-slate-600 focus:outline-none focus:border-blue-500 transition-all" />
          </div>
          <div>
            <label class="block text-slate-300 text-sm font-medium mb-2">Password</label>
            <input v-model="form.password" type="password" required placeholder="••••••••" class="w-full px-4 py-3 bg-white/4 border border-white/10 rounded-xl text-white text-sm placeholder-slate-600 focus:outline-none focus:border-blue-500 transition-all" />
          </div>
          <div v-if="error" class="bg-red-500/10 border border-red-500/20 rounded-xl px-4 py-3 text-red-400 text-sm">{{ error }}</div>
          <button type="submit" :disabled="loading" class="w-full bg-gradient-to-r from-blue-500 to-cyan-500 text-white font-bold py-3.5 rounded-xl shadow-lg hover:from-blue-600 hover:to-cyan-600 transition-all disabled:opacity-60 flex items-center justify-center gap-2">
            <svg v-if="loading" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
            {{ loading ? "Signing in..." : "Sign In" }}
          </button>
        </form>
      </div>
    </div>
  </div>
</template>