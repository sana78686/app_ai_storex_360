<template>
  <div>
    <Breadcrumb :items="breadcrumbItems" />
    <div class="mx-auto max-w-4xl px-4 py-8">
      <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-900">Zahlungsmethoden</h1>
        <p class="mt-2 text-sm text-slate-600">Verwalten Sie Ihre Zahlungsmethoden</p>
      </div>

      <div class="space-y-6">
        <!-- Select Payment Method Type -->
        <div class="bg-white rounded-lg border border-slate-200 p-6">
          <h2 class="text-lg font-semibold text-slate-900 mb-4">Zahlungsmethode auswählen</h2>
          <p class="text-sm text-slate-600 mb-4">Wählen Sie eine Zahlungsmethode aus, die Sie hinzufügen möchten:</p>

          <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4 mb-6">
            <button
              v-for="method in availablePaymentMethods"
              :key="method.name"
              @click="selectPaymentType(method)"
              :class="[
                'p-4 border-2 rounded-lg transition-all cursor-pointer',
                selectedPaymentType?.name === method.name
                  ? 'border-brand bg-brand/5'
                  : 'border-slate-200 hover:border-brand/40'
              ]"
            >
              <div class="flex flex-col items-center gap-2">
                <img
                  v-if="method.image"
                  :src="method.image"
                  :alt="method.alt"
                  class="h-12 w-auto max-w-[100px] object-contain"
                  @error="handleImageError($event, method)"
                  loading="lazy"
                />
                <span class="text-sm font-medium text-slate-900">{{ method.name }}</span>
              </div>
            </button>
          </div>

          <!-- Credit Card Form (only for Visa/Maestro) -->
          <div v-if="selectedPaymentType && (selectedPaymentType.name === 'Visa' || selectedPaymentType.name === 'Maestro')" class="mt-6 pt-6 border-t border-slate-200">
            <h3 class="text-md font-semibold text-slate-900 mb-4">Kartendetails eingeben</h3>
            <form @submit.prevent="addPaymentMethod" class="space-y-4">
              <div class="grid gap-4 sm:grid-cols-2">
                <TextField
                  v-model="newPayment.cardNumber"
                  label="Kartennummer"
                  placeholder="1234 5678 9012 3456"
                  type="text"
                  maxlength="19"
                  @input="formatCardNumber"
                />
                <TextField
                  v-model="newPayment.cardholder"
                  label="Karteninhaber"
                  placeholder="Max Mustermann"
                />
                <TextField
                  v-model="newPayment.expiry"
                  label="Ablaufdatum"
                  placeholder="MM/JJ"
                  maxlength="5"
                  @input="formatExpiry"
                />
                <TextField
                  v-model="newPayment.cvv"
                  label="CVV"
                  placeholder="123"
                  type="password"
                  maxlength="4"
                />
              </div>
              <button type="submit" class="w-full sm:w-auto px-6 py-3 sm:py-2 bg-brand text-white rounded-lg hover:bg-brand-dark active:bg-brand-dark text-base sm:text-sm touch-manipulation min-h-[52px] sm:min-h-[44px] transition-all">
                {{ selectedPaymentType.name }} hinzufügen
              </button>
            </form>
          </div>

          <!-- Quick Add for Other Methods -->
          <div v-else-if="selectedPaymentType && selectedPaymentType.name !== 'Visa' && selectedPaymentType.name !== 'Maestro'" class="mt-6 pt-6 border-t border-slate-200">
            <div class="bg-slate-50 rounded-lg p-4 mb-4">
              <p class="text-sm text-slate-600 mb-4">
                <span v-if="selectedPaymentType.name === 'PayPal'">
                  Sie werden bei der Bestellung zu PayPal weitergeleitet, um sich anzumelden.
                </span>
                <span v-else-if="selectedPaymentType.name === 'Klarna'">
                  Sie werden bei der Bestellung zu Klarna weitergeleitet, um die Zahlung abzuschließen.
                </span>
                <span v-else-if="selectedPaymentType.name === 'Giropay'">
                  Sie werden bei der Bestellung zu Giropay weitergeleitet, um die Zahlung abzuschließen.
                </span>
                <span v-else>
                  Diese Zahlungsmethode wird bei der Bestellung verfügbar sein.
                </span>
              </p>
            </div>
            <button
              @click="addPaymentMethod"
              class="px-4 py-2 bg-brand text-white rounded-lg hover:bg-brand-dark"
            >
              {{ selectedPaymentType.name }} als Zahlungsmethode speichern
            </button>
          </div>
        </div>

        <!-- Saved Payment Methods -->
        <div class="bg-white rounded-lg border border-slate-200 p-6">
          <h2 class="text-lg font-semibold text-slate-900 mb-4">Gespeicherte Zahlungsmethoden</h2>
          <div v-if="paymentMethods.length === 0" class="text-center py-8 text-slate-500">
            <CreditCard class="h-12 w-12 mx-auto mb-2 text-slate-300" />
            <p>Keine Zahlungsmethoden gespeichert</p>
          </div>
          <div v-else class="space-y-4">
            <div
              v-for="method in paymentMethods"
              :key="method.id"
              class="flex items-center justify-between p-4 border border-slate-200 rounded-lg"
            >
              <div class="flex items-center gap-4">
                <div class="h-12 w-20 bg-slate-50 rounded-lg flex items-center justify-center p-2">
                  <img
                    v-if="method.image"
                    :src="method.image"
                    :alt="method.type"
                    class="h-8 w-auto max-w-[70px] object-contain"
                    @error="handleImageError($event, method)"
                    loading="lazy"
                  />
                  <CreditCard v-else class="h-6 w-6 text-slate-400" />
                </div>
                <div>
                  <p class="font-medium text-slate-900">
                    {{ method.type }}
                    <span v-if="method.last4"> •••• {{ method.last4 }}</span>
                  </p>
                  <p v-if="method.expiry" class="text-sm text-slate-600">Ablaufdatum: {{ method.expiry }}</p>
                  <p v-else class="text-sm text-slate-600">Gespeichert</p>
                </div>
              </div>
              <div class="flex gap-2">
                <button
                  v-if="method.isDefault"
                  class="px-3 py-1 text-xs font-medium bg-brand/10 text-brand rounded"
                >
                  Standard
                </button>
                <button
                  @click="setAsDefault(method.id)"
                  v-else
                  class="px-3 py-1 text-xs font-medium border border-slate-200 rounded hover:bg-slate-50"
                >
                  Als Standard setzen
                </button>
                <button
                  @click="removePaymentMethod(method.id)"
                  class="px-3 py-1 text-xs font-medium text-red-600 hover:bg-red-50 rounded"
                >
                  Entfernen
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { CreditCard } from 'lucide-vue-next'
import Breadcrumb from '@theme-classic/component/Breadcrumb.vue'
import TextField from '@theme-classic/component/ui/TextField.vue'
import { useNotificationStore } from '@theme-classic/stores/notificationStore'
import { usePaymentStore } from '@theme-classic/stores/paymentStore'
import { paymentMethods as availablePaymentMethodsConfig, getPaymentMethod } from '@theme-classic/config/paymentShipping'

