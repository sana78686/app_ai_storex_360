<template>
  <div>
    <Breadcrumb :items="breadcrumbItems" />
    <div class="mx-auto max-w-7xl px-3 sm:px-4 py-6 sm:py-8">
      <div class="mb-4 sm:mb-6">
        <h1 class="text-xl sm:text-2xl font-bold text-slate-900">Willkommen zurück, {{ authStore.user?.name || 'Kunde' }}!</h1>
        <p class="mt-2 text-xs sm:text-sm text-slate-600">Übersicht über Ihre Bestellungen und Aktivitäten</p>
      </div>

      <!-- Stats Cards -->
      <div class="grid gap-3 sm:gap-4 grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 mb-6 sm:mb-8">
        <div class="bg-white rounded-lg border border-slate-200 p-4 sm:p-6">
          <div class="flex items-center justify-between">
            <div class="min-w-0 flex-1">
              <p class="text-xs sm:text-sm text-slate-600 mb-1 truncate">Offene Bestellungen</p>
              <p class="text-xl sm:text-2xl font-bold text-slate-900">{{ orders.length }}</p>
            </div>
            <div class="h-10 w-10 sm:h-12 sm:w-12 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0 ml-2">
              <Package class="h-5 w-5 sm:h-6 sm:w-6 text-blue-600" />
            </div>
          </div>
        </div>

        <div class="bg-white rounded-lg border border-slate-200 p-4 sm:p-6">
          <div class="flex items-center justify-between">
            <div class="min-w-0 flex-1">
              <p class="text-xs sm:text-sm text-slate-600 mb-1 truncate">Wunschliste</p>
              <p class="text-xl sm:text-2xl font-bold text-slate-900">{{ wishlistStore.totalItems }}</p>
            </div>
            <div class="h-10 w-10 sm:h-12 sm:w-12 rounded-full bg-pink-100 flex items-center justify-center flex-shrink-0 ml-2">
              <Heart class="h-5 w-5 sm:h-6 sm:w-6 text-pink-600" />
            </div>
          </div>
        </div>

        <div class="bg-white rounded-lg border border-slate-200 p-4 sm:p-6">
          <div class="flex items-center justify-between">
            <div class="min-w-0 flex-1">
              <p class="text-xs sm:text-sm text-slate-600 mb-1 truncate">Warenkorb</p>
              <p class="text-xl sm:text-2xl font-bold text-slate-900">{{ cartStore.totalItems }}</p>
            </div>
            <div class="h-10 w-10 sm:h-12 sm:w-12 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0 ml-2">
              <ShoppingCart class="h-5 w-5 sm:h-6 sm:w-6 text-green-600" />
            </div>
          </div>
        </div>

        <div class="bg-white rounded-lg border border-slate-200 p-4 sm:p-6">
          <div class="flex items-center justify-between">
            <div class="min-w-0 flex-1">
              <p class="text-xs sm:text-sm text-slate-600 mb-1 truncate">Benachrichtigungen</p>
              <p class="text-xl sm:text-2xl font-bold text-slate-900">{{ notificationStore.unreadCount }}</p>
            </div>
            <div class="h-10 w-10 sm:h-12 sm:w-12 rounded-full bg-yellow-100 flex items-center justify-center flex-shrink-0 ml-2">
              <Bell class="h-5 w-5 sm:h-6 sm:w-6 text-yellow-600" />
            </div>
          </div>
        </div>
      </div>

      <!-- Recent Orders -->
      <div class="bg-white rounded-lg border border-slate-200 p-4 sm:p-6 mb-4 sm:mb-6">
        <div class="flex items-center justify-between mb-3 sm:mb-4">
          <h2 class="text-base sm:text-lg font-semibold text-slate-900">Letzte Bestellungen</h2>
          <router-link to="/konto/bestellungen" class="text-xs sm:text-sm text-brand hover:underline touch-manipulation">
            Alle anzeigen
          </router-link>
        </div>
        <div v-if="orders.length === 0" class="text-center py-8 text-slate-500">
          <Package class="h-12 w-12 mx-auto mb-2 text-slate-300" />
          <p class="text-sm">Noch keine Bestellungen</p>
        </div>
        <div v-else class="space-y-3 sm:space-y-4">
          <div
            v-for="order in orders.slice(0, 3)"
            :key="order.id"
            class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 p-3 sm:p-4 border border-slate-200 rounded-lg active:bg-slate-50 touch-manipulation"
          >
            <div>
              <p class="font-medium text-slate-900">Bestellung #{{ order.id }}</p>
              <p class="text-sm text-slate-600">{{ order.date }}</p>
            </div>
            <div class="text-right">
              <p class="font-semibold text-slate-900">{{ formatPrice(order.total) }}</p>
              <span :class="getStatusClass(order.status)">{{ order.status }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="grid gap-3 sm:gap-4 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3">
        <router-link
          to="/konto/bestellungen"
          class="bg-white rounded-lg border border-slate-200 p-4 sm:p-6 active:border-brand/60 active:shadow-md transition-all touch-manipulation"
        >
          <Package class="h-7 w-7 sm:h-8 sm:w-8 text-brand mb-2 sm:mb-3" />
          <h3 class="font-semibold text-sm sm:text-base text-slate-900 mb-1">Bestellungen verwalten</h3>
          <p class="text-xs sm:text-sm text-slate-600">Bestellungen ansehen und verfolgen</p>
        </router-link>

        <router-link
          to="/konto/adressen"
          class="bg-white rounded-lg border border-slate-200 p-6 hover:border-brand/60 hover:shadow-md transition-all"
        >
          <MapPin class="h-8 w-8 text-brand mb-3" />
          <h3 class="font-semibold text-slate-900 mb-1">Adressen verwalten</h3>
          <p class="text-sm text-slate-600">Liefer- und Rechnungsadressen</p>
        </router-link>

        <router-link
          to="/konto/zahlungen"
          class="bg-white rounded-lg border border-slate-200 p-6 hover:border-brand/60 hover:shadow-md transition-all"
        >
          <CreditCard class="h-8 w-8 text-brand mb-3" />
          <h3 class="font-semibold text-slate-900 mb-1">Zahlungsmethoden</h3>
          <p class="text-sm text-slate-600">Kreditkarten und Zahlungsmethoden</p>
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useAuthStore } from '@theme-prism/stores/authStore'
import { useCartStore } from '@theme-prism/stores/cartStore'
import { useWishlistStore } from '@theme-prism/stores/wishlistStore'
import { useNotificationStore } from '@theme-prism/stores/notificationStore'
import { Package, Heart, ShoppingCart, Bell, MapPin, CreditCard } from 'lucide-vue-next'
import Breadcrumb from '@theme-prism/component/Breadcrumb.vue'

