<template>
  <TenantDashboardLayout>
    <!-- Router view for child routes -->
    <router-view />
  </TenantDashboardLayout>
</template>

<script setup>
import TenantDashboardLayout from '@/tenant-admin/layouts/DashboardLayout.vue'
</script>

<style scoped>
/* Using Tailwind CSS for responsive design */
</style>

