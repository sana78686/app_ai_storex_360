<template>
  <div>
    <Breadcrumb :items="breadcrumbItems" />
    <div class="mx-auto max-w-4xl px-3 sm:px-4 py-6 sm:py-8">
      <div class="mb-4 sm:mb-6">
        <button
          @click="$router.back()"
          class="mb-3 sm:mb-4 flex items-center gap-2 text-sm text-slate-600 hover:text-brand active:text-brand-dark touch-manipulation min-h-[44px]"
        >
          <ArrowLeft class="h-4 w-4" />
          Zurück zu Bestellungen
        </button>
        <h1 class="text-xl sm:text-2xl font-bold text-slate-900">Bestellungsverfolgung</h1>
        <p class="mt-2 text-xs sm:text-sm text-slate-600">Bestellung #{{ orderId }}</p>
      </div>

      <div v-if="order" class="space-y-6">
        <!-- Order Status Timeline -->
        <div class="bg-white rounded-lg border border-slate-200 p-6">
          <h2 class="text-lg font-semibold text-slate-900 mb-4">Bestellstatus</h2>
          <div class="relative">
            <div class="absolute left-4 top-0 bottom-0 w-0.5 bg-slate-200"></div>
            <div class="space-y-6">
              <div
                v-for="(status, index) in orderStatuses"
                :key="index"
                class="relative flex items-start gap-4"
              >
                <div
                  :class="[
                    'relative z-10 flex h-8 w-8 items-center justify-center rounded-full border-2',
                    status.completed
                      ? 'border-brand bg-brand text-white'
                      : status.active
                      ? 'border-brand bg-white'
                      : 'border-slate-300 bg-white'
                  ]"
                >
                  <Check v-if="status.completed" class="h-4 w-4" />
                  <div v-else-if="status.active" class="h-3 w-3 rounded-full bg-brand"></div>
                </div>
                <div class="flex-1 pb-6">
                  <p
                    :class="[
                      'font-medium',
                      status.completed || status.active ? 'text-slate-900' : 'text-slate-500'
                    ]"
                  >
                    {{ status.label }}
                  </p>
                  <p class="text-sm text-slate-600">{{ status.date }}</p>
                  <p v-if="status.description" class="mt-1 text-sm text-slate-600">
                    {{ status.description }}
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Delivery Information -->
        <div class="bg-white rounded-lg border border-slate-200 p-6">
          <h2 class="text-lg font-semibold text-slate-900 mb-4">Lieferinformationen</h2>
          <div class="grid gap-4 sm:grid-cols-2">
            <div>
              <p class="text-sm font-medium text-slate-600 mb-1">Versanddienstleister</p>
              <p class="text-slate-900">{{ order.shipping.carrier }}</p>
            </div>
            <div>
              <p class="text-sm font-medium text-slate-600 mb-1">Sendungsnummer</p>
              <p class="text-slate-900 font-mono">{{ order.shipping.trackingNumber }}</p>
              <a
                :href="order.shipping.trackingUrl"
                target="_blank"
                class="text-sm text-brand hover:underline mt-1 inline-block"
              >
                Verfolgen auf {{ order.shipping.carrier }} →
              </a>
            </div>
            <div>
              <p class="text-sm font-medium text-slate-600 mb-1">Voraussichtliche Lieferung</p>
              <p class="text-slate-900">{{ order.shipping.estimatedDelivery }}</p>
            </div>
            <div>
              <p class="text-sm font-medium text-slate-600 mb-1">Lieferadresse</p>
              <p class="text-slate-900">{{ order.shipping.address }}</p>
            </div>
          </div>
        </div>

        <!-- Order Items -->
        <div class="bg-white rounded-lg border border-slate-200 p-6">
          <h2 class="text-lg font-semibold text-slate-900 mb-4">Bestellte Artikel</h2>
          <div class="space-y-4">
            <div
              v-for="item in order.items"
              :key="item.id"
              class="flex items-start gap-4 pb-4 border-b border-slate-100 last:border-0"
            >
              <img
                :src="item.image"
                :alt="item.name"
                class="h-20 w-20 object-cover rounded-lg"
              />
              <div class="flex-1">
                <h3 class="font-medium text-slate-900 mb-1">{{ item.name }}</h3>
                <p class="text-sm text-slate-600">Menge: {{ item.quantity }}</p>
                <p class="text-sm font-medium text-slate-900 mt-1">
                  {{ formatPrice(item.price) }}
                </p>
                <div v-if="item.canReview" class="mt-3">
                  <button
                    @click="openReviewModal(item)"
                    class="text-sm text-brand hover:underline"
                  >
                    Bewertung schreiben
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Order Summary -->
        <div class="bg-white rounded-lg border border-slate-200 p-6">
          <h2 class="text-lg font-semibold text-slate-900 mb-4">Bestellübersicht</h2>
          <div class="space-y-2">
            <div class="flex justify-between text-sm">
              <span class="text-slate-600">Zwischensumme</span>
              <span class="text-slate-900">{{ formatPrice(order.subtotal) }}</span>
            </div>
            <div class="flex justify-between text-sm">
              <span class="text-slate-600">Versandkosten</span>
              <span class="text-slate-900">{{ formatPrice(order.shippingCost) }}</span>
            </div>
            <div class="flex justify-between text-sm">
              <span class="text-slate-600">MwSt.</span>
              <span class="text-slate-900">{{ formatPrice(order.tax) }}</span>
            </div>
            <div class="border-t border-slate-200 pt-2 flex justify-between font-semibold">
              <span class="text-slate-900">Gesamtbetrag</span>
              <span class="text-slate-900">{{ formatPrice(order.total) }}</span>
            </div>
          </div>
        </div>

        <!-- Refunds Section -->
        <div v-if="order.refunds && order.refunds.length > 0" class="bg-white rounded-lg border border-slate-200 p-6">
          <h2 class="text-lg font-semibold text-slate-900 mb-4">Rückerstattungen</h2>
          <div class="space-y-4">
            <div
              v-for="refund in order.refunds"
              :key="refund.id"
              class="border border-slate-200 rounded-lg p-4"
            >
              <div class="flex items-start justify-between mb-2">
                <div>
                  <p class="font-medium text-slate-900">Rückerstattung #{{ refund.id }}</p>
                  <p class="text-sm text-slate-600">{{ refund.reason }}</p>
                </div>
                <span :class="getRefundStatusClass(refund.status)">{{ refund.status }}</span>
              </div>
              <div class="mt-2 flex justify-between text-sm">
                <span class="text-slate-600">Betrag:</span>
                <span class="font-semibold text-slate-900">{{ formatPrice(refund.amount) }}</span>
              </div>
              <div v-if="refund.processedDate" class="mt-2 text-xs text-slate-500">
                Verarbeitet am: {{ new Date(refund.processedDate).toLocaleDateString('de-DE') }}
              </div>
              <div v-else-if="refund.expectedDate" class="mt-2 text-xs text-slate-500">
                Erwartet bis: {{ new Date(refund.expectedDate).toLocaleDateString('de-DE') }}
              </div>
            </div>
          </div>
        </div>

        <!-- Actions -->
        <div class="flex flex-wrap gap-2 sm:gap-3">
          <button
            v-if="order.canCancel"
            @click="showCancelModal = true"
            class="px-3 sm:px-4 py-2.5 sm:py-2 border-2 border-red-200 text-red-600 rounded-lg hover:bg-red-50 active:bg-red-100 text-xs sm:text-sm font-medium touch-manipulation min-h-[44px]"
          >
            Bestellung stornieren
          </button>
          <button
            v-if="order.status === 'Geliefert'"
            @click="openPartialReturnModal"
            class="px-3 sm:px-4 py-2.5 sm:py-2 border-2 border-slate-200 rounded-lg hover:bg-slate-50 active:bg-slate-100 text-xs sm:text-sm font-medium touch-manipulation min-h-[44px]"
          >
            Teilweise Retoure
          </button>
          <button
            v-if="order.status === 'Geliefert'"
            @click="requestReturn(order.id)"
            class="px-3 sm:px-4 py-2.5 sm:py-2 border-2 border-slate-200 rounded-lg hover:bg-slate-50 active:bg-slate-100 text-xs sm:text-sm font-medium touch-manipulation min-h-[44px]"
          >
            Vollständige Retoure
          </button>
          <button
            v-if="order.status === 'Geliefert'"
            @click="downloadInvoice(order.id)"
            class="px-3 sm:px-4 py-2.5 sm:py-2 border-2 border-slate-200 rounded-lg hover:bg-slate-50 active:bg-slate-100 text-xs sm:text-sm font-medium touch-manipulation min-h-[44px]"
          >
            <span class="hidden sm:inline">Rechnung herunterladen</span>
            <span class="sm:hidden">Rechnung</span>
          </button>
          <button
            @click="buyAgain(order)"
            class="px-3 sm:px-4 py-2.5 sm:py-2 bg-brand text-white rounded-lg hover:bg-brand-dark active:bg-brand-dark text-xs sm:text-sm font-medium touch-manipulation min-h-[44px]"
          >
            Erneut bestellen
          </button>
        </div>
      </div>

      <div v-else class="text-center py-12">
        <Package class="h-16 w-16 mx-auto mb-4 text-slate-300" />
        <p class="text-slate-600">Bestellung nicht gefunden</p>
      </div>
    </div>

    <!-- Review Modal -->
    <ReviewModal
      v-if="showReviewModal"
      :product="selectedProduct"
      :order-id="orderId"
      @close="showReviewModal = false"
      @submitted="handleReviewSubmitted"
    />

    <!-- Cancel Order Modal -->
    <div
      v-if="showCancelModal"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-3 sm:p-4"
      @click.self="showCancelModal = false"
    >
      <div class="bg-white rounded-lg max-w-md w-full p-4 sm:p-6 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-base sm:text-lg font-semibold text-slate-900">Bestellung stornieren</h3>
          <button @click="showCancelModal = false" class="text-slate-400 hover:text-slate-600 active:text-slate-700 touch-manipulation min-h-[44px] min-w-[44px] flex items-center justify-center">
            <X class="h-5 w-5" />
          </button>
        </div>
        <p class="text-sm text-slate-600 mb-4">
          Möchten Sie die Bestellung #{{ orderId }} wirklich stornieren? Die vollständige Rückerstattung wird automatisch verarbeitet.
        </p>
        <div class="flex flex-col sm:flex-row gap-2 sm:gap-3">
          <button
            @click="showCancelModal = false"
            class="flex-1 px-4 py-3 sm:py-2 border-2 border-slate-200 rounded-lg hover:bg-slate-50 active:bg-slate-100 text-base sm:text-sm touch-manipulation min-h-[52px] sm:min-h-[44px] transition-all"
          >
            Abbrechen
          </button>
          <button
            @click="handleCancelOrder"
            class="flex-1 px-4 py-3 sm:py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 active:bg-red-800 text-base sm:text-sm touch-manipulation min-h-[52px] sm:min-h-[44px] transition-all"
          >
            Stornieren
          </button>
        </div>
      </div>
    </div>

    <!-- Partial Return Modal -->
    <div
      v-if="showPartialReturnModal"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
      @click.self="showPartialReturnModal = false"
    >
      <div class="bg-white rounded-lg max-w-2xl w-full max-h-[90vh] overflow-y-auto p-6">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-semibold text-slate-900">Teilweise Retoure</h3>
          <button @click="showPartialReturnModal = false" class="text-slate-400 hover:text-slate-600">
            <X class="h-5 w-5" />
          </button>
        </div>
        <p class="text-sm text-slate-600 mb-4">
          Wählen Sie die Artikel aus, die Sie zurückgeben möchten:
        </p>
        <div class="space-y-3 mb-4">
          <label
            v-for="item in order.items"
            :key="item.id"
            class="flex items-start gap-3 p-3 border border-slate-200 rounded-lg hover:bg-slate-50 cursor-pointer"
          >
            <input
              type="checkbox"
              :value="item.id"
              v-model="selectedItemsForReturn"
              class="mt-1"
            />
            <img :src="item.image" :alt="item.name" class="h-16 w-16 object-cover rounded" />
            <div class="flex-1">
              <p class="font-medium text-slate-900">{{ item.name }}</p>
              <p class="text-sm text-slate-600">Menge: {{ item.quantity }}</p>
              <p class="text-sm font-medium text-slate-900">{{ formatPrice(item.price) }}</p>
            </div>
          </label>
        </div>
        <div class="mb-4">
          <label class="block text-sm font-medium text-slate-900 mb-2">Grund für Retoure</label>
          <select
            v-model="returnReason"
            class="w-full px-4 py-2 border border-slate-300 rounded-lg"
          >
            <option value="">Bitte wählen</option>
            <option value="defekt">Artikel defekt</option>
            <option value="falsch">Falscher Artikel</option>
            <option value="groesse">Falsche Größe</option>
            <option value="nicht_gefallen">Gefällt mir nicht</option>
            <option value="beschaedigt">Beschädigt angekommen</option>
            <option value="sonstiges">Sonstiges</option>
          </select>
        </div>
        <div class="flex flex-col sm:flex-row gap-2 sm:gap-3">
          <button
            @click="showPartialReturnModal = false"
            class="flex-1 px-4 py-3 sm:py-2 border-2 border-slate-200 rounded-lg hover:bg-slate-50 active:bg-slate-100 text-base sm:text-sm touch-manipulation min-h-[52px] sm:min-h-[44px] transition-all"
          >
            Abbrechen
          </button>
          <button
            @click="handlePartialReturn"
            :disabled="selectedItemsForReturn.length === 0 || !returnReason"
            class="flex-1 px-4 py-3 sm:py-2 bg-brand text-white rounded-lg hover:bg-brand-dark active:bg-brand-dark disabled:opacity-50 text-base sm:text-sm touch-manipulation min-h-[52px] sm:min-h-[44px] transition-all"
          >
            Retoure anfordern
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useRoute } from 'vue-router'
import { Package, Check, ArrowLeft, X } from 'lucide-vue-next'
import Breadcrumb from '@theme-prism/component/Breadcrumb.vue'
// import ReviewModal from '@theme-prism/component/ReviewModal.vue'
import { useNotificationStore } from '@theme-prism/stores/notificationStore'
import { useOrderStore } from '@theme-prism/stores/orderStore'
import { useNotificationPreferencesStore } from '@theme-prism/stores/notificationPreferencesStore'

