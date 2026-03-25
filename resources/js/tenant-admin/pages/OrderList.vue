<template>
  <div class="relative bg-white rounded-lg shadow-sm border border-gray-200">
    <div class="p-4 border-b border-gray-200 flex justify-between items-center bg-white rounded-t-lg">
      <h1 class="text-xl font-bold text-[#202223]">Orders</h1>
      <div class="flex gap-3">
        <button class="px-3 py-1.5 border border-gray-300 rounded-lg text-sm font-semibold hover:bg-gray-50 transition-colors">
          Export
        </button>
        <router-link to="/dashboard/order/create" class="px-4 py-1.5 bg-[#202223] text-white rounded-lg text-sm font-semibold hover:bg-black transition">
          Create Order
        </router-link>
      </div>
    </div>

    <div class="p-3 border-b border-gray-200 bg-white flex flex-col md:flex-row gap-3 items-center">
      <div class="relative flex-1 w-full">
        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
          </svg>
        </span>
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Filter orders"
          class="w-full pl-9 pr-3 py-1.5 border border-gray-300 rounded-lg text-sm focus:ring-1 focus:ring-black outline-none transition-all"
        />
      </div>

      <div class="flex items-center gap-2 w-full md:w-auto">
        <button class="px-3 py-1.5 border border-gray-300 rounded-lg text-sm font-medium hover:bg-gray-50 flex items-center gap-2">
          <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
          </svg>
          Status
        </button>
        <button class="px-3 py-1.5 border border-gray-300 rounded-lg text-sm font-medium hover:bg-gray-50">
          Payment
        </button>
      </div>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full text-left text-sm">
        <thead class="bg-gray-50 text-gray-600 font-medium border-b border-gray-200">
          <tr>
            <th class="px-4 py-3 w-10">
              <input
                type="checkbox"
                :checked="isAllSelected"
                @change="toggleSelectAll"
                class="w-4 h-4 rounded border-gray-300 text-black focus:ring-black cursor-pointer"
              >
            </th>
            <th class="px-4 py-3">Order</th>
            <th class="px-4 py-3">Date</th>
            <th class="px-4 py-3">Customer</th>
            <th class="px-4 py-3">Total</th>
            <th class="px-4 py-3">Payment</th>
            <th class="px-4 py-3">Fulfillment</th>
          </tr>
        </thead>
       <tbody class="divide-y divide-gray-100">
  <tr v-if="loading">
    <td colspan="7" class="px-4 py-10 text-center text-gray-400">Loading orders...</td>
  </tr>

  <tr v-for="order in orders" :key="order.id"
      class="hover:bg-gray-50 transition-colors"
      :class="{'bg-blue-50': selectedOrders.includes(order.id)}">
    <td class="px-4 py-4">
      <input type="checkbox" v-model="selectedOrders" :value="order.id" class="w-4 h-4 rounded border-gray-300">
    </td>
    <td class="px-4 py-4 font-bold text-black">#{{ order.id }}</td>
    <td class="px-4 py-4 text-gray-500">{{ formatDate(order.created_at) }}</td>
    <td class="px-4 py-4 text-gray-700 font-medium">
      {{ order.customer?.name || 'Guest' }}
    </td>
    <td class="px-4 py-4 text-black">Rs {{ order.total}}</td>
    <td class="px-4 py-4">
      <span :class="order.payment_status === 'paid' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'"
            class="px-2 py-0.5 rounded-full text-[11px] font-bold uppercase">
        {{ order.payment_status || 'Pending' }}
      </span>
    </td>
    <td class="px-4 py-4">
      <span :class="order.status === 'fulfilled' ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800'"
            class="px-2 py-0.5 rounded-full text-[11px] font-bold uppercase">
        {{ order.status }}
      </span>
    </td>
  </tr>
</tbody>
      </table>
      <div class="p-4 border-t border-gray-200 flex justify-between items-center bg-white">
  <span class="text-xs text-gray-500">Showing {{ orders.length }} of {{ pagination.total }} orders</span>
  <div class="flex gap-2">
    <button
      @click="fetchOrders(pagination.current_page - 1)"
      :disabled="pagination.current_page === 1"
      class="px-3 py-1 border rounded text-sm disabled:opacity-50">
      Previous
    </button>
    <button
      @click="fetchOrders(pagination.current_page + 1)"
      :disabled="pagination.current_page === pagination.last_page"
      class="px-3 py-1 border rounded text-sm disabled:opacity-50">
      Next
    </button>
  </div>
