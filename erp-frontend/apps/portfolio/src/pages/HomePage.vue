<script setup lang="ts">
import { onMounted, computed } from "vue"
import { RouterLink } from "vue-router"
import { usePortfolioStore } from "../stores/portfolio"
import { useI18n } from "vue-i18n"
import TheNavbar from "../components/TheNavbar.vue"
import TheFooter from "../components/TheFooter.vue"

const store = usePortfolioStore()
const { t, locale } = useI18n()
onMounted(() => store.fetchAll())

const features = computed(() => store.features.length ? store.features : [
  { icon: "fa-users", color: "blue", title_en: "HR Management", title_ar: "إدارة الموارد البشرية", desc_en: "Streamline hiring, attendance, leaves, and performance reviews.", desc_ar: "أتمتة التوظيف والحضور والإجازات وتقييم الأداء." },
  { icon: "fa-boxes-stacked", color: "cyan", title_en: "Inventory Control", title_ar: "إدارة المخزون", desc_en: "Real-time stock tracking, multi-warehouse, automated reorders.", desc_ar: "تتبع المخزون لحظياً، متعدد المستودعات، إعادة الطلب التلقائي." },
  { icon: "fa-cash-register", color: "purple", title_en: "POS System", title_ar: "نقاط البيع", desc_en: "Lightning-fast point of sale with offline mode and analytics.", desc_ar: "نقاط بيع سريعة مع وضع عدم الاتصال والتحليلات." },
  { icon: "fa-handshake", color: "green", title_en: "CRM", title_ar: "إدارة العملاء", desc_en: "Manage leads, deals, and customer relationships visually.", desc_ar: "إدارة العملاء المحتملين والصفقات والعلاقات بصرياً." },
  { icon: "fa-money-bill-wave", color: "yellow", title_en: "Payroll", title_ar: "الرواتب", desc_en: "Automated salary calculations, tax compliance, payslips.", desc_ar: "حساب الرواتب تلقائياً، الامتثال الضريبي، قسائم الراتب." },
  { icon: "fa-chart-line", color: "red", title_en: "Accounting", title_ar: "المحاسبة", desc_en: "Full double-entry bookkeeping, reports, multi-currency.", desc_ar: "محاسبة مزدوجة كاملة، تقارير، دعم متعدد العملات." },
])

const testimonials = computed(() => store.testimonials.length ? store.testimonials : [
  { name: "Sarah Johnson", role: "CEO, TechCorp", quote_en: "NexaERP transformed how we manage our 200-person team. Absolutely game-changing.", quote_ar: "غيّر NexaERP طريقة إدارتنا لفريق من 200 شخص. تغيير جذري حقيقي.", rating: 5, initials: "SJ" },
  { name: "Michael Chen", role: "Operations Director", quote_en: "Real-time tracking across 5 warehouses. Our stockouts dropped by 90%.", quote_ar: "تتبع فوري عبر 5 مستودعات. انخفض نفاد المخزون لدينا بنسبة 90%.", rating: 5, initials: "MC" },
  { name: "Aisha Al-Rashid", role: "CFO, RetailMax", quote_en: "The accounting module gives us financial clarity we never had before.", quote_ar: "وحدة المحاسبة أعطتنا وضوحاً مالياً لم نحظَ به من قبل.", rating: 5, initials: "AA" },
])

const colorMap: Record<string, string> = { blue: "text-blue-400 bg-blue-500/10 border-blue-500/20", cyan: "text-cyan-400 bg-cyan-500/10 border-cyan-500/20", purple: "text-purple-400 bg-purple-500/10 border-purple-500/20", green: "text-green-400 bg-green-500/10 border-green-500/20", yellow: "text-yellow-400 bg-yellow-500/10 border-yellow-500/20", red: "text-red-400 bg-red-500/10 border-red-500/20" }
</script>

