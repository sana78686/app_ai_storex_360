<template>
  <div>
    <Breadcrumb :items="breadcrumbItems" />
    <div class="mx-auto max-w-7xl px-4 py-8">
      <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-900">Bestellungen & Retouren</h1>
        <p class="mt-2 text-sm text-slate-600">Verwalten Sie Ihre Bestellungen und verfolgen Sie den Versandstatus</p>
      </div>

      <Tabs
        :tabs="tabs"
        variant="top"
        :default-tab="0"
        @tab-change="handleTabChange"
      >
        <template #panel="{ tab, index }">
          <div v-if="index === 0" class="space-y-4">
            <div v-if="orders.length === 0" class="text-center py-12 bg-white rounded-lg border border-slate-200">
              <Package class="h-16 w-16 mx-auto mb-4 text-slate-300" />
              <p class="text-slate-600 mb-4">Sie haben noch keine Bestellungen</p>
              <router-link to="/" class="inline-block px-4 py-2 bg-brand text-white rounded-lg hover:bg-brand-dark">
                Jetzt einkaufen
              </router-link>
            </div>

            <div v-else v-for="order in orders" :key="order.id" class="bg-white rounded-lg border border-slate-200 p-4 sm:p-6">
              <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-2 sm:gap-0 mb-4">
                <div>
                  <h3 class="font-semibold text-sm sm:text-base text-slate-900">Bestellung #{{ order.id }}</h3>
                  <p class="text-xs sm:text-sm text-slate-600">Bestellt am {{ order.date }}</p>
                </div>
                <span :class="getStatusClass(order.status)">{{ order.status }}</span>
              </div>

              <div class="space-y-3 mb-4">
                <div v-for="item in order.items" :key="item.id" class="flex items-start sm:items-center gap-3 sm:gap-4">
                  <img :src="item.image" :alt="item.name" class="h-20 w-20 sm:h-16 sm:w-16 flex-shrink-0 object-cover rounded-lg" />
                  <div class="flex-1 min-w-0">
                    <p class="font-medium text-sm sm:text-base text-slate-900 line-clamp-2">{{ item.name }}</p>
                    <p class="text-xs sm:text-sm text-slate-600 mt-1">Menge: {{ item.quantity }}</p>
                  </div>
                  <p class="font-semibold text-sm sm:text-base text-slate-900 flex-shrink-0">{{ formatPrice(item.price) }}</p>
                </div>
              </div>

              <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pt-4 border-t border-slate-200">
                <div>
                  <p class="text-xs sm:text-sm text-slate-600">Gesamtbetrag</p>
                  <p class="text-lg sm:text-xl font-bold text-slate-900">{{ formatPrice(order.total) }}</p>
                </div>
                <div class="flex flex-wrap gap-2">
                  <router-link
                    :to="`/konto/bestellungen/${order.id}`"
                    class="px-3 sm:px-4 py-2.5 sm:py-2 border border-slate-200 rounded-lg hover:bg-slate-50 active:bg-slate-100 text-xs sm:text-sm font-medium touch-manipulation min-h-[44px] flex items-center justify-center"
                  >
                    Verfolgen
                  </router-link>
                  <button
                    v-if="order.canCancel"
                    @click="cancelOrder(order.id)"
                    class="px-3 sm:px-4 py-2.5 sm:py-2 border border-red-200 text-red-600 rounded-lg hover:bg-red-50 active:bg-red-100 text-xs sm:text-sm font-medium touch-manipulation min-h-[44px]"
                  >
                    Stornieren
                  </button>
                  <button
                    v-if="order.status === 'Geliefert'"
                    @click="requestReturn(order.id)"
                    class="px-3 sm:px-4 py-2.5 sm:py-2 border border-slate-200 rounded-lg hover:bg-slate-50 active:bg-slate-100 text-xs sm:text-sm font-medium touch-manipulation min-h-[44px]"
                  >
                    Retoure
                  </button>
                  <button
                    v-if="order.status === 'Geliefert'"
                    @click="buyAgain(order)"
                    class="px-3 sm:px-4 py-2.5 sm:py-2 bg-brand text-white rounded-lg hover:bg-brand-dark active:bg-brand-dark text-xs sm:text-sm font-medium touch-manipulation min-h-[44px]"
                  >
                    Erneut bestellen
                  </button>
                </div>
              </div>
            </div>
          </div>

          <div v-else-if="index === 1" class="space-y-4">
            <div v-if="returns.length === 0" class="text-center py-12 bg-white rounded-lg border border-slate-200">
              <Package class="h-16 w-16 mx-auto mb-4 text-slate-300" />
              <p class="text-slate-600">Keine Retouren vorhanden</p>
            </div>

            <div v-else v-for="returnItem in returns" :key="returnItem.id" class="bg-white rounded-lg border border-slate-200 p-6">
              <div class="flex items-start justify-between mb-4">
                <div>
                  <h3 class="font-semibold text-slate-900">Retoure #{{ returnItem.id }}</h3>
                  <p class="text-sm text-slate-600">Bestellung #{{ returnItem.orderId }}</p>
                </div>
                <span :class="getStatusClass(returnItem.status)">{{ returnItem.status }}</span>
              </div>
              <p class="text-sm text-slate-600 mb-4">{{ returnItem.reason }}</p>
              <div class="flex gap-2">
                <button class="px-4 py-2 border border-slate-200 rounded-lg hover:bg-slate-50 text-sm font-medium">
                  Details anzeigen
                </button>
              </div>
            </div>
          </div>
        </template>
      </Tabs>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { Package } from 'lucide-vue-next'
