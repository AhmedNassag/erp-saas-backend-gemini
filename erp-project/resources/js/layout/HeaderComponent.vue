<template>
  <div id="kt_app_header" class="app-header">
    <div class="app-container container-fluid d-flex align-items-stretch flex-stack" id="kt_app_header_container">
      <div class="d-flex align-items-center d-block d-lg-none ms-n3" title="Show sidebar menu">
        <div class="btn btn-icon btn-active-color-primary w-35px h-35px me-2" id="kt_app_sidebar_mobile_toggle">
          <i class="ki-outline ki-abstract-14 fs-2"></i>
        </div>
        <a href="/dashboard">
          <img alt="Logo" src="/assets/media/logos/demo42-small.svg" class="h-30px" />
        </a>
      </div>
      <div class="app-navbar flex-lg-grow-1" id="kt_app_header_navbar">
        <div class="app-navbar-item d-flex align-items-stretch flex-lg-grow-1">
          <div class="header-search d-flex align-items-center w-lg-200px">
            <form class="d-none d-lg-block w-100 position-relative mb-5 mb-lg-0">
              <i class="ki-outline ki-magnifier search-icon fs-2 text-gray-500 position-absolute top-50 translate-middle-y ms-5"></i>
              <input type="text" class="search-input form-control form-control rounded-1 ps-13" placeholder="Search..." v-model="searchTerm" @input="onSearch" />
            </form>
          </div>
        </div>
        <div class="app-navbar-item ms-1 ms-md-3">
          <div class="cursor-pointer symbol symbol-35px" data-kt-menu-trigger="{default:'click', lg:'hover'}" data-kt-menu-attach="parent" data-kt-menu-placement="bottom-end">
            <img :src="userAvatar" alt="user" class="h-30px w-30px rounded-circle" />
          </div>
          <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg-light-primary fw-semibold w-275px" data-kt-menu="true">
            <div class="menu-item px-3">
              <div class="menu-content d-flex align-items-center px-3 py-3">
                <div class="symbol symbol-45px me-5">
                  <img :src="userAvatar" alt="" />
                </div>
                <div class="d-flex flex-column">
                  <span class="fw-bold fs-6">{{ userName }}</span>
                  <span class="fw-semibold text-muted fs-7">{{ userEmail }}</span>
                </div>
              </div>
            </div>
            <div class="separator my-2"></div>
            <div class="menu-item px-3">
              <a href="#" class="menu-link px-3" @click.prevent="logout">
                <span class="menu-icon"><i class="ki-outline ki-exit-down fs-2"></i></span>
                <span class="menu-title">Sign Out</span>
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { Auth } from '../API/Auth'
import { useMetronic } from '../composables/useMetronic'

export default {
  name: 'HeaderComponent',
  data() {
    return {
      searchTerm: '',
    }
  },
  computed: {
    userName() { return Auth.USER?.full_name || Auth.USER?.name || 'User' },
    userEmail() { return Auth.USER?.email || '' },
    userAvatar() { return Auth.USER?.avatar || '/assets/media/avatars/blank.png' },
  },
  mounted() {
    const { initComponents } = useMetronic()
    initComponents()
  },
  methods: {
    onSearch() {},
    logout() {
      Auth.logOut()
    }
  }
}
</script>
