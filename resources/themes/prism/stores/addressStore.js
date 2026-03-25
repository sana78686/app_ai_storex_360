import { defineStore } from 'pinia'

export const useAddressStore = defineStore('addresses', {
  state: () => ({
    deliveryAddresses: [],
    billingAddresses: []
  }),
  
  getters: {
    defaultDeliveryAddress() {
      return this.deliveryAddresses.find(a => a.isDefault) || this.deliveryAddresses[0] || null
    },
    
    defaultBillingAddress() {
      return this.billingAddresses.find(a => a.isDefault) || this.billingAddresses[0] || null
    }
  },
  
  actions: {
    addDeliveryAddress(address) {
      const newAddress = {
        id: Date.now() + Math.random(),
        firstName: address.firstName || '',
        lastName: address.lastName || '',
        street: address.street || '',
        postalCode: address.postalCode || '',
        city: address.city || '',
        country: address.country || 'Deutschland',
        isDefault: this.deliveryAddresses.length === 0
      }
      this.deliveryAddresses.push(newAddress)
      return newAddress
    },
    
    addBillingAddress(address) {
      const newAddress = {
        id: Date.now() + Math.random(),
        firstName: address.firstName || '',
        lastName: address.lastName || '',
        street: address.street || '',
        postalCode: address.postalCode || '',
        city: address.city || '',
        country: address.country || 'Deutschland',
        isDefault: this.billingAddresses.length === 0
      }
      this.billingAddresses.push(newAddress)
      return newAddress
    },
    
    updateDeliveryAddress(id, updates) {
      const index = this.deliveryAddresses.findIndex(a => a.id === id)
      if (index > -1) {
        this.deliveryAddresses[index] = { ...this.deliveryAddresses[index], ...updates }
      }
    },
    
    updateBillingAddress(id, updates) {
      const index = this.billingAddresses.findIndex(a => a.id === id)
      if (index > -1) {
        this.billingAddresses[index] = { ...this.billingAddresses[index], ...updates }
      }
    },
    
    removeDeliveryAddress(id) {
      this.deliveryAddresses = this.deliveryAddresses.filter(a => a.id !== id)
      if (this.deliveryAddresses.length > 0 && !this.deliveryAddresses.some(a => a.isDefault)) {
        this.deliveryAddresses[0].isDefault = true
      }
    },
    
    removeBillingAddress(id) {
      this.billingAddresses = this.billingAddresses.filter(a => a.id !== id)
      if (this.billingAddresses.length > 0 && !this.billingAddresses.some(a => a.isDefault)) {
        this.billingAddresses[0].isDefault = true
      }
    },
    
    setDefaultDelivery(id) {
      this.deliveryAddresses.forEach(a => {
        a.isDefault = a.id === id
      })
    },
    
    setDefaultBilling(id) {
      this.billingAddresses.forEach(a => {
        a.isDefault = a.id === id
      })
    }
  },
  
  persist: {
    key: 'address-storage',
    storage: localStorage
  }
})
