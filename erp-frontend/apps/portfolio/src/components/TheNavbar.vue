<script setup lang="ts">
import { ref, onMounted, onUnmounted } from "vue"
import { RouterLink } from "vue-router"
import { useI18n } from "vue-i18n"
const { t, locale } = useI18n()
const scrolled = ref(false)
const mobileOpen = ref(false)
function onScroll() { scrolled.value = window.scrollY > 20 }
onMounted(() => window.addEventListener("scroll", onScroll))
onUnmounted(() => window.removeEventListener("scroll", onScroll))
function toggleLocale() { locale.value = locale.value === "en" ? "ar" : "en"; localStorage.setItem("locale", locale.value); document.documentElement.dir = locale.value === "ar" ? "rtl" : "ltr" }
</script>
<template>
  <nav :class="[scrolled ? \"bg-slate-900/95 backdrop-blur-xl shadow-2xl border-b border-slate-800\" : \"bg-transparent\", \"fixed top-0 left-0 right-0 z-50 transition-all duration-500\"]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex items-center justify-between h-16 lg:h-20">
        <RouterLink to="/" class="flex items-center gap-3 group">
          <div class="w-9 h-9 rounded-xl btn-primary flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
            <i class="fa-solid fa-bolt text-white text-sm"></i>
          </div>
          <span class="text-xl font-bold"><span class="gradient-text">Nexa</span><span class="text-white">ERP</span></span>
        </RouterLink>
        <div class="hidden lg:flex items-center gap-8">
          <RouterLink to="/"        class="text-slate-300 hover:text-white text-sm font-medium transition-colors">{{ t("nav.home") }}</RouterLink>
          <RouterLink to="/about"   class="text-slate-300 hover:text-white text-sm font-medium transition-colors">{{ t("nav.about") }}</RouterLink>
          <RouterLink to="/pricing" class="text-slate-300 hover:text-white text-sm font-medium transition-colors">{{ t("nav.pricing") }}</RouterLink>
          <RouterLink to="/contact" class="text-slate-300 hover:text-white text-sm font-medium transition-colors">{{ t("nav.contact") }}</RouterLink>
        </div>
        <div class="hidden lg:flex items-center gap-3">
          <button @click="toggleLocale" class="px-3 py-1.5 glass rounded-lg text-xs font-semibold text-slate-300 hover:text-white transition-colors border border-slate-700">
            {{ locale === "en" ? "عربي" : "EN" }}
          </button>
          <RouterLink to="/pricing" class="btn-primary text-white text-sm font-semibold px-6 py-2.5 rounded-xl shadow-lg">
            {{ t("nav.startTrial") }}
          </RouterLink>
        </div>
        <button @click="mobileOpen = !mobileOpen" class="lg:hidden text-slate-300 hover:text-white p-2 rounded-lg">
          <i :class="mobileOpen ? \"fa-solid fa-xmark\" : \"fa-solid fa-bars\"" class="text-xl"></i>
        </button>
      </div>
    </div>
    <div v-if="mobileOpen" class="lg:hidden bg-slate-900/95 backdrop-blur-xl border-t border-slate-800 px-4 py-4 space-y-2">
      <RouterLink v-for="l in [{to:\"/\",label:t(\"nav.home\")},{to:\"/about\",label:t(\"nav.about\")},{to:\"/pricing\",label:t(\"nav.pricing\")},{to:\"/contact\",label:t(\"nav.contact\")}]" :key="l.to" :to="l.to" @click="mobileOpen=false" class="block px-4 py-2.5 text-slate-300 hover:text-white hover:bg-white/5 rounded-lg transition-all">{{ l.label }}</RouterLink>
      <div class="flex gap-2 pt-2">
        <button @click="toggleLocale" class="px-3 py-2 glass rounded-lg text-xs font-semibold text-slate-300 border border-slate-700">{{ locale === "en" ? "عربي" : "EN" }}</button>
        <RouterLink to="/pricing" @click="mobileOpen=false" class="flex-1 btn-primary text-white text-center font-semibold px-4 py-2 rounded-xl text-sm">{{ t("nav.startTrial") }}</RouterLink>
      </div>
    </div>
  </nav>
</template>