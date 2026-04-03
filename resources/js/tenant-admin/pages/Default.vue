<template>
  <TenantDashboardLayout>
    <router-view v-slot="{ Component }">
      <transition name="gull-page" mode="out-in">
        <component :is="Component" />
      </transition>
    </router-view>
  </TenantDashboardLayout>
</template>

<script setup>
import TenantDashboardLayout from '@/tenant-admin/layouts/DashboardLayout.vue'
</script>

<style scoped>
.gull-page-enter-active,
.gull-page-leave-active {
  transition:
    opacity 0.2s ease,
    transform 0.2s ease;
}

.gull-page-enter-from,
.gull-page-leave-to {
  opacity: 0;
  transform: translateY(10px);
}
</style>
