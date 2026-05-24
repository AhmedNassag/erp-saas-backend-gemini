<script setup lang="ts">
import { RouterView } from "vue-router"
import { useI18n } from "vue-i18n"
import { onMounted } from "vue"
const { locale } = useI18n()
onMounted(() => { document.documentElement.dir = locale.value === "ar" ? "rtl" : "ltr" })
</script>
<template><RouterView /></template>