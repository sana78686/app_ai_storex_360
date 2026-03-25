<template>
  <div class="flex flex-1 flex-col">
    <!-- Thin account bar: greeting + shop back + logout -->
    <div class="border-b border-slate-200 bg-slate-50">
      <div class="mx-auto max-w-7xl px-3 sm:px-4 py-2 sm:py-3">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
          <p class="text-sm text-slate-700">
            Hallo, <span class="font-semibold text-slate-900">{{ authStore.user?.name || authStore.user?.email || 'Kunde' }}</span>
          </p>
          <div class="flex items-center gap-3 text-sm">
            <router-link
              to="/"
              class="text-slate-600 hover:text-brand transition-colors"
            >
              Zurück zum Shop
            </router-link>
            <span class="text-slate-300 hidden sm:inline">|</span>
            <button
              type="button"
              @click="handleLogout"
              class="text-red-600 hover:text-red-700 font-medium transition-colors"
            >
              Abmelden
            </button>
          </div>
        </div>
      </div>
    </div>

    <CustomerAccountNav />

    <div class="flex-1 bg-white">
      <router-view />
    </div>
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router'
import { useAuthStore } from '@theme-prism/stores/authStore'
import { useNotificationStore } from '@theme-prism/stores/notificationStore'
import CustomerAccountNav from '@theme-prism/pages/customer/CustomerAccountNav.vue'


const router = useRouter()
const authStore = useAuthStore()
const notificationStore = useNotificationStore()

function handleLogout() {
  authStore.logout()
  notificationStore.success('Sie wurden erfolgreich abgemeldet', 'Abgemeldet')
  router.push('/')
}
</script>