const authStore = useAuthStore()
const cartStore = useCartStore()
const wishlistStore = useWishlistStore()
const notificationStore = useNotificationStore()

const breadcrumbItems = computed(() => [
  { label: 'Startseite', to: '/' },
  { label: 'Mein Konto', to: null }
])

// Mock orders data - in production, fetch from API
const orders = computed(() => [
  {
    id: 'ORD-001',
    date: '15.01.2024',
    total: 89.99,
    status: 'Versandt'
  },
  {
    id: 'ORD-002',
    date: '10.01.2024',
    total: 149.50,
    status: 'Geliefert'
  }
])

const formatPrice = (price) => {
  return new Intl.NumberFormat('de-DE', {
    style: 'currency',
    currency: 'EUR'
  }).format(price)
}

const getStatusClass = (status) => {
  const classes = {
    'Versandt': 'text-xs px-2 py-1 rounded-full bg-blue-100 text-blue-700',
    'Geliefert': 'text-xs px-2 py-1 rounded-full bg-green-100 text-green-700',
    'Bearbeitung': 'text-xs px-2 py-1 rounded-full bg-yellow-100 text-yellow-700'
  }
  return classes[status] || 'text-xs px-2 py-1 rounded-full bg-slate-100 text-slate-700'
}
</script>