const route = useRoute()
const notificationStore = useNotificationStore()
const orderStore = useOrderStore()
const notificationPrefsStore = useNotificationPreferencesStore()

const orderId = computed(() => route.params.orderId)
const showReviewModal = ref(false)
const showCancelModal = ref(false)
const showPartialReturnModal = ref(false)
const selectedProduct = ref(null)
const selectedItemsForReturn = ref([])
const returnReason = ref('')

const breadcrumbItems = computed(() => [
  { label: 'Startseite', to: '/' },
  { label: 'Mein Konto', to: '/konto' },
  { label: 'Bestellungen', to: '/konto/bestellungen' },
  { label: `Bestellung #${orderId.value}`, to: null }
])

// Get order from store
const order = computed(() => orderStore.getOrderById(orderId.value))

const orderStatuses = computed(() => {
  if (!order.value) return []

  const statusMap = {
    'Bestellt': { completed: true, active: false, date: order.value.orderDate },
    'Bezahlt': { completed: order.value.status !== 'Bestellt', active: order.value.status === 'Bezahlt', date: order.value.paymentDate },
    'Versandt': { completed: ['Versandt', 'Unterwegs', 'Geliefert'].includes(order.value.status), active: order.value.status === 'Versandt', date: order.value.shippingDate },
    'Unterwegs': { completed: order.value.status === 'Geliefert', active: order.value.status === 'Unterwegs', date: order.value.inTransitDate },
    'Geliefert': { completed: order.value.status === 'Geliefert', active: false, date: order.value.deliveryDate }
  }

  return [
    {
      label: 'Bestellt',
      ...statusMap['Bestellt'],
      description: 'Ihre Bestellung wurde erhalten'
    },
    {
      label: 'Bezahlt',
      ...statusMap['Bezahlt'],
      description: 'Zahlung wurde erfolgreich verarbeitet'
    },
    {
      label: 'Versandt',
      ...statusMap['Versandt'],
      description: order.value.shipping?.carrier ? `Versandt mit ${order.value.shipping.carrier}` : 'Paket wurde versendet'
    },
    {
      label: 'Unterwegs',
      ...statusMap['Unterwegs'],
      description: 'Ihr Paket ist auf dem Weg'
    },
    {
      label: 'Geliefert',
      ...statusMap['Geliefert'],
      description: 'Paket wurde zugestellt'
    }
  ]
})

