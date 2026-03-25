import { defineStore } from 'pinia'

export const useNotificationPreferencesStore = defineStore('notificationPreferences', {
  state: () => ({
    preferences: {
      email: {
        enabled: true,
        orderUpdates: true,
        shippingUpdates: true,
        deliveryConfirmations: true,
        returnUpdates: true,
        refundUpdates: true,
        reviewReminders: true,
        promotions: false,
        newsletter: false
      },
      sms: {
        enabled: false,
        orderUpdates: false,
        shippingUpdates: true,
        deliveryConfirmations: true,
        returnUpdates: false,
        refundUpdates: false
      },
      push: {
        enabled: true,
        orderUpdates: true,
        shippingUpdates: true,
        deliveryConfirmations: true
      }
    },
    email: '',
    phone: ''
  }),

  getters: {
    isEmailEnabled: (state) => state.preferences.email.enabled,
    isSmsEnabled: (state) => state.preferences.sms.enabled
  },

  actions: {
    updateEmailPreference(key, value) {
      if (this.preferences.email.hasOwnProperty(key)) {
        this.preferences.email[key] = value
      }
    },

    updateSmsPreference(key, value) {
      if (this.preferences.sms.hasOwnProperty(key)) {
        this.preferences.sms[key] = value
      }
    },

    updatePushPreference(key, value) {
      if (this.preferences.push.hasOwnProperty(key)) {
        this.preferences.push[key] = value
      }
    },

    setEmail(email) {
      this.email = email
    },

    setPhone(phone) {
      this.phone = phone
    },

    // Send notification based on preferences
    sendNotification(type, title, message, data = {}) {
      const notifications = []

      // Email notifications
      if (this.preferences.email.enabled && this.preferences.email[type]) {
        notifications.push({
          channel: 'email',
          to: this.email,
          subject: title,
          body: message,
          data
        })
      }

      // SMS notifications
      if (this.preferences.sms.enabled && this.preferences.sms[type] && this.phone) {
        notifications.push({
          channel: 'sms',
          to: this.phone,
          message: `${title}: ${message}`,
          data
        })
      }

      // In production, this would call an API
      console.log('Notifications to send:', notifications)
      return notifications
    },

    sendOrderUpdate(orderId, status, message) {
      return this.sendNotification('orderUpdates', 'Bestellstatus aktualisiert', message, { orderId, status })
    },

    sendShippingUpdate(orderId, trackingNumber, message) {
      return this.sendNotification('shippingUpdates', 'Versandaktualisierung', message, { orderId, trackingNumber })
    },

    sendDeliveryConfirmation(orderId, message) {
      return this.sendNotification('deliveryConfirmations', 'Zustellung bestätigt', message, { orderId })
    },

    sendReturnUpdate(returnId, status, message) {
      return this.sendNotification('returnUpdates', 'Retouren-Update', message, { returnId, status })
    },

    sendRefundUpdate(refundId, amount, message) {
      return this.sendNotification('refundUpdates', 'Rückerstattung', message, { refundId, amount })
    }
  },

  persist: {
    key: 'notification-preferences-storage'
  }
})
