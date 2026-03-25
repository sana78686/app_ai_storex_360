import { defineStore } from 'pinia'

// Simple in-memory storage for demo (in production, use a real backend)
const users = []

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
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
    async login(email, password) {
      // Simulate API call
      await new Promise((resolve) => setTimeout(resolve, 500))
      
      const user = users.find(
        (u) => u.email === email && u.password === password
      )
      
      if (user) {
        this.user = {
          id: user.id,
          email: user.email,
          name: user.name,
          role: user.role || (email.includes('seller') ? 'seller' : 'customer'),
          createdAt: user.createdAt
        }
        this.isAuthenticated = true
        
        // Initialize seller store if user is a seller
        if (this.isSeller) {
          const { useSellerStore } = await import('./sellerStore')
          const sellerStore = useSellerStore()
          await sellerStore.initializeSeller(this.user.id)
        }
        
        return { success: true }
      }
      
      return { success: false, error: 'Ungültige E-Mail oder Passwort' }
    },
    
    async register(email, password, name) {
      // Simulate API call
      await new Promise((resolve) => setTimeout(resolve, 500))
      
      if (users.find((u) => u.email === email)) {
        return { success: false, error: 'Diese E-Mail ist bereits registriert' }
      }
      
      const newUser = {
        id: `user_${Date.now()}`,
        email,
        password, // In production, hash this!
        name,
        createdAt: new Date().toISOString()
      }
      
      users.push(newUser)
      
      this.user = {
        id: newUser.id,
        email: newUser.email,
        name: newUser.name,
        createdAt: newUser.createdAt
      }
      this.isAuthenticated = true
      
      return { success: true }
    },
    
    logout() {
      this.user = null
      this.isAuthenticated = false
    }
  },
  
  persist: {
    key: 'auth-storage'
  }
})

