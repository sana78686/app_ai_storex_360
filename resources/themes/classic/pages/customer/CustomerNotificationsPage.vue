<template>
  <div>
    <Breadcrumb :items="breadcrumbItems" />
    <div class="mx-auto max-w-4xl px-4 py-8">
      <div class="mb-6 flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-bold text-slate-900">Benachrichtigungen</h1>
          <p class="mt-2 text-sm text-slate-600">Verwalten Sie Ihre Benachrichtigungen</p>
        </div>
        <button
          v-if="notificationStore.unreadCount > 0"
          @click="markAllAsRead"
          class="px-4 py-2 text-sm font-medium text-brand hover:bg-brand/10 rounded-lg"
        >
          Alle als gelesen markieren
        </button>
      </div>

      <div class="space-y-4">
        <div v-if="notificationStore.notifications.length === 0" class="text-center py-12 bg-white rounded-lg border border-slate-200">
          <Bell class="h-16 w-16 mx-auto mb-4 text-slate-300" />
          <p class="text-slate-600">Keine Benachrichtigungen</p>
        </div>

        <div
          v-for="notification in notificationStore.notifications"
          :key="notification.id"
          :class="[
            'bg-white rounded-lg border p-4 cursor-pointer transition-all',
            notification.read ? 'border-slate-200' : 'border-brand/50 bg-brand/5'
          ]"
          @click="markAsRead(notification.id)"
        >
          <div class="flex items-start gap-4">
            <div :class="getIconClass(notification.type)">
              <component :is="getIcon(notification.type)" class="h-5 w-5" />
            </div>
            <div class="flex-1">
              <div class="flex items-start justify-between">
                <div>
                  <p class="font-semibold text-slate-900">{{ notification.title }}</p>
                  <p class="text-sm text-slate-600 mt-1">{{ notification.message }}</p>
                  <p class="text-xs text-slate-500 mt-2">{{ formatDate(notification.createdAt) }}</p>
                </div>
                <button
                  @click.stop="removeNotification(notification.id)"
                  class="text-slate-400 hover:text-slate-600"
                >
                  <X class="h-4 w-4" />
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
import { computed } from 'vue'
import { Bell, X, CheckCircle2, XCircle, AlertTriangle, Info } from 'lucide-vue-next'
import Breadcrumb from '@theme-classic/component/Breadcrumb.vue'
import { useNotificationStore } from '@theme-classic/stores/notificationStore'

const notificationStore = useNotificationStore()

const breadcrumbItems = computed(() => [
  { label: 'Startseite', to: '/' },
  { label: 'Mein Konto', to: '/konto' },
  { label: 'Benachrichtigungen', to: null }
])

const getIcon = (type) => {
  const icons = {
    success: CheckCircle2,
    error: XCircle,
    warning: AlertTriangle,
    info: Info
  }
  return icons[type] || Info
}

const getIconClass = (type) => {
  const classes = {
    success: 'h-10 w-10 rounded-full bg-green-100 flex items-center justify-center text-green-600',
    error: 'h-10 w-10 rounded-full bg-red-100 flex items-center justify-center text-red-600',
    warning: 'h-10 w-10 rounded-full bg-yellow-100 flex items-center justify-center text-yellow-600',
    info: 'h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600'
  }
  return classes[type] || classes.info
}

const markAsRead = (id) => {
  notificationStore.markAsRead(id)
}

const markAllAsRead = () => {
  notificationStore.markAllAsRead()
}

const removeNotification = (id) => {
  notificationStore.removeNotification(id)
}

const formatDate = (dateString) => {
  const date = new Date(dateString)
  const now = new Date()
  const diff = now - date
  const minutes = Math.floor(diff / 60000)
  const hours = Math.floor(minutes / 60)
  const days = Math.floor(hours / 24)

  if (minutes < 1) return 'Gerade eben'
  if (minutes < 60) return `vor ${minutes} Minuten`
  if (hours < 24) return `vor ${hours} Stunden`
  if (days < 7) return `vor ${days} Tagen`
  return date.toLocaleDateString('de-DE')
}
</script>
