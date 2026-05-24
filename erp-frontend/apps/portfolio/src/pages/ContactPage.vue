<script setup lang="ts">
import { ref, reactive } from "vue"
import { useI18n } from "vue-i18n"
import { portfolioApi } from "@nexaerp/api"
import TheNavbar from "../components/TheNavbar.vue"
import TheFooter from "../components/TheFooter.vue"

const { t } = useI18n()
const submitting = ref(false)
const submitted  = ref(false)
const error      = ref("")
const openFaq    = ref<number | null>(null)

const form = reactive({ name: "", email: "", company: "", subject: "", message: "" })

async function submit() {
  submitting.value = true; error.value = ""
  try {
    await portfolioApi.contact(form)
    submitted.value = true
  } catch (e: any) {
    error.value = e?.response?.data?.message ?? "Failed to send. Please try again."
  } finally { submitting.value = false }
}

const faqs = [
  { q: "How quickly can I get started?", a: "You can be up and running in under 10 minutes. Sign up, choose your plan, and your ERP is provisioned instantly." },
  { q: "Do you offer a free trial?", a: "Yes! We offer a 14-day free trial with full access to all features. No credit card required." },
  { q: "Can I migrate data from my existing system?", a: "Absolutely. We provide data migration tools and dedicated support to help you import your existing data." },
  { q: "Is my data secure?", a: "Security is our top priority. We use AES-256 encryption, SOC 2 compliance, daily backups, and each client gets their own isolated database." },
  { q: "What kind of support do you offer?", a: "All plans include email support. Professional and Enterprise plans include priority support with guaranteed response times." },
]
</script>

