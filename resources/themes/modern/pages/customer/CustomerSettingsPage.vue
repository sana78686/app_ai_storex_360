<template>
  <div>
    <Breadcrumb :items="breadcrumbItems" />
    <div class="mx-auto max-w-4xl px-3 sm:px-4 py-6 sm:py-8">
      <div class="mb-4 sm:mb-6">
        <h1 class="text-xl sm:text-2xl font-bold text-slate-900">Einstellungen</h1>
        <p class="mt-2 text-xs sm:text-sm text-slate-600">Verwalten Sie Ihre Kontoeinstellungen</p>
      </div>

      <div class="space-y-4 sm:space-y-6">
        <!-- Profile Settings -->
        <div class="bg-white rounded-lg border border-slate-200 p-4 sm:p-6">
          <h2 class="text-lg font-semibold text-slate-900 mb-4">Profilinformationen</h2>
          <form @submit.prevent="updateProfile" class="space-y-4">
            <div class="grid gap-4 sm:grid-cols-2">
              <TextField
                v-model="profile.firstName"
                label="Vorname"
                :default-value="authStore.user?.name?.split(' ')[0] || ''"
              />
              <TextField
                v-model="profile.lastName"
                label="Nachname"
                :default-value="authStore.user?.name?.split(' ')[1] || ''"
              />
              <TextField
                v-model="profile.email"
                label="E-Mail"
                type="email"
                :default-value="authStore.user?.email || ''"
                class="sm:col-span-2"
              />
              <TextField
                v-model="profile.phone"
                label="Telefonnummer"
                type="tel"
              />
            </div>
            <button type="submit" class="w-full sm:w-auto px-6 py-3 sm:py-2 bg-brand text-white rounded-lg hover:bg-brand-dark active:bg-brand-dark text-base sm:text-sm touch-manipulation min-h-[52px] sm:min-h-[44px] transition-all">
              Änderungen speichern
            </button>
          </form>
        </div>

        <!-- Password Change -->
        <div class="bg-white rounded-lg border border-slate-200 p-6">
          <h2 class="text-lg font-semibold text-slate-900 mb-4">Passwort ändern</h2>
          <form @submit.prevent="changePassword" class="space-y-4">
            <TextField
              v-model="password.current"
              label="Aktuelles Passwort"
              type="password"
            />
            <TextField
              v-model="password.new"
              label="Neues Passwort"
              type="password"
            />
            <TextField
              v-model="password.confirm"
              label="Neues Passwort bestätigen"
              type="password"
            />
            <button type="submit" class="px-4 py-2 bg-brand text-white rounded-lg hover:bg-brand-dark">
              Passwort ändern
            </button>
          </form>
        </div>

        <!-- Notification Preferences -->
        <div class="bg-white rounded-lg border border-slate-200 p-6">
          <h2 class="text-lg font-semibold text-slate-900 mb-4">Benachrichtigungseinstellungen</h2>
          <Tabs
            :tabs="notificationTabs"
            variant="top"
            :default-tab="0"
          >
            <template #panel="{ tab, index }">
              <!-- Email Preferences -->
              <div v-if="index === 0" class="space-y-4 pt-4">
                <label class="flex items-center justify-between">
                  <div>
                    <p class="font-medium text-slate-900">E-Mail-Benachrichtigungen aktivieren</p>
                    <p class="text-sm text-slate-600">E-Mail: {{ notificationPrefsStore.email || authStore.user?.email }}</p>
                  </div>
                  <input
                    type="checkbox"
                    :checked="notificationPrefsStore.preferences.email.enabled"
                    @change="notificationPrefsStore.updateEmailPreference('enabled', $event.target.checked)"
                    class="rounded"
                  />
                </label>
                <div v-if="notificationPrefsStore.preferences.email.enabled" class="ml-4 space-y-3 border-l-2 border-slate-200 pl-4">
                  <label class="flex items-center justify-between">
                    <span class="text-sm text-slate-700">Bestellaktualisierungen</span>
                    <input
                      type="checkbox"
                      :checked="notificationPrefsStore.preferences.email.orderUpdates"
                      @change="notificationPrefsStore.updateEmailPreference('orderUpdates', $event.target.checked)"
                      class="rounded"
                    />
                  </label>
                  <label class="flex items-center justify-between">
                    <span class="text-sm text-slate-700">Versandaktualisierungen</span>
                    <input
                      type="checkbox"
                      :checked="notificationPrefsStore.preferences.email.shippingUpdates"
                      @change="notificationPrefsStore.updateEmailPreference('shippingUpdates', $event.target.checked)"
                      class="rounded"
                    />
                  </label>
                  <label class="flex items-center justify-between">
                    <span class="text-sm text-slate-700">Zustellbestätigungen</span>
                    <input
                      type="checkbox"
                      :checked="notificationPrefsStore.preferences.email.deliveryConfirmations"
                      @change="notificationPrefsStore.updateEmailPreference('deliveryConfirmations', $event.target.checked)"
                      class="rounded"
                    />
                  </label>
                  <label class="flex items-center justify-between">
                    <span class="text-sm text-slate-700">Retouren-Updates</span>
                    <input
                      type="checkbox"
                      :checked="notificationPrefsStore.preferences.email.returnUpdates"
                      @change="notificationPrefsStore.updateEmailPreference('returnUpdates', $event.target.checked)"
                      class="rounded"
                    />
                  </label>
                  <label class="flex items-center justify-between">
                    <span class="text-sm text-slate-700">Rückerstattungs-Updates</span>
                    <input
                      type="checkbox"
                      :checked="notificationPrefsStore.preferences.email.refundUpdates"
                      @change="notificationPrefsStore.updateEmailPreference('refundUpdates', $event.target.checked)"
                      class="rounded"
                    />
                  </label>
                  <label class="flex items-center justify-between">
                    <span class="text-sm text-slate-700">Bewertungserinnerungen</span>
                    <input
                      type="checkbox"
                      :checked="notificationPrefsStore.preferences.email.reviewReminders"
                      @change="notificationPrefsStore.updateEmailPreference('reviewReminders', $event.target.checked)"
                      class="rounded"
                    />
                  </label>
                  <label class="flex items-center justify-between">
                    <span class="text-sm text-slate-700">Aktionen & Angebote</span>
                    <input
                      type="checkbox"
                      :checked="notificationPrefsStore.preferences.email.promotions"
                      @change="notificationPrefsStore.updateEmailPreference('promotions', $event.target.checked)"
                      class="rounded"
                    />
                  </label>
                  <label class="flex items-center justify-between">
                    <span class="text-sm text-slate-700">Newsletter</span>
                    <input
                      type="checkbox"
                      :checked="notificationPrefsStore.preferences.email.newsletter"
                      @change="notificationPrefsStore.updateEmailPreference('newsletter', $event.target.checked)"
                      class="rounded"
                    />
                  </label>
                </div>
              </div>

              <!-- SMS Preferences -->
              <div v-if="index === 1" class="space-y-4 pt-4">
                <label class="flex items-center justify-between">
                  <div>
                    <p class="font-medium text-slate-900">SMS-Benachrichtigungen aktivieren</p>
                    <p class="text-sm text-slate-600">Telefon: {{ notificationPrefsStore.phone || 'Nicht angegeben' }}</p>
                  </div>
                  <input
                    type="checkbox"
                    :checked="notificationPrefsStore.preferences.sms.enabled"
                    @change="notificationPrefsStore.updateSmsPreference('enabled', $event.target.checked)"
                    class="rounded"
                  />
                </label>
                <TextField
                  v-if="!notificationPrefsStore.phone"
                  v-model="phoneInput"
                  label="Telefonnummer für SMS"
                  type="tel"
                  placeholder="+49 123 456789"
                  class="mb-4"
                />
                <button
                  v-if="!notificationPrefsStore.phone && phoneInput"
                  @click="savePhoneNumber"
                  class="mb-4 px-4 py-2 bg-brand text-white rounded-lg hover:bg-brand-dark text-sm"
                >
                  Telefonnummer speichern
                </button>
                <div v-if="notificationPrefsStore.preferences.sms.enabled" class="ml-4 space-y-3 border-l-2 border-slate-200 pl-4">
                  <label class="flex items-center justify-between">
                    <span class="text-sm text-slate-700">Bestellaktualisierungen</span>
                    <input
                      type="checkbox"
                      :checked="notificationPrefsStore.preferences.sms.orderUpdates"
                      @change="notificationPrefsStore.updateSmsPreference('orderUpdates', $event.target.checked)"
                      class="rounded"
                    />
                  </label>
                  <label class="flex items-center justify-between">
                    <span class="text-sm text-slate-700">Versandaktualisierungen</span>
                    <input
                      type="checkbox"
                      :checked="notificationPrefsStore.preferences.sms.shippingUpdates"
                      @change="notificationPrefsStore.updateSmsPreference('shippingUpdates', $event.target.checked)"
                      class="rounded"
                    />
                  </label>
                  <label class="flex items-center justify-between">
                    <span class="text-sm text-slate-700">Zustellbestätigungen</span>
                    <input
                      type="checkbox"
                      :checked="notificationPrefsStore.preferences.sms.deliveryConfirmations"
                      @change="notificationPrefsStore.updateSmsPreference('deliveryConfirmations', $event.target.checked)"
                      class="rounded"
                    />
                  </label>
                </div>
              </div>
            </template>
          </Tabs>
          <button @click="savePreferences" class="mt-4 px-4 py-2 bg-brand text-white rounded-lg hover:bg-brand-dark">
            Einstellungen speichern
          </button>
        </div>

        <!-- Account Deletion -->
        <div class="bg-white rounded-lg border-2 border-red-200 p-6">
          <h2 class="text-lg font-semibold text-red-900 mb-2">Konto löschen</h2>
          <p class="text-sm text-slate-600 mb-4">
            Wenn Sie Ihr Konto löschen, werden alle Ihre Daten unwiderruflich gelöscht. Diese Aktion kann nicht rückgängig gemacht werden.
          </p>
          <button
            @click="showDeleteConfirm = true"
            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700"
          >
            Konto löschen
          </button>
        </div>
      </div>

      <!-- Delete Confirmation Modal -->
      <div
        v-if="showDeleteConfirm"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
        @click="showDeleteConfirm = false"
      >
        <div
          @click.stop
          class="bg-white rounded-lg p-6 max-w-md w-full mx-4"
        >
          <h3 class="text-xl font-bold text-slate-900 mb-2">Konto wirklich löschen?</h3>
          <p class="text-slate-600 mb-6">
            Diese Aktion kann nicht rückgängig gemacht werden. Alle Ihre Daten werden permanent gelöscht.
          </p>
          <div class="mb-4">
            <label class="block text-sm font-medium text-slate-900 mb-2">
              Geben Sie "LÖSCHEN" ein, um zu bestätigen:
            </label>
            <input
              v-model="deleteConfirm"
              type="text"
              class="w-full px-3 py-2 border border-slate-300 rounded-lg"
              placeholder="LÖSCHEN"
            />
          </div>
          <div class="flex gap-3">
            <button
              @click="deleteAccount"
              :disabled="deleteConfirm !== 'LÖSCHEN'"
              class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              Konto löschen
            </button>
            <button
              @click="showDeleteConfirm = false"
              class="flex-1 px-4 py-2 border border-slate-200 rounded-lg hover:bg-slate-50"
            >
              Abbrechen
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@theme-prism/stores/authStore'
import { useNotificationStore } from '@theme-prism/stores/notificationStore'
import { useNotificationPreferencesStore } from '@theme-prism/stores/notificationPreferencesStore'
import Breadcrumb from '@theme-prism/component/Breadcrumb.vue'
import TextField from '@theme-prism/component/ui/TextField.vue'
import Tabs from '@theme-prism/component/ui/Tabs.vue'

