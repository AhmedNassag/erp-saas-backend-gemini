<script setup lang="ts">
import { useI18n } from "vue-i18n"
import { RouterLink } from "vue-router"
const { t } = useI18n()

const sections = [
  { title: "Hero Section",     desc: "Edit the main headline, subtitle, and CTA buttons",     icon: "🎯", route: "/cms/hero" },
  { title: "Features Section", desc: "Manage the 6 feature cards shown on the homepage",      icon: "⚡", route: "/cms/features" },
  { title: "Pricing Section",  desc: "Control pricing plans displayed on the pricing page",   icon: "💰", route: "/cms/pricing" },
  { title: "Testimonials",     desc: "Add, edit, or remove customer testimonials",            icon: "💬", route: "/cms/testimonials" },
]
</script>

<template>
  <div class="space-y-6">
    <div>
      <h1 class="text-2xl font-black text-white">Portfolio CMS</h1>
      <p class="text-slate-400 text-sm mt-1">Control the content displayed on your public website</p>
    </div>

    <!-- Live Preview Link -->
    <div class="bg-blue-500/10 border border-blue-500/20 rounded-2xl p-4 flex items-center justify-between">
      <div class="flex items-center gap-3">
        <span class="text-2xl">🌐</span>
        <div>
          <p class="text-white font-semibold text-sm">Live Website</p>
          <p class="text-slate-400 text-xs">Changes are reflected immediately on the public site</p>
        </div>
      </div>
      <a href="http://erp.test:8000" target="_blank"
        class="bg-blue-500 text-white text-xs font-semibold px-4 py-2 rounded-xl hover:bg-blue-600 transition-colors">
        Preview Site →
      </a>
    </div>

    <!-- CMS Sections Grid -->
    <div class="grid sm:grid-cols-2 gap-4">
      <RouterLink v-for="section in sections" :key="section.route" :to="section.route"
        class="bg-slate-900 border border-slate-800 rounded-2xl p-5 hover:border-blue-500/30 hover:bg-blue-500/5 transition-all duration-200 group">
        <div class="flex items-start gap-4">
          <div class="w-12 h-12 rounded-xl bg-white/5 flex items-center justify-center text-2xl flex-shrink-0 group-hover:bg-blue-500/10 transition-colors">
            {{ section.icon }}
          </div>
          <div class="flex-1">
            <h3 class="text-white font-bold mb-1">{{ section.title }}</h3>
            <p class="text-slate-400 text-sm">{{ section.desc }}</p>
          </div>
          <span class="text-slate-600 group-hover:text-blue-400 transition-colors text-lg">→</span>
        </div>
      </RouterLink>
    </div>
  </div>
</template>