<template>
  <div class="min-h-screen bg-slate-950">
    <TheNavbar />

    <section class="relative pt-32 pb-20 overflow-hidden" style="background: linear-gradient(135deg, #0F172A 0%, #1E3A5F 50%, #0F172A 100%);">
      <div class="max-w-7xl mx-auto px-4 text-center">
        <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black text-white mb-6">Get in <span class="gradient-text">Touch</span></h1>
        <p class="text-slate-400 text-lg max-w-2xl mx-auto">Have a question or ready to get started? Our team is here to help.</p>
      </div>
    </section>

    <!-- Contact Cards -->
    <section class="py-16 bg-slate-950">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid md:grid-cols-3 gap-6 mb-16">
          <div v-for="c in [{icon:\"fa-envelope\",color:\"text-blue-400 bg-blue-500/10 border-blue-500/20\",title:\"Email Us\",val:\"hello@nexaerp.com\",sub:\"We reply within 2 hours\"},{icon:\"fa-phone\",color:\"text-green-400 bg-green-500/10 border-green-500/20\",title:\"Call Us\",val:\"+1 (234) 567-890\",sub:\"Mon-Fri, 9am-6pm EST\"},{icon:\"fa-location-dot\",color:\"text-purple-400 bg-purple-500/10 border-purple-500/20\",title:\"Visit Us\",val:\"123 Tech Park, Silicon Valley\",sub:\"CA 94025, United States\"}]" :key="c.title"
            class="glass rounded-2xl p-6 border border-slate-700/50 text-center hover:border-blue-500/30 hover:-translate-y-1 transition-all duration-300">
            <div :class="[\"w-14 h-14 rounded-2xl border flex items-center justify-center mx-auto mb-5\", c.color]">
              <i :class="`fa-solid ${c.icon} text-xl`"></i>
            </div>
            <h3 class="text-white font-bold text-lg mb-2">{{ c.title }}</h3>
            <p class="gradient-text font-semibold mb-1">{{ c.val }}</p>
            <p class="text-slate-500 text-sm">{{ c.sub }}</p>
          </div>
        </div>

        <!-- Form -->
        <div class="max-w-2xl mx-auto">
          <div class="glass rounded-2xl p-8 border border-slate-700/50 mb-12">
            <h2 class="text-2xl font-black text-white mb-6">Send Us a Message</h2>
            <div v-if="submitted" class="bg-green-500/10 border border-green-500/20 rounded-xl p-6 text-center">
              <i class="fa-solid fa-check-circle text-green-400 text-3xl mb-3 block"></i>
              <p class="text-green-400 font-semibold">Message sent successfully!</p>
              <p class="text-slate-400 text-sm mt-1">We will get back to you within 24 hours.</p>
            </div>
            <form v-else @submit.prevent="submit" class="space-y-5">
              <div class="grid sm:grid-cols-2 gap-4">
                <div>
                  <label class="block text-slate-300 text-sm font-medium mb-2">{{ t("contact.name") }} <span class="text-red-400">*</span></label>
                  <input v-model="form.name" type="text" required :placeholder="t(\"contact.name\")" class="w-full px-4 py-3 bg-white/4 border border-white/10 rounded-xl text-white text-sm placeholder-slate-600 focus:outline-none focus:border-blue-500 transition-all" />
                </div>
                <div>
                  <label class="block text-slate-300 text-sm font-medium mb-2">{{ t("contact.email") }} <span class="text-red-400">*</span></label>
                  <input v-model="form.email" type="email" required :placeholder="t(\"contact.email\")" class="w-full px-4 py-3 bg-white/4 border border-white/10 rounded-xl text-white text-sm placeholder-slate-600 focus:outline-none focus:border-blue-500 transition-all" />
                </div>
              </div>
              <div>
                <label class="block text-slate-300 text-sm font-medium mb-2">{{ t("contact.company") }}</label>
                <input v-model="form.company" type="text" :placeholder="t(\"contact.company\")" class="w-full px-4 py-3 bg-white/4 border border-white/10 rounded-xl text-white text-sm placeholder-slate-600 focus:outline-none focus:border-blue-500 transition-all" />
              </div>
              <div>
                <label class="block text-slate-300 text-sm font-medium mb-2">{{ t("contact.subject") }} <span class="text-red-400">*</span></label>
                <select v-model="form.subject" required class="w-full px-4 py-3 bg-slate-800 border border-white/10 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500 transition-all">
                  <option value="" disabled selected>Select a subject...</option>
                  <option v-for="s in [\"Sales Inquiry\",\"Request a Demo\",\"Technical Support\",\"Billing Question\",\"Partnership\",\"Other\"]" :key="s" :value="s">{{ s }}</option>
                </select>
              </div>
              <div>
                <label class="block text-slate-300 text-sm font-medium mb-2">{{ t("contact.message") }} <span class="text-red-400">*</span></label>
                <textarea v-model="form.message" rows="5" required :placeholder="t(\"contact.message\")" class="w-full px-4 py-3 bg-white/4 border border-white/10 rounded-xl text-white text-sm placeholder-slate-600 focus:outline-none focus:border-blue-500 transition-all resize-none"></textarea>
              </div>
              <div v-if="error" class="bg-red-500/10 border border-red-500/20 rounded-xl px-4 py-3 text-red-400 text-sm">{{ error }}</div>
              <button type="submit" :disabled="submitting" class="w-full btn-primary text-white font-bold py-4 rounded-xl shadow-xl flex items-center justify-center gap-2 disabled:opacity-70">
                <i v-if="submitting" class="fa-solid fa-spinner fa-spin"></i>
                {{ submitting ? t("contact.sending") : submitted ? t("contact.sent") : t("contact.send") }}
              </button>
            </form>
          </div>

          <!-- FAQ -->
          <div>
            <h2 class="text-2xl font-black text-white mb-6 text-center">Frequently Asked <span class="gradient-text">Questions</span></h2>
            <div class="space-y-3">
              <div v-for="(faq, i) in faqs" :key="i" class="glass rounded-xl border border-slate-700/50 overflow-hidden">
                <button @click="openFaq = openFaq === i ? null : i" class="w-full flex items-center justify-between px-6 py-4 text-left hover:bg-white/3 transition-colors">
                  <span class="text-white font-semibold text-sm pr-4">{{ faq.q }}</span>
                  <div :class="[\"w-7 h-7 rounded-lg bg-blue-500/10 flex items-center justify-center flex-shrink-0 transition-transform duration-300\", openFaq === i ? \"rotate-45\" : \"\"]">
                    <i class="fa-solid fa-plus text-blue-400 text-xs"></i>
                  </div>
                </button>
                <div v-if="openFaq === i" class="px-6 pb-5">
                  <p class="text-slate-400 text-sm leading-relaxed">{{ faq.a }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <TheFooter />
  </div>
</template>