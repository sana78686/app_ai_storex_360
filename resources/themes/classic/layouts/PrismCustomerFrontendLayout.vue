<template>
  <div class="flex min-h-screen bg-slate-50">
    <!-- Sidebar -->
    <aside class="hidden w-64 flex-shrink-0 border-r border-slate-200 bg-white lg:block">
      <div class="sticky top-0 flex h-screen flex-col">
        <!-- Logo/Header -->
        <div class="border-b border-slate-200 p-6">
          <div class="flex items-center gap-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-brand/10">
              <User class="h-6 w-6 text-brand" />
            </div>
            <div class="flex-1">
              <div class="flex items-center justify-between gap-3">
                <div>
                  <h1 class="font-bold text-slate-900">Mein Konto</h1>
                  <p class="text-xs text-slate-600">{{ authStore.user?.name || authStore.user?.email }}</p>
                </div>
                <router-link
                  to="/"
                  class="inline-flex items-center gap-1 rounded-full border border-slate-200 px-3 py-1 text-xs font-semibold text-slate-700 transition hover:border-brand hover:text-brand"
                  aria-label="Zur Startseite"
                >
                  <ArrowLeft class="h-4 w-4" />
                  Zur Startseite
                </router-link>
              </div>
            </div>
          </div>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 space-y-1 overflow-y-auto p-4">
          <router-link
            v-for="item in navigationItems"
            :key="item.name"
            :to="item.to"
            :class="[
              'flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors',
              isActive(item.to)
                ? 'bg-brand/10 text-brand'
                : 'text-slate-700 hover:bg-slate-100'
            ]"
          >
            <component :is="item.icon" class="h-5 w-5" />
            {{ item.name }}
            <span
              v-if="item.badge"
              class="ml-auto rounded-full bg-brand px-2 py-0.5 text-xs font-semibold text-white"
            >
              {{ item.badge }}
            </span>
          </router-link>
        </nav>

        <!-- Footer -->
        <div class="border-t border-slate-200 p-4 space-y-2">
          <router-link
            to="/"
            class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm text-slate-700 hover:bg-slate-100"
          >
            <ArrowLeft class="h-5 w-5" />
            Zurück zum Shop
          </router-link>
          <button
            @click="handleLogout"
            class="w-full flex items-center gap-3 rounded-lg px-3 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors"
          >
            <LogOut class="h-5 w-5" />
            Abmelden
          </button>
        </div>
      </div>
    </aside>

    <!-- Mobile Header -->
    <div class="lg:hidden fixed top-0 left-0 right-0 z-50 border-b border-slate-200 bg-white">
      <div class="flex items-center justify-between p-4">
        <div class="flex items-center gap-2">
          <User class="h-6 w-6 text-brand" />
          <span class="font-bold text-slate-900">Mein Konto</span>
        </div>
        <button
          @click="showMobileMenu = !showMobileMenu"
          class="rounded-lg p-2 hover:bg-slate-100"
        >
          <Menu v-if="!showMobileMenu" class="h-6 w-6" />
          <X v-else class="h-6 w-6" />
        </button>
      </div>

      <!-- Mobile Menu -->
      <Transition name="slide">
        <div v-if="showMobileMenu" class="border-t border-slate-200 bg-white p-4">
          <nav class="space-y-1 mb-4">
            <router-link
              v-for="item in navigationItems"
              :key="item.name"
              :to="item.to"
              @click="showMobileMenu = false"
              :class="[
                'flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium',
                isActive(item.to)
                  ? 'bg-brand/10 text-brand'
                  : 'text-slate-700 hover:bg-slate-100'
              ]"
            >
              <component :is="item.icon" class="h-5 w-5" />
              {{ item.name }}
            </router-link>
          </nav>
          <div class="border-t border-slate-200 pt-4 space-y-2">
            <router-link
              to="/"
              @click="showMobileMenu = false"
              class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm text-slate-700 hover:bg-slate-100"
            >
              <ArrowLeft class="h-5 w-5" />
              Zurück zum Shop
            </router-link>
            <button
              @click="handleLogout"
              class="w-full flex items-center gap-3 rounded-lg px-3 py-2 text-sm text-red-600 hover:bg-red-50"
            >
              <LogOut class="h-5 w-5" />
              Abmelden
            </button>
          </div>
        </div>
      </Transition>
    </div>

    <!-- Main Content -->
    <main class="flex-1 lg:ml-0" :class="{ 'pt-20': showMobileMenu || true }">
      <div class="mx-auto max-w-7xl px-4 py-6 lg:px-6">
        <router-view />
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '@theme-classic/stores/authStore'
import { useNotificationStore } from '@theme-classic/stores/notificationStore'
import { User, LayoutDashboard, Package, Heart, CreditCard, MapPin, Settings, Bell, ArrowLeft, Menu, X, LogOut } from 'lucide-vue-next'

const route = useRoute()
const router = useRouter()
const authStore = useAuthStore()
const notificationStore = useNotificationStore()
const showMobileMenu = ref(false)

// Logout function
const handleLogout = () => {
  authStore.logout()
  notificationStore.success('Sie wurden erfolgreich abgemeldet', 'Abgemeldet')
  router.push('/')
}

const navigationItems = [
  {
    name: 'Übersicht',
    to: '/konto',
    icon: LayoutDashboard
  },
  {
    name: 'Bestellungen & Retouren',
    to: '/konto/bestellungen',
    icon: Package
  },
  {
    name: 'Wunschliste',
    to: '/konto/wunschliste',
    icon: Heart
  },
  {
    name: 'Zahlungsmethoden',
    to: '/konto/zahlungen',
    icon: CreditCard
  },
  {
    name: 'Adressen',
    to: '/konto/adressen',
    icon: MapPin
  },
  {
    name: 'Benachrichtigungen',
    to: '/konto/benachrichtigungen',
    icon: Bell,
    badge: computed(() => notificationStore.unreadCount > 0 ? notificationStore.unreadCount : null)
  },
  {
    name: 'Einstellungen',
    to: '/konto/einstellungen',
    icon: Settings
  }
]

const isActive = (path) => {
  if (path === '/konto' || path === '/konto/') {
    return route.path === '/konto' || route.path === '/konto/'
  }
  return route.path === path || route.path.startsWith(path + '/')
}
</script>

<style scoped>
.slide-enter-active,
.slide-leave-active {
  transition: all 0.3s ease;
}

.slide-enter-from,
.slide-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}
</style>
