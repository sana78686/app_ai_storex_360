<template>
  <div>
    <Breadcrumb :items="breadcrumbItems" />
    <div class="mx-auto max-w-6xl px-4 py-8">
      <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-900">{{ $t('customerAddressPage.title') }}</h1>
        <p class="mt-2 text-sm text-slate-600">{{ $t('customerAddressPage.subtitle') }}</p>
      </div>

      <!-- Tabs Navigation -->
      <div class="border-b border-slate-200 mb-6">
        <nav class="flex space-x-1" aria-label="Tabs">
          <button
            v-for="(tab, index) in tabsData"
            :key="index"
            @click="activeTab = index"
            :class="[
              'whitespace-nowrap px-4 py-3 text-sm font-medium border-b-2 transition-colors',
              activeTab === index
                ? 'border-brand text-brand'
                : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300'
            ]"
          >
            {{ tab.label }}
          </button>
        </nav>
      </div>

      <!-- Tab Content -->
      <div>
        <!-- Delivery Addresses Tab -->
        <div v-if="activeTab === 0" class="space-y-6">
            <!-- Add New Delivery Address Form -->
            <div class="bg-white rounded-lg border border-slate-200 p-4 sm:p-6">
              <h2 class="text-base sm:text-lg font-semibold text-slate-900 mb-2">
                {{ isEditing && editType === 'delivery' ? $t('customerAddressPage.editAddress') : $t('customerAddressPage.addDeliveryAddress') }}
              </h2>
              <p class="text-xs sm:text-sm text-slate-600 mb-4">
                {{ $t('customerAddressPage.saveMultipleHint') }}
              </p>

              <form @submit.prevent="handleSubmitDelivery" class="space-y-3 sm:space-y-4">
                <div class="grid gap-3 sm:gap-4 sm:grid-cols-2">
                  <div>
                    <label class="block text-sm font-medium text-slate-900 mb-1">
                      Vorname <span class="text-red-500">*</span>
                    </label>
                    <input
                      v-model="deliveryForm.firstName"
                      type="text"
                      required
                      class="w-full px-3 sm:px-4 py-2.5 sm:py-2 text-base sm:text-sm border-2 border-slate-300 rounded-lg focus:ring-2 focus:ring-brand focus:border-brand"
                      placeholder="Max"
                    />
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-slate-900 mb-1">Nachname</label>
                    <input
                      v-model="deliveryForm.lastName"
                      type="text"
                      class="w-full px-3 sm:px-4 py-2.5 sm:py-2 text-base sm:text-sm border-2 border-slate-300 rounded-lg focus:ring-2 focus:ring-brand focus:border-brand"
                      placeholder="Mustermann"
                    />
                  </div>
                </div>

                <div>
                  <label class="block text-sm font-medium text-slate-900 mb-1">
                    Straße & Hausnummer <span class="text-red-500">*</span>
                  </label>
                  <input
                    v-model="deliveryForm.street"
                    type="text"
                    required
                    class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-brand focus:border-brand"
                    placeholder="Musterstraße 123"
                  />
                </div>

                <div class="grid gap-4 sm:grid-cols-3">
                  <div>
                    <label class="block text-sm font-medium text-slate-900 mb-1">
                      Postleitzahl <span class="text-red-500">*</span>
                    </label>
                    <input
                      v-model="deliveryForm.postalCode"
                      type="text"
                      required
                      class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-brand focus:border-brand"
                      placeholder="12345"
                    />
                  </div>
                  <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-slate-900 mb-1">
                      Stadt <span class="text-red-500">*</span>
                    </label>
                    <input
                      v-model="deliveryForm.city"
                      type="text"
                      required
                      class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-brand focus:border-brand"
                      placeholder="Berlin"
                    />
                  </div>
                </div>

                <div>
                  <label class="block text-sm font-medium text-slate-900 mb-1">Land</label>
                  <input
                    v-model="deliveryForm.country"
                    type="text"
                    class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-brand focus:border-brand"
                    placeholder="Deutschland"
                  />
                </div>

                <div class="flex gap-3 pt-2">
                  <button
                    type="submit"
                    class="px-6 py-2 bg-brand text-white rounded-lg hover:bg-brand-dark font-medium"
                  >
                    {{ isEditing && editType === 'delivery' ? $t('customerAddressPage.updateAddress') : $t('customerAddressPage.addAddress') }}
                  </button>
                  <button
                    v-if="isEditing && editType === 'delivery'"
                    type="button"
                    @click="cancelEdit"
                    class="px-6 py-2 border border-slate-300 rounded-lg hover:bg-slate-50 font-medium"
                  >
                    {{ $t('customerAddressPage.cancel') }}
                  </button>
                </div>
              </form>
            </div>

            <!-- Delivery Addresses List -->
            <div class="bg-white rounded-lg border border-slate-200 p-6">
              <h2 class="text-lg font-semibold text-slate-900 mb-4">
                {{ $t('customerAddressPage.savedDeliveryCount', { count: deliveryAddresses.length }) }}
              </h2>

              <p v-if="deliveryAddresses.length > 0" class="text-sm text-slate-600 mb-4">
                {{ $t('customerAddressPage.clickToSelect') }}
              </p>

              <div v-if="deliveryAddresses.length === 0" class="text-center py-12 border-2 border-dashed border-slate-200 rounded-lg">
                <MapPin class="h-12 w-12 mx-auto mb-3 text-slate-300" />
                <p class="text-slate-600 font-medium mb-1">{{ $t('customerAddressPage.noAddressesYet') }}</p>
                <p class="text-sm text-slate-500">{{ $t('customerAddressPage.addFirstDelivery') }}</p>
              </div>

              <div v-else class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <div
                  v-for="address in deliveryAddresses"
                  :key="address.id"
                  @click="selectDeliveryAddress(address.id)"
                  class="p-5 border-2 rounded-lg cursor-pointer transition-all duration-200 hover:shadow-lg"
                  :class="selectedDeliveryId === address.id
                    ? 'border-brand bg-brand/10 shadow-md'
                    : address.isDefault
                    ? 'border-blue-400 bg-blue-50/50'
                    : 'border-slate-200 hover:border-slate-300'"
                >
                  <!-- Selection/Default Badge -->
                  <div class="absolute top-3 right-3">
                    <div
                      v-if="selectedDeliveryId === address.id"
                      class="h-7 w-7 rounded-full bg-brand flex items-center justify-center shadow-md"
                    >
                      <Check class="h-5 w-5 text-white" />
                    </div>
                    <span
                      v-else-if="address.isDefault"
                      class="px-2 py-1 bg-blue-600 text-white text-xs font-medium rounded"
                    >
                      {{ $t('customerAddressPage.default') }}
                    </span>
                  </div>

                  <!-- Address Details -->
                  <div class="pr-10 mb-4">
                    <p class="font-bold text-slate-900 mb-2 text-lg">
                      {{ address.firstName }} {{ address.lastName }}
                    </p>
                    <p class="text-sm text-slate-600 mb-1">{{ address.street }}</p>
                    <p class="text-sm text-slate-600 mb-1">
                      {{ address.postalCode }} {{ address.city }}
                    </p>
                    <p class="text-sm text-slate-600">{{ address.country }}</p>
                  </div>

                  <!-- Actions -->
                  <div class="flex flex-wrap gap-2 pt-3 border-t border-slate-200" @click.stop>
                    <button
                      @click.stop="setDefaultDelivery(address.id)"
                      v-if="!address.isDefault"
                      class="flex-1 text-xs sm:text-sm px-3 py-2.5 sm:py-2 border-2 border-slate-300 rounded hover:bg-slate-50 active:bg-slate-100 text-slate-700 font-medium touch-manipulation min-h-[44px]"
                    >
                      {{ $t('customerAddressPage.setAsDefault') }}
                    </button>
                    <button
                      @click.stop="startEditDelivery(address)"
                      class="flex-1 text-xs sm:text-sm px-3 py-2.5 sm:py-2 border-2 border-slate-300 rounded hover:bg-slate-50 active:bg-slate-100 text-slate-700 font-medium touch-manipulation min-h-[44px]"
                    >
                      {{ $t('customerAddressPage.edit') }}
                    </button>
                    <button
                      @click.stop="deleteDeliveryAddress(address.id)"
                      class="flex-1 text-xs sm:text-sm px-3 py-2.5 sm:py-2 border-2 border-red-300 text-red-600 rounded hover:bg-red-50 active:bg-red-100 font-medium touch-manipulation min-h-[44px]"
                    >
                      {{ $t('customerAddressPage.delete') }}
                    </button>
                  </div>
                </div>
              </div>

              <!-- Selected Address Confirmation -->
              <div
                v-if="selectedDeliveryId && deliveryAddresses.find(a => a.id === selectedDeliveryId)"
                class="mt-4 p-4 bg-green-50 border-2 border-green-200 rounded-lg"
              >
                <div class="flex items-center gap-2">
                  <Check class="h-5 w-5 text-green-600" />
                  <p class="text-sm font-medium text-green-900">
                    {{ $t('customerAddressPage.selectedForNextOrder') }}
                  </p>
                </div>
              </div>
            </div>
          </div>

        <!-- Billing Addresses Tab -->
        <div v-else-if="activeTab === 1" class="space-y-6">
            <!-- Use Delivery Address Option -->
            <div class="bg-blue-50 border-2 border-blue-200 rounded-lg p-6">
              <div class="flex items-start gap-4">
                <div class="flex-shrink-0 mt-1">
                  <input
                    type="checkbox"
                    :checked="useDeliveryAddressForBilling"
                    @change="toggleUseDeliveryAddress"
                    class="h-5 w-5 text-brand focus:ring-brand border-slate-300 rounded"
                  />
                </div>
                <div class="flex-1">
                  <label class="text-sm font-semibold text-slate-900 cursor-pointer block mb-2">
                    Rechnungsadresse gleich Lieferadresse verwenden
                  </label>
                  <p class="text-sm text-slate-600">
                    Wenn aktiviert, wird Ihre ausgewählte Lieferadresse auch als Rechnungsadresse verwendet.
                  </p>
                  <div v-if="useDeliveryAddressForBilling && selectedDeliveryId" class="mt-3 p-3 bg-white rounded border border-blue-200">
                    <p class="text-xs font-medium text-slate-700 mb-1">Verwendete Lieferadresse:</p>
                    <p class="text-sm text-slate-900">
                      {{ getSelectedDeliveryAddressText() }}
                    </p>
                  </div>
                  <div v-else-if="useDeliveryAddressForBilling && !selectedDeliveryId" class="mt-3 p-3 bg-yellow-50 rounded border border-yellow-200">
                    <p class="text-sm text-yellow-800">
                      ⚠️ Bitte wählen Sie zuerst eine Lieferadresse aus dem Tab "Lieferadressen"
                    </p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Add New Billing Address Form -->
            <div class="bg-white rounded-lg border border-slate-200 p-6" :class="{ 'opacity-50 pointer-events-none': useDeliveryAddressForBilling }">
              <h2 class="text-lg font-semibold text-slate-900 mb-2">
                {{ isEditing && editType === 'billing' ? 'Adresse bearbeiten' : 'Neue Rechnungsadresse hinzufügen' }}
              </h2>
              <p v-if="!useDeliveryAddressForBilling" class="text-sm text-slate-600 mb-4">
                Fügen Sie eine separate Rechnungsadresse hinzu, falls diese von der Lieferadresse abweicht.
              </p>

              <form @submit.prevent="handleSubmitBilling" class="space-y-4">
                <div class="grid gap-4 sm:grid-cols-2">
                  <div>
                    <label class="block text-sm font-medium text-slate-900 mb-1">
                      Vorname <span class="text-red-500">*</span>
                    </label>
                    <input
                      v-model="billingForm.firstName"
                      type="text"
                      required
                      class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-brand focus:border-brand"
                    />
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-slate-900 mb-1">Nachname</label>
                    <input
                      v-model="billingForm.lastName"
                      type="text"
                      class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-brand focus:border-brand"
                    />
                  </div>
                </div>

                <div>
                  <label class="block text-sm font-medium text-slate-900 mb-1">
                    Straße & Hausnummer <span class="text-red-500">*</span>
                  </label>
                  <input
                    v-model="billingForm.street"
                    type="text"
                    required
                    class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-brand focus:border-brand"
                  />
                </div>

                <div class="grid gap-4 sm:grid-cols-3">
                  <div>
                    <label class="block text-sm font-medium text-slate-900 mb-1">
                      Postleitzahl <span class="text-red-500">*</span>
                    </label>
                    <input
                      v-model="billingForm.postalCode"
                      type="text"
                      required
                      class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-brand focus:border-brand"
                    />
                  </div>
                  <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-slate-900 mb-1">
                      Stadt <span class="text-red-500">*</span>
                    </label>
                    <input
                      v-model="billingForm.city"
                      type="text"
                      required
                      class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-brand focus:border-brand"
                    />
                  </div>
                </div>

                <div>
                  <label class="block text-sm font-medium text-slate-900 mb-1">Land</label>
                  <input
                    v-model="billingForm.country"
                    type="text"
                    class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-brand focus:border-brand"
                    placeholder="Deutschland"
                  />
                </div>

                <div class="flex gap-3 pt-2">
                  <button
                    type="submit"
                    class="px-6 py-2 bg-brand text-white rounded-lg hover:bg-brand-dark font-medium"
                  >
                    {{ isEditing && editType === 'billing' ? 'Adresse aktualisieren' : 'Adresse hinzufügen' }}
                  </button>
                  <button
                    v-if="isEditing && editType === 'billing'"
                    type="button"
                    @click="cancelEdit"
                    class="px-6 py-2 border border-slate-300 rounded-lg hover:bg-slate-50 font-medium"
                  >
                    Abbrechen
                  </button>
                </div>
              </form>
            </div>

            <!-- Billing Addresses List -->
            <div class="bg-white rounded-lg border border-slate-200 p-6" :class="{ 'opacity-50 pointer-events-none': useDeliveryAddressForBilling }">
              <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-slate-900">
                  Gespeicherte Rechnungsadressen ({{ billingAddresses.length }})
                </h2>
                <span v-if="useDeliveryAddressForBilling" class="text-xs px-2 py-1 bg-blue-100 text-blue-700 rounded">
                  Lieferadresse wird verwendet
                </span>
              </div>

              <div v-if="useDeliveryAddressForBilling" class="text-center py-8 border-2 border-dashed border-blue-200 bg-blue-50 rounded-lg">
                <Check class="h-10 w-10 mx-auto mb-3 text-blue-600" />
                <p class="text-blue-900 font-medium mb-1">Lieferadresse wird verwendet</p>
                <p class="text-sm text-blue-700">Die ausgewählte Lieferadresse wird als Rechnungsadresse verwendet</p>
              </div>

              <div v-else-if="billingAddresses.length === 0" class="text-center py-12 border-2 border-dashed border-slate-200 rounded-lg">
                <MapPin class="h-12 w-12 mx-auto mb-3 text-slate-300" />
                <p class="text-slate-600 font-medium mb-1">Noch keine Adressen gespeichert</p>
                <p class="text-sm text-slate-500">Fügen Sie oben Ihre erste Rechnungsadresse hinzu oder aktivieren Sie die Option, die Lieferadresse zu verwenden</p>
              </div>

              <div v-else-if="billingAddresses.length > 0" class="grid gap-3 sm:gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <div
                  v-for="address in billingAddresses"
                  :key="address.id"
                  @click="selectBillingAddress(address.id)"
                  class="p-4 sm:p-5 border-2 rounded-lg cursor-pointer transition-all duration-200 active:shadow-lg relative touch-manipulation"
                  :class="selectedBillingId === address.id
                    ? 'border-brand bg-brand/10 shadow-md'
                    : address.isDefault
                    ? 'border-blue-400 bg-blue-50/50'
                    : 'border-slate-200 active:border-slate-300'"
                >
                  <div class="absolute top-3 right-3">
                    <div
                      v-if="selectedBillingId === address.id"
                      class="h-7 w-7 rounded-full bg-brand flex items-center justify-center shadow-md"
                    >
                      <Check class="h-5 w-5 text-white" />
                    </div>
                    <span
                      v-else-if="address.isDefault"
                      class="px-2 py-1 bg-blue-600 text-white text-xs font-medium rounded"
                    >
                      Standard
                    </span>
                  </div>

                  <div class="pr-10 mb-4">
                    <p class="font-bold text-slate-900 mb-2 text-lg">
                      {{ address.firstName }} {{ address.lastName }}
                    </p>
                    <p class="text-sm text-slate-600 mb-1">{{ address.street }}</p>
                    <p class="text-sm text-slate-600 mb-1">
                      {{ address.postalCode }} {{ address.city }}
                    </p>
                    <p class="text-sm text-slate-600">{{ address.country }}</p>
                  </div>

                  <div class="flex flex-wrap gap-2 pt-3 border-t border-slate-200" @click.stop>
                    <button
                      @click.stop="setDefaultBilling(address.id)"
                      v-if="!address.isDefault"
                      class="flex-1 text-xs px-3 py-2 border border-slate-300 rounded hover:bg-slate-50 text-slate-700 font-medium"
                    >
                      Als Standard
                    </button>
                    <button
                      @click.stop="startEditBilling(address)"
                      class="flex-1 text-xs px-3 py-2 border border-slate-300 rounded hover:bg-slate-50 text-slate-700 font-medium"
                    >
                      Bearbeiten
                    </button>
                    <button
                      @click.stop="deleteBillingAddress(address.id)"
                      class="flex-1 text-xs px-3 py-2 border border-red-300 text-red-600 rounded hover:bg-red-50 font-medium"
                    >
                      Löschen
                    </button>
                  </div>
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
import { useI18n } from 'vue-i18n'
import { MapPin, Check } from 'lucide-vue-next'
import Breadcrumb from '@theme-prism/component/Breadcrumb.vue'
import { useNotificationStore } from '@theme-prism/stores/notificationStore'
import { useAddressStore } from '@theme-prism/stores/addressStore'

