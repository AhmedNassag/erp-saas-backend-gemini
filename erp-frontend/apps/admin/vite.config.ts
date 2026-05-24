import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import { resolve } from 'path'

export default defineConfig({
  plugins: [vue()],
  resolve: {
    alias: {
      '@': resolve(__dirname, 'src'),
      '@nexaerp/ui':   resolve(__dirname, '../../packages/ui/src'),
      '@nexaerp/api':  resolve(__dirname, '../../packages/api/src'),
      '@nexaerp/i18n': resolve(__dirname, '../../packages/i18n/src'),
    },
  },
  server: {
    port: 3001,
    proxy: { '/api': { target: 'http://erp.test:8000', changeOrigin: true } },
  },
})