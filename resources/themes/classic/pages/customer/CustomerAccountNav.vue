<template>
  <div class="border-b border-slate-200 bg-white">
    <div class="mx-auto max-w-7xl px-3 sm:px-4">
      <!-- Amazon-style horizontal tabs: scroll on mobile -->
      <nav
        class="flex gap-0 overflow-x-auto scrollbar-hide -mb-px"
        aria-label="Konto-Navigation"
      >
        <router-link
          v-for="item in navItems"
          :key="item.name"
          :to="item.to"
          :class="[
            'flex items-center gap-2 whitespace-nowrap border-b-2 px-4 py-3 text-sm font-medium transition-colors touch-manipulation',
            isActive(item.to)
              ? 'border-brand text-brand'
              : 'border-transparent text-slate-600 hover:border-slate-300 hover:text-slate-900'
          ]"
        >
          <component :is="item.icon" class="h-4 w-4 flex-shrink-0" />
          {{ item.name }}
          <span
            v-if="item.badge != null && item.badge > 0"
            class="ml-1 rounded-full bg-brand px-2 py-0.5 text-xs font-semibold text-white"
          >
            {{ item.badge > 9 ? '9+' : item.badge }}
          </span>
        </router-link>
        <span class="ml-auto flex-shrink-0 w-px" aria-hidden="true" />
      </nav>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useRoute } from 'vue-router'
import { useNotificationStore } from '@theme-classic/stores/notificationStore'
import {
  LayoutDashboard,
  Package,
  Heart,
  CreditCard,
  MapPin,
  Bell,
  Settings
} from 'lucide-vue-next'

const route = useRoute()
const notificationStore = useNotificationStore()

const navItems = computed(() => [
  { name: 'Übersicht', to: '/konto', icon: LayoutDashboard },
  { name: 'Bestellungen & Retouren', to: '/konto/bestellungen', icon: Package },
  { name: 'Wunschliste', to: '/konto/wunschliste', icon: Heart },
  { name: 'Zahlungsmethoden', to: '/konto/zahlungen', icon: CreditCard },
  { name: 'Adressen', to: '/konto/adressen', icon: MapPin },
  {
    name: 'Benachrichtigungen',
    to: '/konto/benachrichtigungen',
    icon: Bell,
    badge: notificationStore.unreadCount
  },
  { name: 'Einstellungen', to: '/konto/einstellungen', icon: Settings }
])

function isActive(path) {
  if (path === '/konto' || path === '/konto/') {
    return route.path === '/konto' || route.path === '/konto/'
  }
  return route.path === path || route.path.startsWith(path + '/')
}
</script>
