<template>
  <section v-if="cartStore.items.length === 0" class="mx-auto max-w-4xl px-4 py-12 text-center">
    <ShoppingBag class="mx-auto h-16 w-16 text-slate-300 mb-4" />
    <h1 class="text-2xl font-bold text-slate-900 mb-2">Noch keine Artikel im Warenkorb</h1>
    <p class="text-slate-600 mb-6">Füge Produkte hinzu, um zur Kasse zu gehen.</p>
    <router-link to="/">
      <button class="rounded-full bg-brand px-6 py-3 text-sm font-semibold text-white hover:bg-brand-dark">
        Weiter einkaufen
      </button>
    </router-link>
  </section>

  <div v-else>
    <Breadcrumb :items="breadcrumbItems" />
    <div class="mx-auto max-w-6xl px-3 sm:px-4 py-6 sm:py-8">
      <h1 class="mb-4 sm:mb-6 text-xl sm:text-2xl font-bold text-slate-900">Kasse</h1>

    <div class="grid gap-6 sm:gap-8 lg:grid-cols-3">
      <!-- Checkout Form -->
      <div class="lg:col-span-2 space-y-4 sm:space-y-6">
        <SectionCard title="Lieferadresse">
          <!-- Select from saved addresses -->
          <div v-if="addressStore.deliveryAddresses.length > 0" class="mb-4">
            <label class="block text-sm font-medium text-slate-900 mb-2">Gespeicherte Adresse auswählen</label>
            <select
              v-model="selectedDeliveryAddressId"
              @change="loadDeliveryAddress"
              class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-brand focus:border-brand"
            >
              <option value="">Neue Adresse eingeben</option>
              <option
                v-for="address in addressStore.deliveryAddresses"
                :key="address.id"
                :value="address.id"
              >
                {{ address.firstName }} {{ address.lastName }}, {{ address.street }}, {{ address.postalCode }} {{ address.city }}
                {{ address.isDefault ? '(Standard)' : '' }}
              </option>
            </select>
          </div>

          <!-- Address Form -->
          <div class="grid gap-4 sm:grid-cols-2">
            <TextField v-model="deliveryAddress.firstName" label="Vorname *" placeholder="Max" required />
            <TextField v-model="deliveryAddress.lastName" label="Nachname" placeholder="Mustermann" />
          </div>
          <TextField v-model="deliveryAddress.street" label="Straße und Hausnummer *" placeholder="Musterstraße 1" required />
          <div class="grid gap-4 sm:grid-cols-3">
            <TextField v-model="deliveryAddress.postalCode" label="PLZ *" placeholder="12345" required />
            <TextField v-model="deliveryAddress.city" label="Stadt *" placeholder="Berlin" class="sm:col-span-2" required />
          </div>
          <TextField v-model="deliveryAddress.country" label="Land" placeholder="Deutschland" />
          <TextField v-model="deliveryAddress.phone" label="Telefon (für Rückfragen)" placeholder="+49 ..." />

          <!-- Save address option -->
          <label class="flex items-center gap-2 mt-4">
            <input type="checkbox" v-model="saveDeliveryAddress" class="rounded" />
            <span class="text-sm text-slate-600">Diese Adresse für zukünftige Bestellungen speichern</span>
          </label>
        </SectionCard>

        <SectionCard title="Versandart">
          <div class="flex items-center justify-between rounded-lg border border-slate-200 bg-white p-4">
            <div class="flex items-center gap-3">
              <Truck class="h-5 w-5 text-brand" />
              <div>
                <p class="font-semibold text-slate-900">Standardversand</p>
                <p class="text-sm text-slate-600">Lieferung in 2-4 Werktagen</p>
              </div>
            </div>
            <span class="text-sm font-semibold text-slate-900">
              {{ shipping === 0 ? 'Kostenlos' : `${shipping.toFixed(2)} €` }}
            </span>
          </div>
        </SectionCard>

        <SectionCard title="Zahlung">
          <!-- Saved Payment Methods -->
          <div v-if="paymentStore.paymentMethods.length > 0" class="mb-4 space-y-3">
            <p class="text-sm font-medium text-slate-900 mb-2">Gespeicherte Zahlungsmethoden</p>
            <label
              v-for="method in paymentStore.paymentMethods"
              :key="method.id"
              :class="[
                'group flex items-center justify-between rounded-lg border-2 bg-white p-4 cursor-pointer transition-all duration-200',
                selectedPayment === `saved-${method.id}`
                  ? 'border-brand bg-brand/5 shadow-md'
                  : 'border-slate-200 hover:border-brand/40 hover:shadow-sm'
              ]"
            >
              <div class="flex items-center gap-4 flex-1">
                <div class="flex-shrink-0">
                  <img
                    v-if="method.image"
                    :src="method.image"
                    :alt="method.type"
                    class="h-8 w-auto max-w-[80px] object-contain transition-transform duration-200 group-hover:scale-105"
                    @error="handleImageError($event, method)"
                    loading="lazy"
                  />
                  <CreditCard v-else class="h-6 w-6 text-slate-400" />
                </div>
                <div class="flex-1 min-w-0">
                  <span class="text-sm font-semibold text-slate-900 block">
                    {{ method.type }}
                    <span v-if="method.last4"> •••• {{ method.last4 }}</span>
                  </span>
                  <span v-if="method.expiry" class="text-xs text-slate-500 mt-0.5 block">Ablauf: {{ method.expiry }}</span>
                  <span v-else class="text-xs text-slate-500 mt-0.5 block">Gespeichert</span>
                </div>
              </div>
              <div class="flex-shrink-0 ml-4">
                <div
                  :class="[
                    'relative h-5 w-5 rounded-full border-2 transition-all duration-200',
                    selectedPayment === `saved-${method.id}`
                      ? 'border-brand bg-brand'
                      : 'border-slate-300 group-hover:border-brand/60'
                  ]"
                >
                  <div
                    v-if="selectedPayment === `saved-${method.id}`"
                    class="absolute inset-0 flex items-center justify-center"
                  >
                    <div class="h-2.5 w-2.5 rounded-full bg-white"></div>
                  </div>
                </div>
                <input
                  type="radio"
                  name="payment"
                  :value="`saved-${method.id}`"
                  v-model="selectedPayment"
                  class="sr-only"
                />
              </div>
            </label>
          </div>

          <!-- Available Payment Methods -->
          <div class="space-y-3" :class="{ 'mt-4 pt-4 border-t border-slate-200': paymentStore.paymentMethods.length > 0 }">
            <p v-if="paymentStore.paymentMethods.length > 0" class="text-sm font-medium text-slate-900 mb-2">Andere Zahlungsmethoden</p>
            <label
              v-for="method in paymentMethods"
              :key="method.name"
              :class="[
                'group flex items-center justify-between rounded-lg border-2 bg-white p-4 cursor-pointer transition-all duration-200',
                selectedPayment === method.name
                  ? 'border-brand bg-brand/5 shadow-md'
                  : 'border-slate-200 hover:border-brand/40 hover:shadow-sm'
              ]"
            >
              <div class="flex items-center gap-4 flex-1">
                <div class="flex-shrink-0">
                  <img
                    v-if="method.image"
                    :src="method.image"
                    :alt="method.alt"
                    class="h-8 w-auto max-w-[80px] object-contain transition-transform duration-200 group-hover:scale-105"
                    @error="handleImageError($event, method)"
                    loading="lazy"
                  />
                  <CreditCard v-else class="h-6 w-6 text-slate-400" />
                </div>
                <div class="flex-1 min-w-0">
                  <span class="text-sm font-semibold text-slate-900 block">{{ method.displayName || method.name }}</span>
                  <span v-if="method.name === 'PayPal'" class="text-xs text-slate-500 mt-0.5 block">Schnell & sicher</span>
                  <span v-else-if="method.name === 'Klarna'" class="text-xs text-slate-500 mt-0.5 block">Sofortüberweisung</span>
                </div>
              </div>
              <div class="flex-shrink-0 ml-4">
                <div
                  :class="[
                    'relative h-5 w-5 rounded-full border-2 transition-all duration-200',
                    selectedPayment === method.name
                      ? 'border-brand bg-brand'
                      : 'border-slate-300 group-hover:border-brand/60'
                  ]"
                >
                  <div
                    v-if="selectedPayment === method.name"
                    class="absolute inset-0 flex items-center justify-center"
                  >
                    <div class="h-2.5 w-2.5 rounded-full bg-white"></div>
                  </div>
                </div>
                <input
                  type="radio"
                  name="payment"
                  :value="method.name"
                  v-model="selectedPayment"
                  class="sr-only"
                />
              </div>
            </label>
          </div>
        </SectionCard>
      </div>

      <!-- Summary -->
      <div class="space-y-4">
        <div class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
          <h2 class="mb-3 text-lg font-semibold text-slate-900">Bestellübersicht</h2>
          <div class="space-y-2 text-sm text-slate-700">
            <div class="flex justify-between">
              <span>Zwischensumme</span>
              <span class="font-medium">{{ cartStore.totalPrice.toFixed(2) }} €</span>
            </div>
            <div class="flex justify-between">
              <span>Versand</span>
              <span class="font-medium">{{ shipping === 0 ? 'Kostenlos' : `${shipping.toFixed(2)} €` }}</span>
            </div>
            <div class="flex justify-between border-t border-slate-200 pt-2 text-base font-semibold text-slate-900">
              <span>Gesamt</span>
              <span>{{ total.toFixed(2) }} €</span>
            </div>
          </div>
          <button
            @click="handlePlaceOrder"
            :disabled="submitting"
            class="mt-4 w-full rounded-full bg-brand px-6 py-4 sm:py-3 text-base sm:text-sm font-semibold text-white hover:bg-brand-dark active:bg-brand-dark disabled:opacity-50 touch-manipulation min-h-[52px] sm:min-h-[44px] transition-all"
          >
            {{ submitting ? 'Wird verarbeitet...' : 'Jetzt kaufen' }}
          </button>
          <p class="mt-2 flex items-center gap-2 text-xs text-slate-500">
            <Lock class="h-4 w-4" />
            Sichere Zahlung & Käuferschutz
          </p>
        </div>

        <div class="rounded-lg border border-slate-200 bg-white p-4 text-sm text-slate-700">
          <div class="flex items-start gap-2">
            <Check class="h-4 w-4 text-brand mt-0.5" />
            <p>14 Tage Rückgaberecht</p>
          </div>
          <div class="mt-2 flex items-start gap-2">
            <Check class="h-4 w-4 text-brand mt-0.5" />
            <p>Kostenloser Versand ab 50 €</p>
          </div>
          <div class="mt-2 flex items-start gap-2">
            <Check class="h-4 w-4 text-brand mt-0.5" />
            <p>Kundenservice & schnelle Lieferung</p>
          </div>
        </div>
      </div>
    </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useCartStore } from '@theme-classic/stores/cartStore'
