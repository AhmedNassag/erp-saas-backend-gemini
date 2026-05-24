import { defineStore } from 'pinia'
import { ref } from 'vue'
import { portfolioApi } from '@nexaerp/api'

export const usePortfolioStore = defineStore('portfolio', () => {
  const packages     = ref([])
  const settings     = ref<any>(null)
  const testimonials = ref([])
  const features     = ref([])
  const loading      = ref(false)

  async function fetchAll() {
    loading.value = true
    try {
      const [pkgs, cfg, tst, feat] = await Promise.allSettled([
        portfolioApi.getPackages(),
        portfolioApi.getSettings(),
        portfolioApi.getTestimonials(),
        portfolioApi.getFeatures(),
      ])
      if (pkgs.status === 'fulfilled')     packages.value     = pkgs.value.data.data ?? pkgs.value.data
      if (cfg.status === 'fulfilled')      settings.value     = cfg.value.data.data  ?? cfg.value.data
      if (tst.status === 'fulfilled')      testimonials.value = tst.value.data.data  ?? tst.value.data
      if (feat.status === 'fulfilled')     features.value     = feat.value.data.data ?? feat.value.data
    } finally { loading.value = false }
  }

  async function fetchPackages() {
    const res = await portfolioApi.getPackages()
    packages.value = res.data.data ?? res.data
  }

  return { packages, settings, testimonials, features, loading, fetchAll, fetchPackages }
})