const notificationStore = useNotificationStore()
const paymentStore = usePaymentStore()

const breadcrumbItems = [
  { label: 'Startseite', to: '/' },
  { label: 'Mein Konto', to: '/konto' },
  { label: 'Zahlungsmethoden', to: null }
]

const availablePaymentMethods = computed(() => availablePaymentMethodsConfig)

const selectedPaymentType = ref(null)

const newPayment = ref({
  cardNumber: '',
  cardholder: '',
  expiry: '',
  cvv: ''
})

const paymentMethods = computed(() => paymentStore.paymentMethods)

onMounted(() => {
  // Load payment methods from store if available
})

const selectPaymentType = (method) => {
  selectedPaymentType.value = method
  // Reset form when selecting different type
  if (method.name !== 'Visa' && method.name !== 'Maestro') {
    newPayment.value = { cardNumber: '', cardholder: '', expiry: '', cvv: '' }
  }
}

const formatCardNumber = (event) => {
  let value = event.target.value.replace(/\s/g, '')
  let formatted = value.match(/.{1,4}/g)?.join(' ') || value
  newPayment.value.cardNumber = formatted
}

const formatExpiry = (event) => {
  let value = event.target.value.replace(/\D/g, '')
  if (value.length >= 2) {
    value = value.substring(0, 2) + '/' + value.substring(2, 4)
  }
  newPayment.value.expiry = value
}

const addPaymentMethod = () => {
  if (!selectedPaymentType.value) {
    notificationStore.error('Bitte wählen Sie zuerst eine Zahlungsmethode aus', 'Fehler')
    return
  }

  // For credit cards, validate fields
  if ((selectedPaymentType.value.name === 'Visa' || selectedPaymentType.value.name === 'Maestro')) {
    if (!newPayment.value.cardNumber || !newPayment.value.cardholder || !newPayment.value.expiry || !newPayment.value.cvv) {
      notificationStore.error('Bitte füllen Sie alle Felder aus', 'Fehler')
      return
    }

    // Validate card number (basic)
    const cardNumber = newPayment.value.cardNumber.replace(/\s/g, '')
    if (cardNumber.length < 13 || cardNumber.length > 19) {
      notificationStore.error('Ungültige Kartennummer', 'Fehler')
      return
    }
  }

  const paymentMethod = {
    id: Date.now(),
    type: selectedPaymentType.value.name,
    image: selectedPaymentType.value.image,
    last4: (selectedPaymentType.value.name === 'Visa' || selectedPaymentType.value.name === 'Maestro')
      ? newPayment.value.cardNumber.replace(/\s/g, '').slice(-4)
      : null,
    expiry: (selectedPaymentType.value.name === 'Visa' || selectedPaymentType.value.name === 'Maestro')
      ? newPayment.value.expiry
      : null,
    isDefault: paymentMethods.value.length === 0
  }

  paymentStore.addPaymentMethod(paymentMethod)

  // Reset form
  selectedPaymentType.value = null
  newPayment.value = { cardNumber: '', cardholder: '', expiry: '', cvv: '' }
  notificationStore.success(`${paymentMethod.type} wurde als Zahlungsmethode hinzugefügt`, 'Erfolgreich')
}

const setAsDefault = (id) => {
  paymentStore.setDefault(id)
  notificationStore.success('Standard-Zahlungsmethode wurde aktualisiert', 'Erfolgreich')
}

const removePaymentMethod = (id) => {
  if (confirm('Möchten Sie diese Zahlungsmethode wirklich entfernen?')) {
    paymentStore.removePaymentMethod(id)
    notificationStore.success('Zahlungsmethode wurde entfernt', 'Erfolgreich')
  }
}

const handleImageError = (event, item) => {
  event.target.style.display = 'none'
}
</script>