onMounted(() => {
  // Start real-time order status polling
  if (order.value) {
    orderStore.startOrderPolling(orderId.value, (updatedOrder) => {
      // Send notification based on preferences
      if (updatedOrder.status === 'Unterwegs') {
        notificationPrefsStore.sendShippingUpdate(
          updatedOrder.id,
          updatedOrder.shipping?.trackingNumber,
          `Ihre Bestellung #${updatedOrder.id} ist jetzt unterwegs.`
        )
        notificationStore.info(`Bestellung #${updatedOrder.id} ist jetzt unterwegs!`, 'Versandaktualisierung')
      } else if (updatedOrder.status === 'Geliefert') {
        notificationPrefsStore.sendDeliveryConfirmation(
          updatedOrder.id,
          `Ihre Bestellung #${updatedOrder.id} wurde erfolgreich zugestellt.`
        )
        notificationStore.success(`Bestellung #${updatedOrder.id} wurde geliefert!`, 'Zustellung bestätigt')
      }
    })
  }
})

onUnmounted(() => {
  // Stop polling when component unmounts
  orderStore.stopOrderPolling()
})

const formatPrice = (price) => {
  return new Intl.NumberFormat('de-DE', {
    style: 'currency',
    currency: 'EUR'
  }).format(price)
}

