import { defineStore } from 'pinia'

export const usePaymentStore = defineStore('payments', {
  state: () => ({
    paymentMethods: [
      {
        id: 1,
        type: 'Visa',
        last4: '1234',
        expiry: '12/25',
        isDefault: true,
        image: '/images/payments/visa.png'
      }
    ]
  }),
  
  getters: {
    defaultPaymentMethod() {
      return this.paymentMethods.find(m => m.isDefault) || this.paymentMethods[0]
    }
  },
  
  actions: {
    addPaymentMethod(method) {
      const newMethod = {
        id: Date.now(),
        ...method,
        isDefault: this.paymentMethods.length === 0
      }
      this.paymentMethods.push(newMethod)
      return newMethod
    },
    
    removePaymentMethod(id) {
      this.paymentMethods = this.paymentMethods.filter(m => m.id !== id)
      if (this.paymentMethods.length > 0 && !this.paymentMethods.some(m => m.isDefault)) {
        this.paymentMethods[0].isDefault = true
      }
    },
    
    setDefault(id) {
      this.paymentMethods.forEach(m => m.isDefault = m.id === id)
    }
  },
  
  persist: {
    key: 'payment-storage'
  }
})
