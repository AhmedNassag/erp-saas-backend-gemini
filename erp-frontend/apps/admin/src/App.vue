<script setup lang="ts">
import { RouterView } from "vue-router"
import ToastContainer from "./components/layout/ToastContainer.vue"
</script>

<template>
  <RouterView />
  <ToastContainer />
</template>