const { t } = useI18n()
const notificationStore = useNotificationStore()
const addressStore = useAddressStore()

const breadcrumbItems = computed(() => [
  { label: t('customerDashboard.breadcrumbHome'), to: '/' },
  { label: t('customerDashboard.breadcrumbAccount'), to: '/konto' },
  { label: t('customerAddressPage.breadcrumbAddresses'), to: null }
])

const tabsData = computed(() => [
  { label: t('customerAddressPage.tabDelivery'), content: '' },
  { label: t('customerAddressPage.tabBilling'), content: '' }
])

// Active tab
const activeTab = ref(0)

// Forms
const deliveryForm = ref({
  firstName: '',
  lastName: '',
  street: '',
  postalCode: '',
  city: '',
  country: 'Deutschland'
})

const billingForm = ref({
  firstName: '',
  lastName: '',
  street: '',
  postalCode: '',
  city: '',
  country: 'Deutschland'
})

// Selection
const selectedDeliveryId = ref(null)
const selectedBillingId = ref(null)

// Use delivery address for billing (Amazon-style)
const useDeliveryAddressForBilling = ref(false)

// Edit state
const isEditing = ref(false)
const editType = ref('') // 'delivery' or 'billing'
const editingId = ref(null)

// Computed
const deliveryAddresses = computed(() => addressStore.deliveryAddresses)
const billingAddresses = computed(() => addressStore.billingAddresses)

