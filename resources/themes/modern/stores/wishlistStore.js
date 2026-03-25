import { defineStore } from 'pinia'

export const useWishlistStore = defineStore('wishlist', {
  state: () => ({
    items: []
  }),
  
  getters: {
    totalItems() {
      return this.items.length
    },
    
    isInWishlist() {
      return (productId) => {
        return this.items.some(item => item.id === productId)
      }
    }
  },
  
  actions: {
    addItem(product) {
      const existingItem = this.items.find((item) => item.id === product.id)
      
      if (!existingItem) {
        this.items.push({ ...product })
      }
    },
    
    removeItem(productId) {
      this.items = this.items.filter((item) => item.id !== productId)
    },
    
    toggleItem(product) {
      const existingItem = this.items.find((item) => item.id === product.id)
      
      if (existingItem) {
        this.removeItem(product.id)
      } else {
        this.addItem(product)
      }
    },
    
    clearWishlist() {
      this.items = []
    }
  },
  
  persist: {
    key: 'wishlist-storage'
  }
})

