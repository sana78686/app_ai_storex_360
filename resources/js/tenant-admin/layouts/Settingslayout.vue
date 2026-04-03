<template>
  <div class="settings-shell mx-auto max-w-3xl">
    <div
      class="settings-shell-card rounded-xl border border-gray-200/90 bg-white p-4 shadow-sm sm:p-5"
    >
      <header class="mb-3 border-b border-gray-100 pb-3">
        <p class="text-[11px] font-bold uppercase tracking-wide text-[#275a19]">
          {{ t(pageMeta.kickerKey) }}
        </p>
        <h1 class="mt-0.5 text-lg font-bold text-gray-900 sm:text-xl">
          {{ t(pageMeta.titleKey) }}
        </h1>
      </header>
      <div class="tenant-settings-page-body settings-body">
        <router-view v-slot="{ Component }">
          <transition name="fade-slide" mode="out-in">
            <component :is="Component" />
          </transition>
        </router-view>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useRoute } from 'vue-router'
import { useI18n } from 'vue-i18n'

const route = useRoute()
const { t } = useI18n()

const PAGE_META = {
  'settings-general': {
    kickerKey: 'adminLayout.settingsNav.general',
    titleKey: 'adminLayout.settingsNav.generalTitle',
  },
  'settings-localization': {
    kickerKey: 'adminLayout.settingsNav.localization',
    titleKey: 'adminLayout.settingsNav.localizationTitle',
  },
  'settings-legal': {
    kickerKey: 'adminLayout.settingsNav.legalInfo',
    titleKey: 'adminLayout.settingsNav.legalTitle',
  },
  'settings-email': {
    kickerKey: 'adminLayout.settingsNav.emailNotifications',
    titleKey: 'adminLayout.settingsNav.emailTitle',
  },
  'settings-coupons': {
    kickerKey: 'adminLayout.settingsNav.coupons',
    titleKey: 'adminLayout.settingsNav.couponsTitle',
  },
  'settings-invoices': {
    kickerKey: 'adminLayout.settingsNav.invoices',
    titleKey: 'adminLayout.settingsNav.invoicesTitle',
  },
  'settings-domains': {
    kickerKey: 'adminLayout.settingsNav.domains',
    titleKey: 'adminLayout.settingsNav.domainsTitle',
  },
}

const pageMeta = computed(() => {
  const name = route.name
  if (name && PAGE_META[name]) {
    return PAGE_META[name]
  }
  return {
    kickerKey: 'adminLayout.settings',
    titleKey: 'adminLayout.settings',
  }
})
</script>

<style scoped>
.fade-slide-enter-active,
.fade-slide-leave-active {
  transition: all 0.2s ease;
}

.fade-slide-enter-from {
  opacity: 0;
  transform: translateY(8px);
}

.fade-slide-leave-to {
  opacity: 0;
  transform: translateY(-8px);
}
</style>
