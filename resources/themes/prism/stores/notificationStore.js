import { defineStore } from 'pinia'
import { ref } from 'vue'

export const useNotificationStore = defineStore('notifications', {
  state: () => ({
    notifications: [],
    unreadCount: 0
  }),
  
  actions: {
    addNotification(notification) {
      const id = Date.now()
      const newNotification = {
        id,
        type: notification.type || 'info', // success, error, warning, info
        title: notification.title || '',
        message: notification.message || '',
        duration: notification.duration || 5000,
        read: false,
        createdAt: new Date().toISOString()
      }
      
      this.notifications.unshift(newNotification)
      if (!newNotification.read) {
        this.unreadCount++
      }
      
      // Auto remove after duration
      if (newNotification.duration > 0) {
        setTimeout(() => {
          this.removeNotification(id)
        }, newNotification.duration)
      }
      
      return id
    },
    
    removeNotification(id) {
      const index = this.notifications.findIndex(n => n.id === id)
      if (index > -1) {
        const notification = this.notifications[index]
        if (!notification.read) {
          this.unreadCount--
        }
        this.notifications.splice(index, 1)
      }
    },
    
    markAsRead(id) {
      const notification = this.notifications.find(n => n.id === id)
      if (notification && !notification.read) {
        notification.read = true
        this.unreadCount--
      }
    },
    
    markAllAsRead() {
      this.notifications.forEach(n => {
        if (!n.read) {
          n.read = true
        }
      })
      this.unreadCount = 0
    },
    
    clearAll() {
      this.notifications = []
      this.unreadCount = 0
    },
    
    // Helper methods for common notifications
    success(message, title = 'Erfolgreich') {
      return this.addNotification({ type: 'success', title, message })
    },
    
    error(message, title = 'Fehler') {
      return this.addNotification({ type: 'error', title, message })
    },
    
    warning(message, title = 'Warnung') {
      return this.addNotification({ type: 'warning', title, message })
    },
    
    info(message, title = 'Information') {
      return this.addNotification({ type: 'info', title, message })
    }
  }
})