const openReviewModal = (item) => {
  selectedProduct.value = item
  showReviewModal.value = true
}

const handleReviewSubmitted = () => {
  showReviewModal.value = false
  notificationStore.success('Bewertung wurde zur Moderation übermittelt', 'Erfolgreich')
}

const handleCancelOrder = () => {
  const success = orderStore.cancelOrder(orderId.value)
  if (success) {
    showCancelModal.value = false
    notificationStore.success('Bestellung wurde storniert. Rückerstattung wird verarbeitet.', 'Storniert')
    notificationPrefsStore.sendOrderUpdate(orderId.value, 'Storniert', 'Ihre Bestellung wurde storniert.')
  } else {
    notificationStore.error('Bestellung kann nicht storniert werden', 'Fehler')
  }
}

const openPartialReturnModal = () => {
  selectedItemsForReturn.value = []
  returnReason.value = ''
  showPartialReturnModal.value = true
}

const handlePartialReturn = () => {
  if (selectedItemsForReturn.value.length === 0 || !returnReason.value) {
    notificationStore.error('Bitte wählen Sie mindestens einen Artikel und geben Sie einen Grund an', 'Fehler')
    return
  }

  const items = selectedItemsForReturn.value.map(itemId => {
    const item = order.value.items.find(i => i.id === itemId)
    return {
      itemId: item.id,
      quantity: item.quantity,
      reason: returnReason.value
    }
  })

  const returnRequest = orderStore.requestPartialReturn(orderId.value, items)
  if (returnRequest) {
    showPartialReturnModal.value = false
    notificationStore.success('Teilweise Retoure wurde angefordert', 'Retoure angefordert')
    notificationPrefsStore.sendReturnUpdate(returnRequest.id, 'Antrag gestellt', 'Ihre Retoure wurde angefordert.')
  }
}