import { useAddressStore } from '@theme-classic/stores/addressStore'
import { usePaymentStore } from '@theme-classic/stores/paymentStore'
import { useNotificationStore } from '@theme-classic/stores/notificationStore'
import { Check, Lock, Truck, CreditCard, ShoppingBag } from 'lucide-vue-next'
import SectionCard from '@theme-classic/component/ui/SectionCard.vue'
import TextField from '@theme-classic/component/ui/TextField.vue'
import Breadcrumb from '@theme-classic/component/Breadcrumb.vue'
import { paymentMethods as allPaymentMethods, getPaymentMethod } from '@theme-classic/config/paymentShipping'

const router = useRouter()
const cartStore = useCartStore()
const addressStore = useAddressStore()
const paymentStore = usePaymentStore()
const notificationStore = useNotificationStore()
const submitting = ref(false)
const selectedPayment = ref('PayPal')
const selectedDeliveryAddressId = ref(null)
const saveDeliveryAddress = ref(false)

const deliveryAddress = ref({
  firstName: '',
  lastName: '',
  street: '',
  postalCode: '',
  city: '',
  country: 'Deutschland',
  phone: ''
})

// Map checkout payment methods to config
const checkoutPaymentMethods = [
  { name: 'PayPal', displayName: 'PayPal' },
  { name: 'Kreditkarte', displayName: 'Kreditkarte (Visa, Mastercard)' },
  { name: 'Klarna', displayName: 'Klarna Sofortüberweisung' }
]

