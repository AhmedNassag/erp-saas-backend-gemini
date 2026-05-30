<template>
  <div id="kt_app_footer" class="app-footer">
    <div class="app-container container-fluid d-flex flex-stack py-4">
      <div class="text-muted fs-7 fw-semibold">
        &copy; {{ year }} ERP System. All rights reserved.
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'FooterComponent',
  data() {
    return { year: new Date().getFullYear() }
  }
}
</script>