// Initialize
onMounted(() => {
  if (addressStore.defaultDeliveryAddress) {
    selectedDeliveryId.value = addressStore.defaultDeliveryAddress.id
  }
  if (addressStore.defaultBillingAddress) {
    selectedBillingId.value = addressStore.defaultBillingAddress.id
  }

  // Check if user previously set to use delivery address
  const saved = localStorage.getItem('use-delivery-for-billing')
  if (saved === 'true') {
    useDeliveryAddressForBilling.value = true
  }
})

// Toggle use delivery address for billing
const toggleUseDeliveryAddress = (event) => {
  useDeliveryAddressForBilling.value = event.target.checked
  localStorage.setItem('use-delivery-for-billing', event.target.checked ? 'true' : 'false')

  if (event.target.checked) {
    notificationStore.info(t('customerAddressPage.deliveryUsedAsBilling'), t('customerAddressPage.settingSaved'))
  } else {
    // Reset to default billing address if available
    if (addressStore.defaultBillingAddress) {
      selectedBillingId.value = addressStore.defaultBillingAddress.id
    }
  }
}

// Get selected delivery address text
const getSelectedDeliveryAddressText = () => {
  if (!selectedDeliveryId.value) return t('customerAddressPage.noDeliverySelected')
  const address = deliveryAddresses.value.find(a => a.id === selectedDeliveryId.value)
  if (!address) return t('customerAddressPage.addressNotFound')
  return `${address.firstName} ${address.lastName}, ${address.street}, ${address.postalCode} ${address.city}`
}

