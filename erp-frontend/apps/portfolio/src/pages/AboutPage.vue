<script setup lang="ts">
import TheNavbar from "../components/TheNavbar.vue"
import TheFooter from "../components/TheFooter.vue"
import { RouterLink } from "vue-router"
</script>
<template>
  <div class="min-h-screen bg-slate-950">
    <TheNavbar />
    <section class="relative pt-32 pb-20 overflow-hidden" style="background: linear-gradient(135deg, #0F172A 0%, #1E3A5F 50%, #0F172A 100%);">
      <div class="max-w-7xl mx-auto px-4 text-center">
        <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black text-white mb-6">About <span class="gradient-text">NexaERP</span></h1>
        <p class="text-slate-400 text-lg max-w-2xl mx-auto">We are on a mission to make enterprise-grade business management accessible to every company.</p>
      </div>
    </section>

    <!-- Mission & Vision -->
    <section class="py-24 bg-slate-950">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid md:grid-cols-2 gap-8 mb-20">
          <div class="glass rounded-2xl p-8 border border-slate-700/50 hover:border-blue-500/30 transition-all duration-300">
            <div class="w-14 h-14 rounded-2xl bg-blue-500/10 border border-blue-500/20 flex items-center justify-center mb-6">
              <i class="fa-solid fa-bullseye text-blue-400 text-2xl"></i>
            </div>
            <h3 class="text-2xl font-black text-white mb-4">Our Mission</h3>
            <p class="text-slate-400 leading-relaxed">To democratize enterprise resource planning by delivering powerful, intuitive, and affordable cloud-based ERP solutions that empower businesses of all sizes to operate at their full potential.</p>
          </div>
          <div class="glass rounded-2xl p-8 border border-slate-700/50 hover:border-cyan-500/30 transition-all duration-300">
            <div class="w-14 h-14 rounded-2xl bg-cyan-500/10 border border-cyan-500/20 flex items-center justify-center mb-6">
              <i class="fa-solid fa-eye text-cyan-400 text-2xl"></i>
            </div>
            <h3 class="text-2xl font-black text-white mb-4">Our Vision</h3>
            <p class="text-slate-400 leading-relaxed">To become the world most trusted ERP platform — a single source of truth for every business operation, from the first employee to the ten-thousandth.</p>
          </div>
        </div>

        <!-- Timeline -->
        <div class="text-center mb-12">
          <h2 class="text-3xl sm:text-4xl font-black text-white mb-4">The <span class="gradient-text">NexaERP Story</span></h2>
        </div>
        <div class="relative pl-8 border-l-2 border-slate-700 space-y-10 max-w-3xl mx-auto mb-20">
          <div v-for="m in [{year:\"2020\",title:\"The Beginning\",desc:\"Founded by enterprise software veterans frustrated with complex ERP solutions.\",icon:\"fa-seedling\",color:\"text-green-400 bg-green-500/10 border-green-500/30\"},{year:\"2021\",title:\"First 50 Clients\",desc:\"Launched MVP with HR and Inventory modules. 50 companies signed up in 6 months.\",icon:\"fa-users\",color:\"text-blue-400 bg-blue-500/10 border-blue-500/30\"},{year:\"2022\",title:\"Full Platform Launch\",desc:\"Launched POS, CRM, Payroll, and Accounting. Series A funding of $8M.\",icon:\"fa-rocket\",color:\"text-purple-400 bg-purple-500/10 border-purple-500/30\"},{year:\"2024\",title:\"500+ Companies\",desc:\"Powering 500+ companies across 30 countries with 50,000+ active users.\",icon:\"fa-trophy\",color:\"text-yellow-400 bg-yellow-500/10 border-yellow-500/30\"}]" :key="m.year"
            class="relative">
            <div class="absolute -left-[2.85rem] top-1 w-6 h-6 rounded-full bg-gradient-to-r from-blue-500 to-cyan-500 border-2 border-slate-950 flex items-center justify-center">
              <div class="w-2 h-2 bg-white rounded-full"></div>
            </div>
            <div class="glass rounded-2xl p-6 border border-slate-700/50 hover:border-blue-500/30 transition-all duration-300">
              <div class="flex items-start gap-4">
                <div :class="[\"w-12 h-12 rounded-xl border flex items-center justify-center flex-shrink-0\", m.color]">
                  <i :class="`fa-solid ${m.icon} text-lg`"></i>
                </div>
                <div>
                  <div class="flex items-center gap-3 mb-2">
                    <span class="gradient-text font-black text-xl">{{ m.year }}</span>
                    <h3 class="text-white font-bold text-lg">{{ m.title }}</h3>
                  </div>
                  <p class="text-slate-400 text-sm leading-relaxed">{{ m.desc }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Values -->
        <div class="text-center mb-12">
          <h2 class="text-3xl sm:text-4xl font-black text-white mb-4">Our Core <span class="gradient-text">Values</span></h2>
        </div>
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-16">
          <div v-for="v in [{icon:\"fa-heart\",color:\"text-red-400 bg-red-500/10 border-red-500/20\",title:\"Customer First\",desc:\"Every decision starts with: how does this help our customers succeed?\"},{icon:\"fa-lightbulb\",color:\"text-yellow-400 bg-yellow-500/10 border-yellow-500/20\",title:\"Innovation\",desc:\"We constantly push boundaries and challenge the status quo.\"},{icon:\"fa-shield-halved\",color:\"text-blue-400 bg-blue-500/10 border-blue-500/20\",title:\"Trust & Security\",desc:\"Enterprise-grade security is non-negotiable.\"},{icon:\"fa-handshake\",color:\"text-green-400 bg-green-500/10 border-green-500/20\",title:\"Transparency\",desc:\"Honest pricing, clear communication, no hidden surprises.\"},{icon:\"fa-bolt\",color:\"text-cyan-400 bg-cyan-500/10 border-cyan-500/20\",title:\"Speed & Reliability\",desc:\"99.9% uptime SLA. Your business never stops, so neither do we.\"},{icon:\"fa-globe\",color:\"text-purple-400 bg-purple-500/10 border-purple-500/20\",title:\"Global Mindset\",desc:\"Multi-language, multi-currency, and multi-timezone support.\"}]" :key="v.title"
            class="glass rounded-2xl p-6 border border-slate-700/50 hover:border-blue-500/30 hover:-translate-y-1 transition-all duration-300">
            <div :class="[\"w-12 h-12 rounded-xl border flex items-center justify-center mb-4\", v.color]">
              <i :class="`fa-solid ${v.icon} text-lg`"></i>
            </div>
            <h3 class="text-white font-bold text-lg mb-2">{{ v.title }}</h3>
            <p class="text-slate-400 text-sm leading-relaxed">{{ v.desc }}</p>
          </div>
        </div>

        <!-- CTA -->
        <div class="glass rounded-3xl p-12 border border-blue-500/20 text-center" style="background: linear-gradient(135deg, rgba(59,130,246,0.1), rgba(6,182,212,0.05));">
          <h2 class="text-3xl sm:text-4xl font-black text-white mb-4">Join the <span class="gradient-text">NexaERP Family</span></h2>
          <p class="text-slate-400 text-lg mb-8 max-w-xl mx-auto">Whether you are a 5-person startup or a 500-person enterprise, NexaERP scales with you.</p>
          <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <RouterLink to="/pricing" class="btn-primary text-white font-bold px-8 py-4 rounded-xl shadow-xl flex items-center justify-center gap-2">
              <i class="fa-solid fa-rocket"></i> Start Free Trial
            </RouterLink>
            <RouterLink to="/contact" class="glass border border-slate-600 hover:border-blue-500/50 text-white font-semibold px-8 py-4 rounded-xl transition-all flex items-center justify-center gap-2 hover:bg-white/5">
              <i class="fa-solid fa-message text-blue-400"></i> Talk to Sales
            </RouterLink>
          </div>
        </div>
      </div>
    </section>
    <TheFooter />
  </div>
</template>