<template>
  <div class="min-h-screen bg-slate-950">
    <TheNavbar />

    <!-- Hero -->
    <section class="relative min-h-screen flex items-center overflow-hidden pt-20" style="background: linear-gradient(135deg, #0F172A 0%, #1E3A5F 50%, #0F172A 100%);">
      <div class="absolute inset-0" style="background: radial-gradient(ellipse 80% 60% at 50% -10%, rgba(59,130,246,0.25) 0%, transparent 70%);"></div>
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 w-full relative">
        <div class="grid lg:grid-cols-2 gap-16 items-center">
          <div>
            <div class="inline-flex items-center gap-2 glass rounded-full px-4 py-2 mb-6 border border-blue-500/20">
              <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
              <span class="text-slate-300 text-xs font-medium">{{ t("hero.badge") }}</span>
            </div>
            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black leading-tight mb-6">
              <span class="text-white">{{ t("hero.title").split(" with ")[0] }}</span><br>
              <span class="gradient-text">{{ t("hero.title").includes("with") ? "with " + t("hero.title").split(" with ")[1] : t("hero.title") }}</span>
            </h1>
            <p class="text-slate-400 text-lg leading-relaxed mb-8 max-w-lg">{{ t("hero.subtitle") }}</p>
            <div class="flex flex-col sm:flex-row gap-4">
              <RouterLink to="/pricing" class="btn-primary text-white font-semibold px-8 py-4 rounded-xl text-center shadow-xl flex items-center justify-center gap-2">
                <i class="fa-solid fa-rocket"></i> {{ t("hero.cta") }}
              </RouterLink>
              <a href="#features" class="glass border border-slate-600 hover:border-blue-500/50 text-white font-semibold px-8 py-4 rounded-xl text-center transition-all flex items-center justify-center gap-2">
                <i class="fa-solid fa-circle-play text-blue-400"></i> {{ t("hero.demo") }}
              </a>
            </div>
          </div>
          <!-- Dashboard Mockup -->
          <div class="hidden lg:block">
            <div class="rounded-2xl p-4 relative overflow-hidden" style="background: linear-gradient(135deg,#1E293B,#0F172A); border: 1px solid rgba(59,130,246,0.3); box-shadow: 0 40px 80px rgba(0,0,0,0.6);">
              <div class="flex items-center gap-2 mb-4 pb-3 border-b border-slate-700/50">
                <div class="w-3 h-3 rounded-full bg-red-500/70"></div>
                <div class="w-3 h-3 rounded-full bg-yellow-500/70"></div>
                <div class="w-3 h-3 rounded-full bg-green-500/70"></div>
                <div class="ml-4 flex-1 h-5 bg-slate-700/50 rounded-md"></div>
              </div>
              <div class="grid grid-cols-3 gap-2 mb-3">
                <div v-for="(s, i) in [{label:\"Revenue\",val:\"$84.2K\",color:\"blue\"},{label:\"Orders\",val:\"1,284\",color:\"cyan\"},{label:\"Users\",val:\"342\",color:\"purple\"}]" :key="i"
                  :class="`bg-${s.color}-500/10 border border-${s.color}-500/20 rounded-lg p-2`">
                  <div class="text-xs text-slate-400 mb-1">{{ s.label }}</div>
                  <div class="text-sm font-bold text-white">{{ s.val }}</div>
                  <div class="text-xs text-green-400">+12%</div>
                </div>
              </div>
              <div class="bg-slate-800/50 rounded-lg p-3 h-20 flex items-end gap-1">
                <div v-for="h in [40,65,45,80,55,90,70,85,60,95,75,88]" :key="h" class="flex-1 rounded-sm" :style="`height:${h}%; background: linear-gradient(to top, #3B82F6, #06B6D4); opacity: 0.7;`"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Features -->
    <section id="features" class="py-24 bg-slate-950">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
          <span class="text-blue-400 text-sm font-semibold uppercase tracking-widest">Everything You Need</span>
          <h2 class="text-3xl sm:text-4xl font-black text-white mt-3 mb-4">Powerful Features for <span class="gradient-text">Modern Business</span></h2>
        </div>
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
          <div v-for="(feat, i) in features" :key="i" class="glass rounded-2xl p-6 border border-slate-700/50 hover:border-blue-500/30 hover:-translate-y-1 transition-all duration-300">
            <div :class="[\"w-12 h-12 rounded-xl border flex items-center justify-center mb-5\", colorMap[feat.color] ?? colorMap.blue]">
              <i :class="`fa-solid ${feat.icon} text-lg`"></i>
            </div>
            <h3 class="text-white font-bold text-lg mb-2">{{ locale === \"ar\" ? feat.title_ar : feat.title_en }}</h3>
            <p class="text-slate-400 text-sm leading-relaxed">{{ locale === \"ar\" ? feat.desc_ar : feat.desc_en }}</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Stats -->
    <section class="py-20" style="background: linear-gradient(135deg, #0F172A 0%, #1E3A5F 50%, #0F172A 100%);">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-8">
          <div v-for="s in [{val:\"500+\",label:\"Companies\",icon:\"fa-building\"},{val:\"50K+\",label:\"Active Users\",icon:\"fa-users\"},{val:\"99.9%\",label:\"Uptime SLA\",icon:\"fa-server\"},{val:\"24/7\",label:\"Support\",icon:\"fa-headset\"}]" :key="s.val"
            class="glass rounded-2xl p-6 text-center border border-slate-700/50">
            <i :class="`fa-solid ${s.icon} text-blue-400 text-2xl mb-3 block`"></i>
            <div class="text-3xl font-black gradient-text mb-1">{{ s.val }}</div>
            <div class="text-slate-400 text-sm">{{ s.label }}</div>
          </div>
        </div>
      </div>
    </section>

    <!-- Testimonials -->
    <section class="py-24 bg-slate-950">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
          <h2 class="text-3xl sm:text-4xl font-black text-white mb-4">Loved by <span class="gradient-text">Businesses Worldwide</span></h2>
        </div>
        <div class="grid md:grid-cols-3 gap-6">
          <div v-for="(t, i) in testimonials" :key="i" class="glass rounded-2xl p-6 border border-slate-700/50 flex flex-col">
            <div class="flex gap-1 mb-4">
              <i v-for="s in t.rating" :key="s" class="fa-solid fa-star text-yellow-400 text-sm"></i>
            </div>
            <p class="text-slate-300 text-sm leading-relaxed flex-1 mb-6">"{{ locale === \"ar\" ? t.quote_ar : t.quote_en }}"</p>
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-cyan-500 flex items-center justify-center text-white text-sm font-bold">{{ t.initials }}</div>
              <div><div class="text-white font-semibold text-sm">{{ t.name }}</div><div class="text-slate-500 text-xs">{{ t.role }}</div></div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- CTA -->
    <section class="py-20" style="background: linear-gradient(135deg, #1E3A5F 0%, #0F172A 100%);">
      <div class="max-w-4xl mx-auto px-4 text-center">
        <h2 class="text-3xl sm:text-5xl font-black text-white mb-6">Ready to Transform<br><span class="gradient-text">Your Business?</span></h2>
        <p class="text-slate-400 text-lg mb-10 max-w-2xl mx-auto">Join 500+ companies already using NexaERP. Start your free trial today.</p>
        <RouterLink to="/pricing" class="btn-primary text-white font-bold px-10 py-4 rounded-xl shadow-2xl inline-flex items-center gap-2 text-lg">
          <i class="fa-solid fa-rocket"></i> Start Free Trial
        </RouterLink>
      </div>
    </section>

    <TheFooter />
  </div>
</template>