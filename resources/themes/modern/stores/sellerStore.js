import { defineStore } from 'pinia'

// Demo seller data (in production, this would come from API)
const demoSeller = {
  id: 'seller-1',
  name: 'SimplyShop',
  email: 'seller@einfachshop24.de',
  logo: 'https://images.unsplash.com/photo-1563013544-824ae1b704d3?auto=format&fit=crop&w=100&q=80',
  rating: 98.5,
  itemsSold: '12.3K',
  joinedDate: 'Mai 2013',
  responseTime: '3 Stunden',
  isCommercial: true,
  description: 'Willkommen bei SimplyShop! Wir bieten hochwertige Produkte zu fairen Preisen. Schnelle Lieferung und exzellenter Kundenservice sind unsere Stärken.',
  address: {
    street: 'Musterstraße 123',
    city: 'Herzogenaurach',
    zip: '91074',
    country: 'Deutschland'
  },
  legal: {
    taxId: 'DE123456789',
    tradeRegister: 'HRB 12345',
    eprNumber: 'DE12345678901234'
  },
  ratingBreakdown: {
    detailedDescription: 4.8,
    shippingCosts: 4.7,
    deliveryTime: 4.6,
    communication: 4.9
  },
  stats: {
    totalProducts: 156,
    activeProducts: 142,
    totalOrders: 1234,
    pendingOrders: 12,
    completedOrders: 1180,
    totalRevenue: 45678.90,
    monthlyRevenue: 1234.56,
    averageRating: 4.8,
    totalReviews: 127
  }
}

export const useSellerStore = defineStore('seller', {
  state: () => ({
    currentSeller: null,
    isSeller: false,
    sellerProducts: [],
    sellerOrders: [],
    sellerStats: null
  }),

  getters: {
    activeProductsCount() {
      return this.sellerProducts.filter(p => p.available).length
    },

    pendingOrdersCount() {
      return this.sellerOrders.filter(o => o.status === 'pending').length
    },

    totalRevenue() {
      return this.sellerOrders
        .filter(o => o.status === 'completed')
        .reduce((sum, order) => sum + order.total, 0)
    }
  },

  actions: {
    async initializeSeller(userId) {
      // In production, fetch seller data from API
      await new Promise(resolve => setTimeout(resolve, 300))

      // Check if user is a seller (in production, check from API)
      // For demo, we'll check if user email contains 'seller' or check a flag
      this.currentSeller = demoSeller
      this.isSeller = true

      // Load seller products
      await this.loadSellerProducts()

      // Load seller orders
      await this.loadSellerOrders()

      return { success: true }
    },

    async loadSellerProducts() {
      // In production, fetch from API: GET /api/seller/products
      await new Promise(resolve => setTimeout(resolve, 200))

      // Demo: Get products that belong to this seller
      // In real app, products would have a sellerId field
      const { categoryProducts } = await import('@theme-prism/data/products')
      const allProducts = Object.values(categoryProducts).flat()

      // For demo, assign first 20 products to seller
      this.sellerProducts = allProducts.slice(0, 20).map((p, index) => ({
        ...p,
        sellerId: 'seller-1',
        views: Math.floor(Math.random() * 1000) + 100,
        orders: Math.floor(Math.random() * 50) + 5,
        revenue: (Math.random() * 1000 + 50).toFixed(2)
      }))
    },

    async loadSellerOrders() {
      // In production, fetch from API: GET /api/seller/orders
      await new Promise(resolve => setTimeout(resolve, 200))

      // Demo orders
      this.sellerOrders = [
        {
          id: 'ORD-001',
          orderNumber: '2024-001234',
          customerName: 'Max Mustermann',
          customerEmail: 'max@example.com',
          items: [
            { productId: 'auto-1', title: 'AGM Autobatterie', quantity: 1, price: 89.99 }
          ],
          total: 89.99,
          status: 'pending',
          createdAt: '2024-01-15T10:30:00Z',
          shippingAddress: 'Musterstraße 1, 12345 Berlin'
        },
        {
          id: 'ORD-002',
          orderNumber: '2024-001235',
          customerName: 'Anna Schmidt',
          customerEmail: 'anna@example.com',
          items: [
            { productId: 'garten-1', title: 'Gartenstuhl Set', quantity: 4, price: 49.99 }
          ],
          total: 199.96,
          status: 'completed',
          createdAt: '2024-01-14T14:20:00Z',
          shippingAddress: 'Hauptstraße 42, 54321 München'
        }
      ]
    },

    async updateSellerProfile(profileData) {
      // In production: PUT /api/seller/profile
      await new Promise(resolve => setTimeout(resolve, 500))

      this.currentSeller = {
        ...this.currentSeller,
        ...profileData
      }

      return { success: true }
    },

    async addProduct(productData) {
      // In production: POST /api/seller/products
      await new Promise(resolve => setTimeout(resolve, 500))

      const newProduct = {
        ...productData,
        id: `seller-product-${Date.now()}`,
        sellerId: 'seller-1',
        views: 0,
        orders: 0,
        revenue: 0
      }

      this.sellerProducts.push(newProduct)
      return { success: true, product: newProduct }
    },

    async updateProduct(productId, productData) {
      // In production: PUT /api/seller/products/:id
      await new Promise(resolve => setTimeout(resolve, 500))

      const index = this.sellerProducts.findIndex(p => p.id === productId)
      if (index !== -1) {
        this.sellerProducts[index] = {
          ...this.sellerProducts[index],
          ...productData
        }
        return { success: true }
      }
      return { success: false, error: 'Product not found' }
    },

    async deleteProduct(productId) {
      // In production: DELETE /api/seller/products/:id
      await new Promise(resolve => setTimeout(resolve, 500))

      this.sellerProducts = this.sellerProducts.filter(p => p.id !== productId)
      return { success: true }
    },

    async updateOrderStatus(orderId, status) {
      // In production: PATCH /api/seller/orders/:id
      await new Promise(resolve => setTimeout(resolve, 500))

      const order = this.sellerOrders.find(o => o.id === orderId)
      if (order) {
        order.status = status
        return { success: true }
      }
      return { success: false, error: 'Order not found' }
    },

    async getSellerPublicProfile(sellerId) {
      // In production: GET /api/sellers/:id
      await new Promise(resolve => setTimeout(resolve, 200))

      // Return public seller profile
      return {
        ...demoSeller,
        id: sellerId
      }
    }
  },

  persist: {
    key: 'seller-storage',
    paths: ['currentSeller', 'isSeller']
  }
})

