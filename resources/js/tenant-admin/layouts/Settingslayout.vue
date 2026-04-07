<template>
  <div class="settings-shell mx-auto w-full max-w-7xl px-1 sm:px-0">
    <!-- Sticky under dashboard header (h-16); matches product editor pattern -->
    <header
      class="settings-shell__sticky sticky top-16 z-[45] -mx-1 mb-3 flex flex-wrap items-center justify-between gap-2 rounded-xl border border-gray-200/90 bg-white/95 px-3 py-2.5 shadow-sm backdrop-blur-sm sm:-mx-0 sm:px-4"
      role="banner"
    >
      <div class="flex min-w-0 flex-1 items-center gap-2 sm:gap-3">
        <router-link
          to="/dashboard"
          class="inline-flex shrink-0 items-center gap-1.5 rounded-lg px-2 py-2 text-sm font-semibold text-gray-600 no-underline transition hover:bg-gray-100 hover:text-gray-900"
        >
          <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
          </svg>
          <span class="hidden sm:inline">{{ t('adminLayout.back', 'Back') }}</span>
        </router-link>
        <div class="min-w-0 border-l border-gray-200 pl-2 sm:pl-3">
          <p class="text-[10px] font-bold uppercase tracking-wide text-[#275a19]">
            {{ t(pageMeta.kickerKey) }}
          </p>
          <h1 class="truncate text-base font-bold text-gray-900 sm:text-lg">
            {{ t(pageMeta.titleKey) }}
          </h1>
        </div>
      </div>
      <button
        type="button"
        class="tenant-btn-submit tenant-btn-sm min-h-[40px] shrink-0 px-4 sm:min-h-0"
        :disabled="settingsSaving || !settingsHasSave"
        :title="!settingsHasSave ? t('adminLayout.saveUnavailableHint', 'Save is available on editable settings screens.') : undefined"
        @click="runSettingsSave"
      >
        <span v-if="settingsSaving">{{ t('adminLayout.saving', 'Saving…') }}</span>
        <span v-else>{{ t('adminLayout.save', 'Save') }}</span>
      </button>
    </header>

    <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:gap-8">
      <!-- Secondary SETTINGS nav (matches store settings sidebar pattern) -->
      <aside
        class="settings-shell__aside shrink-0 rounded-xl border border-gray-200/90 bg-gray-50/80 p-3 shadow-sm lg:w-56 lg:border-gray-200/90 lg:bg-white lg:p-4"
        aria-label="Settings sections"
      >
        <p class="mb-3 text-[10px] font-bold uppercase tracking-[0.12em] text-gray-400">
          {{ t('adminLayout.settings', 'Settings') }}
        </p>
        <nav class="flex flex-col gap-0.5">
          <router-link
            v-for="item in navItems"
            :key="item.name"
            :to="item.to"
            class="settings-nav-link group flex items-center gap-2.5 rounded-lg px-2.5 py-2.5 text-sm font-medium text-gray-600 no-underline transition hover:bg-gray-100 hover:text-gray-900"
            :class="{ 'settings-nav-link--active bg-gray-100 text-gray-900': route.name === item.name }"
          >
            <span
              class="flex h-8 w-8 shrink-0 items-center justify-center rounded-md text-gray-500 transition group-hover:text-gray-700"
              :class="{ 'bg-white text-[#275a19] shadow-sm': route.name === item.name }"
              aria-hidden="true"
              v-html="item.icon"
            />
            <span>{{ t(item.labelKey) }}</span>
          </router-link>
        </nav>
      </aside>

      <div class="min-w-0 flex-1">
        <div
          class="settings-shell-card rounded-xl border border-gray-200/90 bg-white p-3 shadow-sm sm:p-4 lg:p-5"
        >
          <div class="tenant-settings-page-body settings-body min-h-0">
            <router-view v-slot="{ Component }">
              <transition name="fade-slide" mode="out-in">
                <component :is="Component" />
              </transition>
            </router-view>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useRoute } from 'vue-router'
import { useI18n } from 'vue-i18n'
import { provideSettingsStickySave } from '@tenant/settings/settingsStickyContext'

const route = useRoute()
const { t } = useI18n()

const settingsSticky = provideSettingsStickySave()
const settingsSaving = settingsSticky.saving
const settingsHasSave = settingsSticky.hasSave
const runSettingsSave = () => settingsSticky.runSave()

const ICONS = {
  gear: '<svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>',
  globe: '<svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
  doc: '<svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>',
  mail: '<svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>',
  ticket: '<svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>',
  invoice: '<svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2zM10 8.5a.5.5 0 11-1 0 .5.5 0 011 0zm5 5a.5.5 0 11-1 0 .5.5 0 011 0z"/></svg>',
}

const navItems = [
  { name: 'settings-general', to: '/dashboard/settings/general', labelKey: 'adminLayout.settingsNav.general', icon: ICONS.gear },
  { name: 'settings-localization', to: '/dashboard/settings/localization', labelKey: 'adminLayout.settingsNav.localization', icon: ICONS.globe },
  { name: 'settings-legal', to: '/dashboard/settings/legal', labelKey: 'adminLayout.settingsNav.legalInfo', icon: ICONS.doc },
  { name: 'settings-email', to: '/dashboard/settings/email', labelKey: 'adminLayout.settingsNav.emailNotifications', icon: ICONS.mail },
  { name: 'settings-coupons', to: '/dashboard/settings/coupons', labelKey: 'adminLayout.settingsNav.coupons', icon: ICONS.ticket },
  { name: 'settings-invoices', to: '/dashboard/settings/invoices', labelKey: 'adminLayout.settingsNav.invoices', icon: ICONS.invoice },
  { name: 'settings-domains', to: '/dashboard/settings/domains', labelKey: 'adminLayout.settingsNav.domains', icon: ICONS.globe },
]

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

.settings-nav-link--active {
  box-shadow: inset 0 0 0 1px rgb(229 231 235 / 0.9);
}
</style>