const paymentMethods = checkoutPaymentMethods.map(m => ({
  ...m,
  ...getPaymentMethod(m.name)
}))

const shipping = computed(() => cartStore.totalPrice >= 50 ? 0 : 4.99)
const total = computed(() => cartStore.totalPrice + shipping.value)

const breadcrumbItems = computed(() => [
  { label: 'Startseite', to: '/' },
  { label: 'Warenkorb', to: '/warenkorb' },
  { label: 'Kasse', to: null }
])

const loadDeliveryAddress = () => {
  if (selectedDeliveryAddressId.value) {
    const address = addressStore.deliveryAddresses.find(a => a.id === selectedDeliveryAddressId.value)
    if (address) {
      deliveryAddress.value = {
        firstName: address.firstName,
        lastName: address.lastName,
        street: address.street,
        postalCode: address.postalCode,
        city: address.city,
        country: address.country,
        phone: ''
      }
    }
  } else {
    // Reset to empty
    deliveryAddress.value = {
      firstName: '',
      lastName: '',
      street: '',
      postalCode: '',
      city: '',
      country: 'Deutschland',
      phone: ''
    }
  }
}

onMounted(() => {
  // Load default address if available
  if (addressStore.defaultDeliveryAddress) {
    selectedDeliveryAddressId.value = addressStore.defaultDeliveryAddress.id
    loadDeliveryAddress()
  }

  // Set default payment method
  if (paymentStore.defaultPaymentMethod) {
    selectedPayment.value = `saved-${paymentStore.defaultPaymentMethod.id}`
  }
})

