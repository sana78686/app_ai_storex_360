<template>
  <TenantDashboardLayout v-if="!isFullscreenPos">
    <router-view v-slot="{ Component }">
      <transition name="gull-page" mode="out-in">
        <component :is="Component" />
      </transition>
    </router-view>
  </TenantDashboardLayout>
  <router-view v-else v-slot="{ Component }">
    <transition name="gull-page" mode="out-in">
      <component :is="Component" />
    </transition>
  </router-view>
</template>

<script setup>
import { computed } from 'vue'
import { useRoute } from 'vue-router'
import TenantDashboardLayout from '@/tenant-admin/layouts/DashboardLayout.vue'

const route = useRoute()
const isFullscreenPos = computed(() => route.meta.fullscreenPos === true)
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
