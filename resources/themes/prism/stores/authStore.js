import { defineStore } from 'pinia'
import axiosTenant from '@theme-prism/axios'

const TOKEN_KEY = 'tenant_token'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    token: null,
    isAuthenticated: false
  }),

  getters: {
    isSeller() {
      return this.user?.role === 'seller' || this.user?.email?.includes('seller')
    },
    isAdmin() {
      return this.user?.role === 'admin'
    }
  },

  actions: {
    setToken(token) {
      this.token = token
      if (token) {
        localStorage.setItem(TOKEN_KEY, token)
      } else {
        localStorage.removeItem(TOKEN_KEY)
      }
    },

    syncTokenToStorage() {
      if (this.token) {
        localStorage.setItem(TOKEN_KEY, this.token)
      }
    },

    async login(email, password) {
      try {
        const { data } = await axiosTenant.post('/customer/login', { email, password })
        const customer = data.customer
        const token = data.token
        this.user = {
          id: customer.id,
          email: customer.email,
          name: customer.name || [customer.first_name, customer.last_name].filter(Boolean).join(' ') || customer.email
        }
        this.setToken(token)
        this.isAuthenticated = true

        if (this.isSeller) {
          const { useSellerStore } = await import('./sellerStore')
          const sellerStore = useSellerStore()
          await sellerStore.initializeSeller(this.user.id)
        }

        return { success: true }
      } catch (err) {
        const msg = err.response?.data?.message || err.response?.data?.errors?.email?.[0]
        return { success: false, error: msg || 'Ungültige E-Mail oder Passwort' }
      }
    },

    async register(email, password, name) {
      try {
        const { data } = await axiosTenant.post('/customer/register', {
          email,
          password,
          password_confirmation: password,
          name
        })
        const customer = data.customer
        const token = data.token
        this.user = {
          id: customer.id,
          email: customer.email,
          name: customer.name || [customer.first_name, customer.last_name].filter(Boolean).join(' ') || customer.email
        }
        this.setToken(token)
        this.isAuthenticated = true
        return { success: true }
      } catch (err) {
        const errors = err.response?.data?.errors
        const msg = errors?.email?.[0] || err.response?.data?.message || 'Diese E-Mail ist bereits registriert'
        return { success: false, error: msg }
      }
    },

    logout() {
      this.user = null
      this.token = null
      this.isAuthenticated = false
      this.setToken(null)
    }
  },

  persist: {
    key: 'auth-storage',
    afterRestore: (ctx) => {
      if (ctx.store.token) {
        localStorage.setItem(TOKEN_KEY, ctx.store.token)
      }
    }
  }
})

