<template>

     <header class="fixed top-0 left-0 right-0 z-50 shadow-sm">
      <div class="flex items-center justify-between px-4 bg-[#131921] h-14">
        <div class="flex items-center space-x-4">
          <button
  @click="toggleSidebar"
  class="md:hidden p-2 text-white rounded-sm transition-all hover:outline hover:outline-1 hover:outline-white"
>
  <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
      d="M4 6h16M4 12h16M4 18h16" />
  </svg>
</button>


          <div class="flex items-center space-x-3">
  <router-link to="/dashboard" class="flex items-center focus:outline-none focus:ring-0">
    <img
      :src="logoUrl"
      alt="Logo"
      class="h-8 brightness-0 invert object-contain cursor-pointer"
    />
  </router-link>
</div>

        </div>

        <div class="hidden lg:flex items-center flex-1 max-w-xl mx-8">
          <div class="flex w-full group">
            <input type="text" placeholder="Search for features, products or help"
              class="flex-1 bg-white px-4 py-2 text-sm focus:outline-none rounded-l-sm border-2 border-transparent focus:border-orange-500" />
            <button class="bg-[#febd69] hover:bg-[#f3a847] px-1 rounded-r-sm transition-colors">
              <svg class="w-5 h-5 text-[#131921]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
              </svg>
            </button>
          </div>
        </div>

        <div class="flex items-center space-x-2 md:space-x-5">
          <div class="relative header-language-dropdown group">
            <button @click="showHeaderLanguageDropdown = !showHeaderLanguageDropdown"
              class="flex items-center space-x-1 px-2 py-1.5 hover:outline hover:outline-1 hover:outline-white text-white text-xs font-bold transition-all">
              <span class="uppercase">{{ locale }}</span>
              <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7"></path></svg>
            </button>
            <div v-if="showHeaderLanguageDropdown" class="absolute top-full right-0 mt-1 bg-white border border-gray-200 shadow-xl z-50 min-w-[140px] rounded-sm py-2">
              <button v-for="lang in availableLanguages" :key="lang.code" @click="switchLanguage(lang.code)"
                class="w-full text-left px-4 py-2 text-xs text-gray-700 hover:bg-gray-100 flex items-center justify-between">
                <span class="font-medium">{{ lang.name }}</span>
                <span v-if="locale === lang.code" class="text-orange-500 font-bold">✓</span>
              </button>
            </div>
          </div>

          <button class="hidden sm:block text-white hover:text-orange-400"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg></button>
          <div class="h-8 w-px bg-gray-700 hidden md:block"></div>
          <!-- <button class="text-white text-xs font-bold hover:text-orange-400">Settings</button> -->
        </div>
      </div>


    </header>
  <div class="min-h-screen bg-[#F9FAFB] p-4 md:p-8 mt-5">
    <!-- <div class="max-w-6xl mx-auto mb-8">
      <h1 class="text-2xl font-bold text-gray-900">Settings</h1>
      <p class="text-sm text-gray-500">Manage your store preferences, localization, and legal information.</p>
    </div> -->

    <div class="max-w-6xl mx-auto flex flex-col lg:flex-row gap-8">

      <aside class=" w-full lg:w-64 flex-shrink-0">
        <nav class="fixed space-y-1 bg-white p-2 rounded-xl border border-gray-200 shadow-sm">
          <button
            v-for="tab in tabs"
            :key="tab.name"
            @click="activeTab = tab.name"
            class="w-full flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-lg transition-all duration-200 group"
            :class="[
              activeTab === tab.name
                ? 'bg-black text-white shadow-md'
                : 'text-gray-600 hover:bg-gray-50 hover:text-black'
            ]"
          >
            <i :class="[tab.icon, activeTab === tab.name ? 'text-white' : 'text-gray-400 group-hover:text-black']" class="w-5 text-center"></i>
            {{ tab.label }}
          </button>
        </nav>
      </aside>

      <main class="flex-1">
        <transition name="fade-slide" mode="out-in">
          <div :key="activeTab" class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 md:p-8 min-h-[500px]">
            <div class="mb-6 pb-4 border-b border-gray-100">
              <h2 class="text-lg font-bold text-gray-900">{{ currentLabel }}</h2>
            </div>

            <component :is="currentComponent" />
          </div>
        </transition>
      </main>

    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import GeneralSettings from '@/tenant-admin/pages/settings/GeneralSettings.vue';
import LocalizationSettings from '@/tenant-admin/pages/settings/LocalizationSettings.vue';
import LegalInfo from '@/tenant-admin/pages/settings/LegalInfo.vue';
import MailSettings from '@/tenant-admin/pages/settings/MailSettings.vue';
import CouponParent from '@/tenant-admin/pages/settings/CouponParent.vue';
import InvoiceSettings from '@/tenant-admin/pages/settings/InvoiceSettings.vue';
import Domains from '@/tenant-admin/pages/settings/Domains.vue';

import { useI18n } from 'vue-i18n'

const { locale } = useI18n()

const showLanguageDropdown = ref(false)
const showHeaderLanguageDropdown = ref(false)

const availableLanguages = [
  { code: 'en', name: 'English' },
  { code: 'de', name: 'Deutsch' }
]

const currentLanguage = computed(() => {
  const currentLang = availableLanguages.find(lang => lang.code === locale.value)
  return currentLang ? currentLang.name : 'English'
})

const switchLanguage = (langCode) => {
  locale.value = langCode
  showLanguageDropdown.value = false
  showHeaderLanguageDropdown.value = false
}
// Using Composition API (modern Vue standard)
const activeTab = ref('general');
const logoUrl = '/assets/logo/Einfachkaufen24.png'
const tabs = [
  { name: 'general', label: 'General', component: GeneralSettings, icon: 'fas fa-cog' },
  { name: 'localization', label: 'Localization', component: LocalizationSettings, icon: 'fas fa-globe' },
  { name: 'legalInfo', label: 'Legal Info', component: LegalInfo, icon: 'fas fa-file-shield' },
  { name: 'mailSettings', label: 'Email Notifications', component: MailSettings, icon: 'fas fa-envelope' },
  { name: 'CouponParent', label: 'Coupons & Discounts', component: CouponParent, icon: 'fas fa-ticket-alt' },
  { name: 'InvoiceSettings', label: 'Invoices', component: InvoiceSettings, icon: 'fas fa-file-invoice-dollar' },
  { name: 'Domains', label: 'Domains', component: Domains, icon: 'fas fa-domain' },
];

const currentComponent = computed(() => {
  return tabs.find(t => t.name === activeTab.value)?.component;
});

const currentLabel = computed(() => {
  return tabs.find(t => t.name === activeTab.value)?.label;
});
</script>

<style scoped>
/* Smooth transition between tabs */
.fade-slide-enter-active,
.fade-slide-leave-active {
  transition: all 0.2s ease;
}

.fade-slide-enter-from {
  opacity: 0;
  transform: translateY(10px);
}

.fade-slide-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}

/* Ensure FontAwesome is available for the icons */
@import url('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css');
</style>
