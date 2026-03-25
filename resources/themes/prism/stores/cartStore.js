import { defineStore } from 'pinia'

const GUEST_TOKEN_KEY = 'cart_guest_token'

function getOrCreateGuestToken() {
  let token = localStorage.getItem(GUEST_TOKEN_KEY)
  if (!token) {
    token = 'guest_' + crypto.randomUUID?.()?.replace(/-/g, '') || Date.now().toString(36) + Math.random().toString(36).slice(2)
    localStorage.setItem(GUEST_TOKEN_KEY, token)
  }
  return token
}

export const useCartStore = defineStore('cart', {
  state: () => ({
    items: []
  }),

  getters: {
    totalPrice() {
      return this.items.reduce(
        (total, item) => total + item.price * item.quantity,
        0
      )
    },

    totalItems() {
      return this.items.reduce((total, item) => total + item.quantity, 0)
    },
    guestToken() {
      return getOrCreateGuestToken()
    }
  },

  actions: {
    getGuestToken() {
      return getOrCreateGuestToken()
    },

    setItems(apiItems) {
      this.items = (apiItems || []).map((it) => ({
        id: it.id,
        title: it.title,
        articleNumber: it.articleNumber || it.product_sku,
        price: Number(it.price),
        oldPrice: it.oldPrice ?? null,
        quantity: it.quantity || 1,
        imageUrl: it.imageUrl || null
      }))
    },

    addItem(product, quantity = 1) {
      const existingItem = this.items.find((item) => item.id === product.id)

      if (existingItem) {
        existingItem.quantity += quantity
      } else {
        this.items.push({ ...product, quantity })
      }
    },

    removeItem(productId) {
      this.items = this.items.filter((item) => item.id !== productId)
    },

    updateQuantity(productId, quantity) {
      if (quantity <= 0) {
        this.removeItem(productId)
        return
      }
      const item = this.items.find((item) => item.id === productId)
      if (item) {
        item.quantity = quantity
      }
    },

    clearCart() {
      this.items = []
    }
  },

  persist: {
    key: 'cart-storage'
  }
})