</div>
    </div>

    <transition name="slide-up">
      <div v-if="selectedOrders.length > 0"
           class="fixed bottom-8 left-1/2 -translate-x-1/2 bg-white border border-gray-200 shadow-2xl rounded-xl px-6 py-3 flex items-center gap-6 z-50">
        <span class="text-sm font-bold text-gray-900">{{ selectedOrders.length }} selected</span>
        <div class="h-4 w-px bg-gray-300"></div>
        <div class="flex items-center gap-4">
          <button @click="showFulfillModal = true" class="text-sm font-bold text-gray-600 hover:text-black">Fulfill orders</button>
          <button @click="selectedOrders = []" class="text-sm font-bold text-red-600 hover:text-red-700">Cancel</button>
        </div>
      </div>
    </transition>

    <transition name="fade">
      <div v-if="showFulfillModal" class="fixed inset-0 z-[60] flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-gray-900/50 backdrop-blur-sm" @click="showFulfillModal = false"></div>
        <div class="relative bg-white rounded-xl shadow-xl w-full max-w-lg overflow-hidden">
          <div class="p-4 border-b border-gray-200 flex justify-between items-center">
            <h2 class="text-lg font-bold">Fulfill {{ selectedOrders.length }} orders</h2>
            <button @click="showFulfillModal = false" class="text-gray-400 hover:text-black text-2xl">&times;</button>
          </div>
          <div class="p-6">
            <div class="mb-4">
              <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Carrier</label>
              <select class="w-full border border-gray-300 rounded-lg p-2 text-sm outline-none focus:ring-1 focus:ring-black">
                <option>DHL</option><option>UPS</option><option>FedEx</option>
              </select>
            </div>
            <div>
              <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Tracking Number</label>
              <input type="text" class="w-full border border-gray-300 rounded-lg p-2 text-sm outline-none focus:ring-1 focus:ring-black" placeholder="Optional">
            </div>
          </div>
          <div class="p-4 bg-gray-50 border-t border-gray-200 flex justify-end gap-3">
            <button @click="showFulfillModal = false" class="px-4 py-2 text-sm font-semibold border border-gray-300 bg-white rounded-lg hover:bg-gray-50">Cancel</button>
            <button @click="confirmFulfillment" class="px-4 py-2 text-sm font-semibold bg-[#008060] text-white rounded-lg hover:bg-[#006e52]">Confirm Fulfillment</button>
          </div>
        </div>
      </div>
    </transition>
  </div>
</template>

<script setup>
import { ref, onMounted, watch, computed } from 'vue'
import axiosTenant from '@/api/axiosTenant'
import debounce from 'lodash/debounce' // Recommended: npm install lodash

const orders = ref([])
const loading = ref(false)
const searchQuery = ref('')
const selectedOrders = ref([])
const showFulfillModal = ref(false)

// Pagination state
const pagination = ref({
  current_page: 1,
  last_page: 1,
  total: 0
})

// --- Fetch Orders from API ---
const fetchOrders = async (page = 1) => {
  loading.value = true
  try {
    const response = await axiosTenant.get('/orders', {
      params: {
        page: page,
        search: searchQuery.value,
        per_page: 15
      }
    })

    if (response.data.success) {
      // Laravel paginate() returns data inside 'data'
      orders.value = response.data.data.data
      pagination.value = {
        current_page: response.data.data.current_page,
        last_page: response.data.data.last_page,
        total: response.data.data.total
      }
    }
  } catch (error) {
    console.error("Failed to load orders", error)
  } finally {
    loading.value = false
  }
}

// --- Search with Debounce ---
// Wait 500ms after typing stops before calling the API
const handleSearch = debounce(() => {
  fetchOrders(1)
}, 500)

watch(searchQuery, () => {
  handleSearch()
})

// --- Lifecycle ---
onMounted(() => {
  fetchOrders()
})

// --- Selection Logic ---
const isAllSelected = computed(() =>
  selectedOrders.value.length === orders.value.length && orders.value.length > 0
)

const toggleSelectAll = () => {
  selectedOrders.value = isAllSelected.value ? [] : orders.value.map(o => o.id)
}

const confirmFulfillment = () => {
  alert(`Successfully fulfilled orders: ${selectedOrders.value.join(', ')}`)
  showFulfillModal.value = false
  selectedOrders.value = []
}

// Utility for Date formatting
const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit'
  })
}
</script>

<style scoped>
.slide-up-enter-active, .slide-up-leave-active { transition: all 0.3s ease-out; }
.slide-up-enter-from, .slide-up-leave-to { transform: translate(-50%, 20px); opacity: 0; }
.fade-enter-active, .fade-leave-active { transition: opacity 0.2s; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
