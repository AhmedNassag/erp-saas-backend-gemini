<script setup lang="ts">
import { ref, computed } from "vue"
import { RouterView, RouterLink, useRouter } from "vue-router"
import { useTenantAuthStore } from "../../stores/auth"
import { useI18n } from "vue-i18n"
const auth = useTenantAuthStore()
const router = useRouter()
const { t, locale } = useI18n()
const sidebarOpen = ref(true)
const navItems = computed(() => [
  { name: "Dashboard",   route: "/dashboard",       emoji: "📊" },
  { name: "Countries",   route: "/core/countries",  emoji: "��" },
  { name: "Cities",      route: "/core/cities",     emoji: "🏙️" },
  { name: "Areas",       route: "/core/areas",      emoji: "📍" },
  { name: "Branches",    route: "/core/branches",   emoji: "🏪" },
  { name: "Roles",       route: "/core/roles",      emoji: "🔐" },
])
function toggleLocale() { locale.value = locale.value === "en" ? "ar" : "en"; localStorage.setItem("locale", locale.value); document.documentElement.dir = locale.value === "ar" ? "rtl" : "ltr" }
async function handleLogout() { await auth.logout(); router.push("/login") }
</script>
<template>
  <div class="flex h-screen bg-slate-950 overflow-hidden">
    <aside :class="[sidebarOpen ? \"w-64\" : \"w-16\", \"flex flex-col bg-slate-900 border-r border-slate-800 transition-all duration-300 flex-shrink-0\"]">
      <div class="flex items-center gap-3 px-4 py-5 border-b border-slate-800">
        <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-blue-500 to-cyan-500 flex items-center justify-center flex-shrink-0 text-white font-bold text-sm">N</div>
        <span v-if="sidebarOpen" class="font-bold text-white text-sm">NexaERP <span class="text-slate-500 text-xs">Tenant</span></span>
      </div>
      <nav class="flex-1 px-2 py-4 space-y-1 overflow-y-auto">
        <RouterLink v-for="item in navItems" :key="item.route" :to="item.route"
          class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-400 hover:text-white hover:bg-white/5 transition-all duration-200"
          active-class="bg-blue-500/10 text-blue-400 border border-blue-500/20">
          <span class="text-base flex-shrink-0">{{ item.emoji }}</span>
          <span v-if="sidebarOpen" class="text-sm font-medium truncate">{{ item.name }}</span>
        </RouterLink>
      </nav>
      <div class="p-3 border-t border-slate-800">
        <div class="flex items-center gap-3 px-2 py-2">
          <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-500 to-cyan-500 flex items-center justify-center flex-shrink-0 text-white text-xs font-bold">
            {{ (auth.user?.name ?? "U").charAt(0).toUpperCase() }}
          </div>
          <div v-if="sidebarOpen" class="flex-1 min-w-0">
            <p class="text-white text-xs font-semibold truncate">{{ auth.user?.name }}</p>
            <p class="text-slate-500 text-xs">{{ auth.user?.role }}</p>
          </div>
          <button v-if="sidebarOpen" @click="handleLogout" class="text-slate-500 hover:text-red-400 transition-colors text-xs">Exit</button>
        </div>
      </div>
    </aside>
    <div class="flex-1 flex flex-col overflow-hidden">
      <header class="flex items-center justify-between px-6 py-4 bg-slate-900/50 border-b border-slate-800">
        <button @click="sidebarOpen = !sidebarOpen" class="text-slate-400 hover:text-white transition-colors text-lg">☰</button>
        <button @click="toggleLocale" class="px-3 py-1.5 bg-white/5 border border-slate-700 rounded-lg text-xs font-semibold text-slate-300 hover:text-white transition-colors">
          {{ locale === "en" ? "عربي" : "EN" }}
        </button>
      </header>
      <main class="flex-1 overflow-y-auto p-6"><RouterView /></main>
    </div>
  </div>
</template>