// Get effective billing address (either selected billing or delivery if option is checked)
const getEffectiveBillingAddress = () => {
  if (useDeliveryAddressForBilling.value && selectedDeliveryId.value) {
    return deliveryAddresses.value.find(a => a.id === selectedDeliveryId.value)
  }
  if (selectedBillingId.value) {
    return billingAddresses.value.find(a => a.id === selectedBillingId.value)
  }
  return null
}

// Delivery Address Methods
const handleSubmitDelivery = () => {
  if (!deliveryForm.value.firstName || !deliveryForm.value.street ||
      !deliveryForm.value.postalCode || !deliveryForm.value.city) {
    notificationStore.error(t('customerAddressPage.fillRequired'), t('customerAddressPage.error'))
    return
  }

  if (isEditing.value && editType.value === 'delivery') {
    addressStore.updateDeliveryAddress(editingId.value, deliveryForm.value)
    selectedDeliveryId.value = editingId.value
    notificationStore.success(t('customerAddressPage.addressUpdated'), t('customerAddressPage.success'))
  } else {
    const newAddress = addressStore.addDeliveryAddress(deliveryForm.value)
    selectedDeliveryId.value = newAddress.id
    notificationStore.success(t('customerAddressPage.addressAdded'), t('customerAddressPage.success'))
  }

  resetDeliveryForm()
  cancelEdit()
}

