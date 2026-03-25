import { defineStore } from 'pinia'

export const useOrderStore = defineStore('orders', {
  state: () => ({
    orders: [
      {
        id: 'ORD-001',
        date: '15.01.2024',
        total: 89.99,
        status: 'Versandt',
        canCancel: false, // Cannot cancel after shipping
        items: [
          { id: 1, name: 'Produkt 1', quantity: 2, price: 44.99, image: 'https://via.placeholder.com/100', canReview: false, canReturn: true }
        ],
        orderDate: '15.01.2024',
        paymentDate: '15.01.2024',
        shippingDate: '16.01.2024',
        inTransitDate: null,
        deliveryDate: null,
        shipping: {
          carrier: 'DHL',
          trackingNumber: '1234567890',
          trackingUrl: 'https://www.dhl.de/de/privatkunden/pakete-verfolgen.html',
          estimatedDelivery: '18.01.2024',
          address: 'Max Mustermann\nMusterstraße 123\n12345 Berlin'
        },
        subtotal: 79.99,
        shippingCost: 4.99,
        tax: 13.60,
        refunds: [] // Track refunds
      },
      {
        id: 'ORD-002',
        date: '10.01.2024',
        total: 149.50,
        status: 'Geliefert',
        canCancel: false,
        items: [
          { id: 2, name: 'Produkt 2', quantity: 1, price: 149.50, image: 'https://via.placeholder.com/100', canReview: true, canReturn: true }
        ],
        orderDate: '10.01.2024',
        paymentDate: '10.01.2024',
        shippingDate: '11.01.2024',
        inTransitDate: '12.01.2024',
        deliveryDate: '13.01.2024',
        shipping: {
          carrier: 'Hermes',
          trackingNumber: '0987654321',
          trackingUrl: 'https://www.myhermes.de/versand/verfolgung/',
          estimatedDelivery: '13.01.2024',
          address: 'Max Mustermann\nMusterstraße 123\n12345 Berlin'
        },
        subtotal: 149.50,
        shippingCost: 0,
        tax: 25.42,
        refunds: []
      },
      {
        id: 'ORD-003',
        date: '20.01.2024',
        total: 199.98,
        status: 'Bezahlt',
        canCancel: true, // Can cancel before shipping
        items: [
          { id: 3, name: 'Produkt 3', quantity: 1, price: 99.99, image: 'https://via.placeholder.com/100', canReview: false, canReturn: false },
          { id: 4, name: 'Produkt 4', quantity: 1, price: 99.99, image: 'https://via.placeholder.com/100', canReview: false, canReturn: false }
        ],
        orderDate: '20.01.2024',
        paymentDate: '20.01.2024',
        shippingDate: null,
        inTransitDate: null,
        deliveryDate: null,
        shipping: null,
        subtotal: 199.98,
        shippingCost: 4.99,
        tax: 34.00,
        refunds: []
      }
    ],
    returns: [],
    orderPollInterval: null // For real-time updates
  }),

  getters: {
    getOrderById: (state) => (id) => {
      return state.orders.find(o => o.id === id)
    },
    activeOrders: (state) => {
      return state.orders.filter(o => !['Storniert', 'Geliefert'].includes(o.status))
    }
  },

  actions: {
    cancelOrder(orderId) {
      const order = this.orders.find(o => o.id === orderId)
      if (order && order.canCancel) {
        order.status = 'Storniert'
        order.canCancel = false
        
        // Process refund automatically
        this.processRefund(orderId, order.total, 'Vollständige Rückerstattung wegen Stornierung')
        
        return true
      }
      return false
    },

    requestPartialReturn(orderId, items) {
      // items: [{ itemId, quantity, reason }]
      const order = this.orders.find(o => o.id === orderId)
      if (!order) return false

      const returnRequest = {
        id: `RET-${Date.now()}`,
        orderId,
        items,
        status: 'Antrag gestellt',
        date: new Date().toISOString(),
        reason: items[0]?.reason || 'Nicht angegeben',
        refundAmount: 0 // Calculate based on items
      }

      // Calculate refund amount
      items.forEach(requestedItem => {
        const orderItem = order.items.find(oi => oi.id === requestedItem.itemId)
        if (orderItem) {
          returnRequest.refundAmount += orderItem.price * requestedItem.quantity
        }
      })

      this.returns.push(returnRequest)

      // Process refund
      this.processRefund(orderId, returnRequest.refundAmount, 'Teilweise Rückerstattung')

      return returnRequest
    },

    processRefund(orderId, amount, reason) {
      const order = this.orders.find(o => o.id === orderId)
      if (!order) return false

      const refund = {
        id: `REF-${Date.now()}`,
        orderId,
        amount,
        reason,
        status: 'Beantragt',
        date: new Date().toISOString(),
        processedDate: null,
        expectedDate: new Date(Date.now() + 7 * 24 * 60 * 60 * 1000).toISOString() // 7 days
      }

      if (!order.refunds) {
        order.refunds = []
      }
      order.refunds.push(refund)

      // Simulate refund processing (in production, this would be async)
      setTimeout(() => {
        refund.status = 'In Bearbeitung'
      }, 2000)

      setTimeout(() => {
        refund.status = 'Abgeschlossen'
        refund.processedDate = new Date().toISOString()
      }, 5000)

      return refund
    },

    updateOrderStatus(orderId, newStatus, updateData = {}) {
      const order = this.orders.find(o => o.id === orderId)
      if (!order) return false

      order.status = newStatus

      // Update dates based on status
      if (newStatus === 'Versandt' && !order.shippingDate) {
        order.shippingDate = new Date().toISOString().split('T')[0]
      }
      if (newStatus === 'Unterwegs' && !order.inTransitDate) {
        order.inTransitDate = new Date().toISOString().split('T')[0]
      }
      if (newStatus === 'Geliefert' && !order.deliveryDate) {
        order.deliveryDate = new Date().toISOString().split('T')[0]
        // Enable reviews for all items
        order.items.forEach(item => {
          item.canReview = true
        })
      }

      Object.assign(order, updateData)
      return true
    },

    // Real-time order status polling (simulated)
    startOrderPolling(orderId, callback) {
      if (this.orderPollInterval) {
        clearInterval(this.orderPollInterval)
      }

      // Simulate real-time updates every 10 seconds
      this.orderPollInterval = setInterval(() => {
        const order = this.getOrderById(orderId)
        if (order && callback) {
          // Simulate status progression
          if (order.status === 'Versandt' && Math.random() > 0.7) {
            this.updateOrderStatus(orderId, 'Unterwegs')
            callback(order)
          } else if (order.status === 'Unterwegs' && Math.random() > 0.8) {
            this.updateOrderStatus(orderId, 'Geliefert')
            callback(order)
          }
        }
      }, 10000) // Poll every 10 seconds
    },

    stopOrderPolling() {
      if (this.orderPollInterval) {
        clearInterval(this.orderPollInterval)
        this.orderPollInterval = null
      }
    }
  },

  persist: {
    key: 'order-storage'
  }
})
