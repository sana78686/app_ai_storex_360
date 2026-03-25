<template>
  <div>
    <Breadcrumb :items="breadcrumbItems" />
    <div class="mx-auto max-w-7xl px-3 sm:px-4 py-6 sm:py-8">
      <div class="mb-4 sm:mb-6">
        <h1 class="text-xl sm:text-2xl font-bold text-slate-900">{{ $t('customerDashboard.welcomeBack', { name: authStore.user?.name || $t('customerDashboard.customer') }) }}</h1>
        <p class="mt-2 text-xs sm:text-sm text-slate-600">{{ $t('customerDashboard.subtitle') }}</p>
      </div>

      <!-- Stats Cards -->
      <div class="grid gap-3 sm:gap-4 grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 mb-6 sm:mb-8">
        <div class="bg-white rounded-lg border border-slate-200 p-4 sm:p-6">
          <div class="flex items-center justify-between">
            <div class="min-w-0 flex-1">
              <p class="text-xs sm:text-sm text-slate-600 mb-1 truncate">{{ $t('customerDashboard.openOrders') }}</p>
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
              <p class="text-xs sm:text-sm text-slate-600 mb-1 truncate">{{ $t('customerDashboard.wishlist') }}</p>
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
              <p class="text-xs sm:text-sm text-slate-600 mb-1 truncate">{{ $t('customerDashboard.cart') }}</p>
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
              <p class="text-xs sm:text-sm text-slate-600 mb-1 truncate">{{ $t('customerDashboard.notifications') }}</p>
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
          <h2 class="text-base sm:text-lg font-semibold text-slate-900">{{ $t('customerDashboard.recentOrders') }}</h2>
          <router-link to="/konto/bestellungen" class="text-xs sm:text-sm text-brand hover:underline touch-manipulation">
            {{ $t('customerDashboard.showAll') }}
          </router-link>
        </div>
        <div v-if="orders.length === 0" class="text-center py-8 text-slate-500">
          <Package class="h-12 w-12 mx-auto mb-2 text-slate-300" />
          <p class="text-sm">{{ $t('customerDashboard.noOrdersYet') }}</p>
        </div>
        <div v-else class="space-y-3 sm:space-y-4">
          <div
            v-for="order in orders.slice(0, 3)"
            :key="order.id"
            class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 p-3 sm:p-4 border border-slate-200 rounded-lg active:bg-slate-50 touch-manipulation"
          >
            <div>
              <p class="font-medium text-slate-900">{{ $t('customerDashboard.orderNumber', { id: order.id }) }}</p>
              <p class="text-sm text-slate-600">{{ order.date }}</p>
            </div>
            <div class="text-right">
              <p class="font-semibold text-slate-900">{{ formatPrice(order.total) }}</p>
              <span :class="getStatusClass(order.status)">{{ orderStatusLabel(order.status) }}</span>
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
          <h3 class="font-semibold text-sm sm:text-base text-slate-900 mb-1">{{ $t('customerDashboard.manageOrders') }}</h3>
          <p class="text-xs sm:text-sm text-slate-600">{{ $t('customerDashboard.viewAndTrackOrders') }}</p>
        </router-link>

        <router-link
          to="/konto/adressen"
          class="bg-white rounded-lg border border-slate-200 p-6 hover:border-brand/60 hover:shadow-md transition-all"
        >
          <MapPin class="h-8 w-8 text-brand mb-3" />
          <h3 class="font-semibold text-slate-900 mb-1">{{ $t('customerDashboard.manageAddresses') }}</h3>
          <p class="text-sm text-slate-600">{{ $t('customerDashboard.deliveryBillingAddresses') }}</p>
        </router-link>

        <router-link
          to="/konto/zahlungen"
          class="bg-white rounded-lg border border-slate-200 p-6 hover:border-brand/60 hover:shadow-md transition-all"
        >
          <CreditCard class="h-8 w-8 text-brand mb-3" />
          <h3 class="font-semibold text-slate-900 mb-1">{{ $t('customerDashboard.paymentMethods') }}</h3>
          <p class="text-sm text-slate-600">{{ $t('customerDashboard.paymentMethodsDesc') }}</p>
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useI18n } from 'vue-i18n'
import { useAuthStore } from '@theme-prism/stores/authStore'
import { useCartStore } from '@theme-prism/stores/cartStore'
import { useWishlistStore } from '@theme-prism/stores/wishlistStore'
import { useNotificationStore } from '@theme-prism/stores/notificationStore'
import { Package, Heart, ShoppingCart, Bell, MapPin, CreditCard } from 'lucide-vue-next'
import Breadcrumb from '@theme-prism/component/Breadcrumb.vue'

const { t } = useI18n()
const authStore = useAuthStore()
const cartStore = useCartStore()
const wishlistStore = useWishlistStore()
const notificationStore = useNotificationStore()

const breadcrumbItems = computed(() => [
  { label: t('customerDashboard.breadcrumbHome'), to: '/' },
  { label: t('customerDashboard.breadcrumbAccount'), to: null }
])

// Status key mapping for i18n (mock data may use DE or EN values)
const statusToKey = {
  Versandt: 'customerDashboard.statusShipped',
  Shipped: 'customerDashboard.statusShipped',
  Geliefert: 'customerDashboard.statusDelivered',
  Delivered: 'customerDashboard.statusDelivered',
  Bearbeitung: 'customerDashboard.statusProcessing',
  Processing: 'customerDashboard.statusProcessing'
}

const orderStatusLabel = (status) => t(statusToKey[status] || status)

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
    Versandt: 'text-xs px-2 py-1 rounded-full bg-blue-100 text-blue-700',
    Shipped: 'text-xs px-2 py-1 rounded-full bg-blue-100 text-blue-700',
    Geliefert: 'text-xs px-2 py-1 rounded-full bg-green-100 text-green-700',
    Delivered: 'text-xs px-2 py-1 rounded-full bg-green-100 text-green-700',
    Bearbeitung: 'text-xs px-2 py-1 rounded-full bg-yellow-100 text-yellow-700',
    Processing: 'text-xs px-2 py-1 rounded-full bg-yellow-100 text-yellow-700'
  }
  return classes[status] || 'text-xs px-2 py-1 rounded-full bg-slate-100 text-slate-700'
}
</script>