import Breadcrumb from '@theme-classic/component/Breadcrumb.vue'
import Tabs from '@theme-classic/component/ui/Tabs.vue'
import { useNotificationStore } from '@theme-classic/stores/notificationStore'
import { useOrderStore } from '@theme-classic/stores/orderStore'
import { useCartStore } from '@theme-classic/stores/cartStore'

const notificationStore = useNotificationStore()
const orderStore = useOrderStore()
const cartStore = useCartStore()

const breadcrumbItems = computed(() => [
  { label: 'Startseite', to: '/' },
  { label: 'Mein Konto', to: '/konto' },
  { label: 'Bestellungen', to: null }
])

const tabs = [
  { label: 'Bestellungen', content: '' },
  { label: 'Retouren', content: '' }
]

// Get orders from store
const orders = computed(() => orderStore.orders)

const returns = computed(() => orderStore.returns)

const formatPrice = (price) => {
  return new Intl.NumberFormat('de-DE', {
    style: 'currency',
    currency: 'EUR'
  }).format(price)
}

const getStatusClass = (status) => {
  const classes = {
    'Versandt': 'text-xs px-3 py-1 rounded-full bg-blue-100 text-blue-700 font-medium',
    'Geliefert': 'text-xs px-3 py-1 rounded-full bg-green-100 text-green-700 font-medium',
    'Bearbeitung': 'text-xs px-3 py-1 rounded-full bg-yellow-100 text-yellow-700 font-medium',
    'In Bearbeitung': 'text-xs px-3 py-1 rounded-full bg-yellow-100 text-yellow-700 font-medium'
  }
  return classes[status] || 'text-xs px-3 py-1 rounded-full bg-slate-100 text-slate-700 font-medium'
}

const handleTabChange = (index) => {
  // Handle tab change if needed
}

const buyAgain = (order) => {
  // Add all items to cart
  order.items.forEach(item => {
    cartStore.addItem(item, item.quantity)
  })
  notificationStore.success('Artikel wurden zum Warenkorb hinzugefügt', 'Erfolgreich')
}

const cancelOrder = (orderId) => {
  if (confirm('Möchten Sie diese Bestellung wirklich stornieren?')) {
    const success = orderStore.cancelOrder(orderId)
    if (success) {
      notificationStore.success('Bestellung wurde storniert', 'Storniert')
    } else {
      notificationStore.error('Bestellung kann nicht storniert werden', 'Fehler')
    }
  }
}

const requestReturn = (orderId) => {
  notificationStore.info(`Bitte öffnen Sie die Bestellungsdetails, um eine Retoure anzufordern`, 'Retoure')
}
</script>