const startEditDelivery = (address) => {
  isEditing.value = true
  editType.value = 'delivery'
  editingId.value = address.id
  deliveryForm.value = {
    firstName: address.firstName,
    lastName: address.lastName || '',
    street: address.street,
    postalCode: address.postalCode,
    city: address.city,
    country: address.country || 'Deutschland'
  }

  // Scroll to form
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

const selectDeliveryAddress = (id) => {
  selectedDeliveryId.value = id

  // If "use delivery for billing" is checked, update billing selection too
  if (useDeliveryAddressForBilling.value) {
    notificationStore.info(t('customerAddressPage.deliverySelectedAsBilling'), t('customerAddressPage.selected'))
  } else {
    notificationStore.info(t('customerAddressPage.deliverySelected'), t('customerAddressPage.selected'))
  }
}

const setDefaultDelivery = (id) => {
  addressStore.setDefaultDelivery(id)
  selectedDeliveryId.value = id
  notificationStore.success(t('customerAddressPage.defaultSet'), t('customerAddressPage.success'))
}

const deleteDeliveryAddress = (id) => {
  if (confirm(t('customerAddressPage.confirmDeleteAddress'))) {
    addressStore.removeDeliveryAddress(id)
    if (selectedDeliveryId.value === id) {
      selectedDeliveryId.value = addressStore.defaultDeliveryAddress?.id || null
    }
    notificationStore.success(t('customerAddressPage.addressDeleted'), t('customerAddressPage.success'))
  }
}

const resetDeliveryForm = () => {
  deliveryForm.value = {
    firstName: '',
    lastName: '',
    street: '',
    postalCode: '',
    city: '',
    country: 'Deutschland'
  }
}

// Billing Address Methods
const handleSubmitBilling = () => {
  if (useDeliveryAddressForBilling.value) {
    notificationStore.warning('Deaktivieren Sie zuerst die Option "Lieferadresse verwenden"', 'Hinweis')
    return
  }

  if (!billingForm.value.firstName || !billingForm.value.street ||
      !billingForm.value.postalCode || !billingForm.value.city) {
    notificationStore.error('Bitte füllen Sie alle Pflichtfelder aus', 'Fehler')
    return
  }

  if (isEditing.value && editType.value === 'billing') {
    addressStore.updateBillingAddress(editingId.value, billingForm.value)
    selectedBillingId.value = editingId.value
    notificationStore.success('Adresse wurde aktualisiert', 'Erfolgreich')
  } else {
    const newAddress = addressStore.addBillingAddress(billingForm.value)
    selectedBillingId.value = newAddress.id
    notificationStore.success('Adresse wurde hinzugefügt und ausgewählt', 'Erfolgreich')
  }

  resetBillingForm()
  cancelEdit()
}

const startEditBilling = (address) => {
  isEditing.value = true
  editType.value = 'billing'
  editingId.value = address.id
  billingForm.value = {
    firstName: address.firstName,
    lastName: address.lastName || '',
    street: address.street,
    postalCode: address.postalCode,
    city: address.city,
    country: address.country || 'Deutschland'
  }

  window.scrollTo({ top: 0, behavior: 'smooth' })
}

const selectBillingAddress = (id) => {
  if (useDeliveryAddressForBilling.value) {
    notificationStore.warning('Deaktivieren Sie zuerst die Option "Lieferadresse verwenden"', 'Hinweis')
    return
  }
  selectedBillingId.value = id
  notificationStore.info('Rechnungsadresse ausgewählt', 'Ausgewählt')
}

const setDefaultBilling = (id) => {
  addressStore.setDefaultBilling(id)
  selectedBillingId.value = id
  notificationStore.success('Standard-Adresse gesetzt und ausgewählt', 'Erfolgreich')
}

const deleteBillingAddress = (id) => {
  if (confirm('Möchten Sie diese Adresse wirklich löschen?')) {
    addressStore.removeBillingAddress(id)
    if (selectedBillingId.value === id) {
      selectedBillingId.value = addressStore.defaultBillingAddress?.id || null
    }
    notificationStore.success('Adresse wurde gelöscht', 'Erfolgreich')
  }
}

const resetBillingForm = () => {
  billingForm.value = {
    firstName: '',
    lastName: '',
    street: '',
    postalCode: '',
    city: '',
    country: 'Deutschland'
  }
}

const cancelEdit = () => {
  isEditing.value = false
  editType.value = ''
  editingId.value = null
  resetDeliveryForm()
  resetBillingForm()
}
</script>
