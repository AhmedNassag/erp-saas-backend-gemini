<template>
  <div class="d-flex flex-column flex-root app-root" id="kt_app_root">
    <div class="app-page flex-column flex-column-fluid" id="kt_app_page">
      <HeaderComponent />
      <div class="app-wrapper flex-column flex-row-fluid" id="kt_app_wrapper">
        <SidebarComponent />
        <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
          <ToolbarComponent />
          <div class="app-content flex-column-fluid" id="kt_app_content">
            <div class="app-container container-fluid">
              <router-view />
            </div>
          </div>
          <FooterComponent />
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import HeaderComponent from './HeaderComponent.vue'
import SidebarComponent from './SidebarComponent.vue'
import ToolbarComponent from './ToolbarComponent.vue'
import FooterComponent from './FooterComponent.vue'
import { useMetronic } from '../composables/useMetronic'

export default {
  name: 'BodyView',
  components: { HeaderComponent, SidebarComponent, ToolbarComponent, FooterComponent },
  mounted() {
    const { initComponents } = useMetronic()
    initComponents()
  }
}
</script>
