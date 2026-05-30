<template>
  <div class="d-flex flex-column flex-root" id="kt_app_root">
    <div class="d-flex flex-column flex-lg-row flex-column-fluid">
      <div class="d-flex flex-column flex-lg-row-auto w-100 w-lg-400px w-xl-500px">
        <div class="d-flex flex-column position-relative h-lg-100 d-flex flex-column align-items-center justify-content-center p-10">
          <div class="w-100 mb-10">
            <img alt="Logo" src="/assets/media/logos/demo42.svg" class="h-60px" />
          </div>
          <div class="w-100 mb-10">
            <h2 class="fw-bold text-dark mb-3">Sign In</h2>
            <div class="text-gray-400 fs-6">Enter your credentials to access the system</div>
          </div>
          <form class="w-100" @submit.prevent="handleLogin">
            <div v-if="error" class="alert alert-danger d-flex align-items-center p-5 mb-8">
              <i class="ki-outline ki-information-5 fs-2 me-4 text-danger"></i>
              <span>{{ error }}</span>
            </div>
            <div class="fv-row mb-8">
              <input type="email" v-model="email" class="form-control form-control-lg form-control-solid" placeholder="Email" required />
            </div>
            <div class="fv-row mb-8">
              <input type="password" v-model="password" class="form-control form-control-lg form-control-solid" placeholder="Password" required />
            </div>
            <div class="d-grid mb-10">
              <button type="submit" class="btn btn-primary" :disabled="loading">
                <span v-if="loading" class="spinner-border spinner-border-sm me-2"></span>
                {{ loading ? 'Signing In...' : 'Sign In' }}
              </button>
            </div>
          </form>
        </div>
      </div>
      <div class="d-flex flex-column flex-lg-row-fluid py-10 bg-body-secondary bg-gradient">
        <div class="d-flex flex-center flex-column flex-column-fluid p-10">
          <img src="/assets/media/svg/illustrations/login-visual.svg" class="mw-100 mh-350px" alt="" />
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { Auth } from '../../../API/Auth'

export default {
  name: 'LoginView',
  data() {
    return {
      email: '',
      password: '',
      error: null,
      loading: false,
    }
  },
  methods: {
    async handleLogin() {
      this.error = null
      this.loading = true
      try {
        await Auth.logIn(this.email, this.password)
        this.$router.push('/dashboard')
      } catch (e) {
        this.error = e.response?.data?.message || 'Invalid credentials'
      } finally {
        this.loading = false
      }
    }
  }
}
</script>

<style scoped>
body {
  background: transparent !important;
}
</style>