const requestReturn = (orderId) => {
  // Full return - return all items
  const items = order.value.items.map(item => ({
    itemId: item.id,
    quantity: item.quantity,
    reason: 'Vollständige Retoure'
  }))

  const returnRequest = orderStore.requestPartialReturn(orderId, items)
  if (returnRequest) {
    notificationStore.info(`Vollständige Retoure für Bestellung #${orderId} angefordert`, 'Retoure angefordert')
    notificationPrefsStore.sendReturnUpdate(returnRequest.id, 'Antrag gestellt', 'Ihre Retoure wurde angefordert.')
  }
}

const downloadInvoice = (orderId) => {
  notificationStore.info('Rechnung wird heruntergeladen...', 'Download')
  // In production, trigger PDF download
}

const buyAgain = (order) => {
  // Add all items to cart
  const { useCartStore } = require('@theme-prism/stores/cartStore')
  const cartStore = useCartStore()
  order.items.forEach(item => {
    cartStore.addItem(item, item.quantity)
  })
  notificationStore.success('Artikel wurden zum Warenkorb hinzugefügt', 'Erfolgreich')
}

const getRefundStatusClass = (status) => {
  const classes = {
    'Beantragt': 'text-xs px-3 py-1 rounded-full bg-yellow-100 text-yellow-700 font-medium',
    'In Bearbeitung': 'text-xs px-3 py-1 rounded-full bg-blue-100 text-blue-700 font-medium',
    'Abgeschlossen': 'text-xs px-3 py-1 rounded-full bg-green-100 text-green-700 font-medium'
  }
  return classes[status] || 'text-xs px-3 py-1 rounded-full bg-slate-100 text-slate-700 font-medium'
}
</script>