const handlePlaceOrder = () => {
  // Validate delivery address
  if (!deliveryAddress.value.firstName || !deliveryAddress.value.street || !deliveryAddress.value.postalCode || !deliveryAddress.value.city) {
    notificationStore.error('Bitte füllen Sie alle Pflichtfelder der Lieferadresse aus', 'Fehler')
    return
  }

  // Save address if requested
  if (saveDeliveryAddress.value) {
    addressStore.addDeliveryAddress({
      firstName: deliveryAddress.value.firstName,
      lastName: deliveryAddress.value.lastName,
      street: deliveryAddress.value.street,
      postalCode: deliveryAddress.value.postalCode,
      city: deliveryAddress.value.city,
      country: deliveryAddress.value.country
    })
  }

  submitting.value = true
  setTimeout(() => {
    cartStore.clearCart()
    submitting.value = false
    notificationStore.success('Vielen Dank! Ihre Bestellung wurde angenommen.', 'Bestellung erfolgreich')
    router.push('/konto/bestellungen')
  }, 900)
}

const handleImageError = (event, item) => {
  const img = event.target
  if (img) {
    img.style.display = 'none'
    // Show text fallback if not already present
    const parent = img.parentElement
    if (parent && !parent.querySelector('.payment-fallback')) {
      const span = document.createElement('span')
      span.className = 'payment-fallback text-sm font-medium text-slate-700'
      span.textContent = item.displayName || item.name
      parent.insertBefore(span, img.nextSibling)
    }
  }
}
</script>

