<template>
  <div id="kt_app_sidebar" class="app-sidebar flex-column" data-kt-drawer="true" data-kt-drawer-name="app-sidebar" data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true" data-kt-drawer-width="275px" data-kt-drawer-direction="start" data-kt-drawer-toggle="#kt_app_sidebar_mobile_toggle" data-kt-app-sidebar-collapse="true">
    <div class="app-sidebar-logo flex-shrink-0 d-none d-md-flex align-items-center px-8" id="kt_app_sidebar_logo">
      <a href="/dashboard">
        <img alt="Logo" src="/assets/media/logos/demo42.svg" class="h-25px app-sidebar-logo-default theme-light-show" />
        <img alt="Logo" src="/assets/media/logos/demo42-dark.svg" class="h-25px app-sidebar-logo-default theme-dark-show" />
      </a>
    </div>
    <div class="app-sidebar-menu overflow-hidden flex-column-fluid">
      <div class="app-sidebar-wrapper hover-scroll-overlay-y my-5 mx-3" id="kt_app_sidebar_menu_wrapper" data-kt-scroll="true" data-kt-scroll-height="auto" data-kt-scroll-dependencies="#kt_app_sidebar_logo" data-kt-scroll-wrappers="#kt_app_sidebar_menu" data-kt-scroll-offset="5px">
        <div class="menu menu-column menu-rounded menu-sub-indention fw-semibold px-1" id="#kt_app_sidebar_menu" data-kt-menu="true">
          <template v-for="(section, sIndex) in menuStore.data" :key="sIndex">
            <div class="menu-item pt-5">
              <div class="menu-content">
                <span class="menu-heading fw-bold text-uppercase fs-7">{{ section.section }}</span>
              </div>
            </div>
            <div v-for="(item, iIndex) in section.items" :key="iIndex" class="menu-item">
              <router-link :to="item.route" class="menu-link" :class="{ active: isActive(item.route) }">
                <span class="menu-icon"><i :class="item.icon"></i></span>
                <span class="menu-title">{{ item.title }}</span>
              </router-link>
            </div>
          </template>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { useMenuStore } from '../store/menu'
import { useRoute } from 'vue-router'
import { useMetronic } from '../composables/useMetronic'

export default {
  name: 'SidebarComponent',
  setup() {
    const menuStore = useMenuStore()
    const route = useRoute()
    return { menuStore, route }
  },
  methods: {
    isActive(path) {
      return this.route.path === path || this.route.path.startsWith(path + '/')
    }
  },
  mounted() {
    const { initComponents } = useMetronic()
    initComponents()
  }
}
</script>
