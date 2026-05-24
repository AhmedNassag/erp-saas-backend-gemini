<script setup lang="ts">
import { RouterLink } from "vue-router"
import { useI18n } from "vue-i18n"
const { t } = useI18n()
</script>
<template>
  <footer class="bg-slate-900 border-t border-slate-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-10">
        <div>
          <RouterLink to="/" class="flex items-center gap-3 mb-4">
            <div class="w-9 h-9 rounded-xl btn-primary flex items-center justify-center"><i class="fa-solid fa-bolt text-white text-sm"></i></div>
            <span class="text-xl font-bold"><span class="gradient-text">Nexa</span><span class="text-white">ERP</span></span>
          </RouterLink>
          <p class="text-slate-400 text-sm leading-relaxed mb-5">The future of business management. Streamline operations and scale with confidence.</p>
          <div class="flex gap-3">
            <a v-for="icon in [\"twitter\",\"linkedin-in\",\"github\",\"youtube\"]" :key="icon" href="#" class="w-9 h-9 glass rounded-lg flex items-center justify-center text-slate-400 hover:text-electric-blue transition-all duration-200">
              <i :class="`fa-brands fa-${icon} text-sm`"></i>
            </a>
          </div>
        </div>
        <div>
          <h4 class="text-white font-semibold mb-5 text-sm uppercase tracking-wider">Product</h4>
          <ul class="space-y-3">
            <li v-for="l in [{to:\"/pricing\",label:\"Pricing\"},{to:\"/#features\",label:\"Features\"},{to:\"/#how-it-works\",label:\"How It Works\"}]" :key="l.to">
              <RouterLink :to="l.to" class="text-slate-400 hover:text-white text-sm transition-colors flex items-center gap-2"><i class="fa-solid fa-chevron-right text-xs text-electric-blue"></i>{{ l.label }}</RouterLink>
            </li>
          </ul>
        </div>
        <div>
          <h4 class="text-white font-semibold mb-5 text-sm uppercase tracking-wider">Company</h4>
          <ul class="space-y-3">
            <li v-for="l in [{to:\"/about\",label:\"About\"},{to:\"/contact\",label:\"Contact\"}]" :key="l.to">
              <RouterLink :to="l.to" class="text-slate-400 hover:text-white text-sm transition-colors flex items-center gap-2"><i class="fa-solid fa-chevron-right text-xs text-electric-blue"></i>{{ l.label }}</RouterLink>
            </li>
          </ul>
        </div>
        <div>
          <h4 class="text-white font-semibold mb-5 text-sm uppercase tracking-wider">Contact</h4>
          <ul class="space-y-4">
            <li class="flex items-start gap-3"><div class="w-8 h-8 rounded-lg bg-electric-blue/10 flex items-center justify-center flex-shrink-0"><i class="fa-solid fa-envelope text-electric-blue text-xs"></i></div><a href="mailto:hello@nexaerp.com" class="text-slate-300 hover:text-white text-sm transition-colors">hello@nexaerp.com</a></li>
          </ul>
        </div>
      </div>
      <div class="mt-12 pt-8 border-t border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-4">
        <p class="text-slate-500 text-sm">&copy; {{ new Date().getFullYear() }} <span class="gradient-text font-semibold">NexaERP</span>. All rights reserved.</p>
      </div>
    </div>
  </footer>
</template>