const router = useRouter()
const authStore = useAuthStore()
const notificationStore = useNotificationStore()
const notificationPrefsStore = useNotificationPreferencesStore()

const breadcrumbItems = [
  { label: 'Startseite', to: '/' },
  { label: 'Mein Konto', to: '/konto' },
  { label: 'Einstellungen', to: null }
]

const profile = ref({
  firstName: authStore.user?.name?.split(' ')[0] || '',
  lastName: authStore.user?.name?.split(' ')[1] || '',
  email: authStore.user?.email || '',
  phone: ''
})

const password = ref({
  current: '',
  new: '',
  confirm: ''
})

const preferences = ref({
  emailNotifications: true,
  newsletter: false,
  smsNotifications: false
})

onMounted(() => {
  // Load preferences from store
  if (authStore.user?.email) {
    notificationPrefsStore.setEmail(authStore.user.email)
  }
  preferences.value.emailNotifications = notificationPrefsStore.preferences.email.enabled
  preferences.value.smsNotifications = notificationPrefsStore.preferences.sms.enabled
})

const showDeleteConfirm = ref(false)
const deleteConfirm = ref('')
const phoneInput = ref('')

const notificationTabs = [
  { label: 'E-Mail', content: '' },
  { label: 'SMS', content: '' }
]

const savePhoneNumber = () => {
  if (phoneInput.value) {
    notificationPrefsStore.setPhone(phoneInput.value)
    notificationStore.success('Telefonnummer wurde gespeichert', 'Erfolgreich')
    phoneInput.value = ''
  }
}

const updateProfile = () => {
  notificationStore.success('Profil wurde aktualisiert', 'Erfolgreich')
}

const changePassword = () => {
  if (password.value.new !== password.value.confirm) {
    notificationStore.error('Die Passwörter stimmen nicht überein', 'Fehler')
    return
  }
  notificationStore.success('Passwort wurde geändert', 'Erfolgreich')
  password.value = { current: '', new: '', confirm: '' }
}

const savePreferences = () => {
  notificationStore.success('Einstellungen wurden gespeichert', 'Erfolgreich')
}

const deleteAccount = () => {
  if (deleteConfirm.value !== 'LÖSCHEN') {
    notificationStore.error('Bitte geben Sie "LÖSCHEN" ein, um zu bestätigen', 'Fehler')
    return
  }

  // In production, call API to delete account
  authStore.logout()
  notificationStore.success('Ihr Konto wurde gelöscht', 'Konto gelöscht')
  router.push('/')
  showDeleteConfirm.value = false
}
</script>
