<template>
  <div class="min-h-screen bg-[#f6f6f7] pb-12 font-sans text-[#303030]">
    <header class="sticky top-0 z-50 bg-white border-b border-gray-200 px-4 py-2 flex justify-between items-center shadow-sm">
      <div class="flex items-center gap-3">
        <button @click="$router.back()" class="p-1 hover:bg-gray-100 rounded">
           <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </button>
        <h1 class="text-xs font-bold uppercase tracking-tight">New Order</h1>
      </div>
      <div class="flex gap-2">
        <button @click="createOrder" :disabled="orderItems.length === 0" class="px-3 py-1 bg-[#1a1a1a] text-white rounded-md text-[11px] font-bold hover:bg-black disabled:bg-gray-300 transition-all">
          CREATE ORDER
        </button>
      </div>
    </header>

    <main class="max-w-[1000px] mx-auto mt-4 px-3 grid grid-cols-1 lg:grid-cols-3 gap-4">

      <div class="lg:col-span-2 space-y-4">
        <section class="bg-white border border-gray-200 rounded-lg shadow-sm p-1 flex gap-1">
          <button @click="fulfillmentType = 'pickup'" :class="fulfillmentType === 'pickup' ? 'bg-black text-white' : 'hover:bg-gray-50 text-gray-500'" class="flex-1 py-1.5 rounded-md text-[10px] font-bold transition-all uppercase tracking-wider">
            Physical Pickup
          </button>
          <button @click="fulfillmentType = 'shipping'" :class="fulfillmentType === 'shipping' ? 'bg-black text-white' : 'hover:bg-gray-50 text-gray-500'" class="flex-1 py-1.5 rounded-md text-[10px] font-bold transition-all uppercase tracking-wider">
            Ship to Address
          </button>
        </section>

        <section class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-visible">
          <div class="p-3">
            <h3 class="text-[10px] font-bold uppercase text-gray-400 mb-2 tracking-widest">Products</h3>
            <div class="relative">
              <span class="absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
              </span>
              <input v-model="productQuery" @input="searchProducts" type="text" placeholder="Search SKU or Name..." class="w-full pl-8 pr-3 py-1.5 bg-gray-50 border border-gray-300 rounded-md text-xs focus:ring-1 focus:ring-black outline-none" />

              <div v-if="searchResults.length > 0" class="absolute z-[100] left-0 right-0 mt-1 bg-white border border-gray-200 rounded-md shadow-xl max-h-48 overflow-y-auto">
                <div v-for="product in searchResults" :key="product.id" @click="addToOrder(product)" class="flex items-center justify-between p-2 hover:bg-gray-50 cursor-pointer border-b border-gray-50 text-xs">
                  <div>
                    <p class="font-medium">{{ product.name }}</p>
                    <span class="text-[9px] text-gray-400">SKU: {{ product.sku }}</span>
                  </div>
                  <span class="font-bold">Rs {{ product.price }}</span>
                </div>
              </div>
            </div>
          </div>

          <div class="border-t border-gray-100">
            <table v-if="orderItems.length > 0" class="w-full text-left">
              <tbody class="divide-y divide-gray-100 text-[11px]">
                <tr v-for="(item, index) in orderItems" :key="index">
                  <td class="p-2">
                    <div class="flex items-center gap-2">
                      <img :src="item.image" class="w-8 h-8 object-cover rounded border" />
                      <span class="font-medium truncate">{{ item.name }}</span>
                    </div>
                  </td>
                  <td class="p-2">
                    <div class="flex items-center border border-gray-200 rounded-md bg-white overflow-hidden">
                      <button @click="updateQty(index, -1)" class="px-1.5 py-0.5 hover:bg-gray-100">-</button>
                      <input type="number" v-model.number="item.qty" class="w-6 text-center text-[10px] font-bold border-none outline-none" />
                      <button @click="updateQty(index, 1)" class="px-1.5 py-0.5 hover:bg-gray-100">+</button>
                    </div>
                  </td>
                  <td class="p-2 font-bold text-right">Rs {{ (item.price * item.qty).toFixed(2) }}</td>
                  <td class="p-2 text-right">
                    <button @click="removeItem(index)" class="text-gray-300 hover:text-red-500">
                      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="2"/></svg>
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
            <div v-else class="p-6 text-center text-gray-400 text-[11px] italic">Add products to start</div>
          </div>
        </section>
      </div>

      <div class="lg:col-span-1 space-y-4">
        <section class="bg-white border border-gray-200 rounded-lg shadow-sm p-4 space-y-3">
          <h3 class="text-[10px] font-bold uppercase text-gray-400 tracking-widest">Customer</h3>

          <div v-if="fulfillmentType === 'pickup'" class="animate-in fade-in duration-300">
             <label class="text-[9px] font-bold text-gray-400 uppercase">Email (Optional for Invoice)</label>
             <input v-model="customerDetails.email" type="email" placeholder="customer@mail.com" class="w-full border-b border-gray-200 py-1 text-xs outline-none focus:border-black transition-colors">
          </div>

          <div v-else class="space-y-3 animate-in slide-in-from-top-2 duration-300">
            <select v-model="selectedCustomer" @change="syncCustomerData" class="w-full border border-gray-300 rounded-md px-2 py-1 text-xs bg-gray-50 outline-none">
              <option :value="null">Guest / New Address</option>
              <option v-for="c in customers" :key="c.id" :value="c">{{ c.name }}</option>
            </select>
            <input v-model="customerDetails.name" type="text" placeholder="Name" class="w-full border-b border-gray-200 py-1 text-xs outline-none">
            <input v-model="customerDetails.phone" type="text" placeholder="Phone" class="w-full border-b border-gray-200 py-1 text-xs outline-none">
            <input v-model="customerDetails.street" type="text" placeholder="Street" class="w-full border-b border-gray-200 py-1 text-xs outline-none">
            <div class="grid grid-cols-2 gap-2">
              <input v-model="customerDetails.postcode" type="text" placeholder="Postcode" class="w-full border-b border-gray-200 py-1 text-xs outline-none">
              <input v-model="customerDetails.location" type="text" placeholder="City" class="w-full border-b border-gray-200 py-1 text-xs outline-none">
            </div>
          </div>
        </section>

        <section class="bg-white border border-gray-200 rounded-lg shadow-sm p-3">
          <h3 class="text-[10px] font-bold uppercase text-gray-400 mb-2 tracking-widest">Payment Method</h3>
          <div class="grid grid-cols-2 gap-1.5">
            <button @click="paymentMethod = 'cash'" :class="paymentMethod === 'cash' ? 'bg-black text-white' : 'border border-gray-200 text-gray-500'" class="flex flex-col items-center py-2 rounded-lg transition-all">
              <svg class="w-4 h-4 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
              <span class="text-[9px] font-bold uppercase">Cash</span>
            </button>
            <button @click="paymentMethod = 'card'" :class="paymentMethod === 'card' ? 'bg-black text-white' : 'border border-gray-200 text-gray-500'" class="flex flex-col items-center py-2 rounded-lg transition-all">
              <svg class="w-4 h-4 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
              <span class="text-[9px] font-bold uppercase">Card</span>
            </button>
          </div>
        </section>

        <section class="bg-white border border-gray-200 rounded-lg shadow-sm p-4 sticky top-16">
          <h3 class="text-[10px] font-bold uppercase text-gray-400 mb-3 tracking-widest">Summary</h3>
          <div class="space-y-2 text-[11px] border-b border-gray-100 pb-3">
            <div class="flex justify-between">
              <span class="text-gray-500">Subtotal</span>
              <span>Rs {{ subtotal.toFixed(2) }}</span>
            </div>
            <div v-if="fulfillmentType === 'shipping'" class="flex justify-between items-center">
              <span class="text-gray-500">Shipping</span>
              <input v-model.number="shipping" type="number" class="w-12 text-right border-b border-gray-200 outline-none" />
            </div>
            <div class="flex justify-between items-center">
              <span class="text-gray-500">Discount (%)</span>
              <input v-model.number="discount" type="number" class="w-8 text-right border-b border-gray-200 outline-none" />
            </div>
            <div class="flex justify-between items-center">
              <div class="flex items-center gap-1">
                <span class="text-gray-500">VAT (%)</span>
                <div class="flex items-center bg-gray-50 rounded px-1 border">
                  <button @click="taxRate > 0 ? taxRate-- : null" class="px-1 text-[10px] hover:bg-gray-200">-</button>
                  <input v-model.number="taxRate" type="number" class="w-6 text-center text-[10px] bg-transparent outline-none">
                  <button @click="taxRate++" class="px-1 text-[10px] hover:bg-gray-200">+</button>
                </div>
              </div>
              <span>Rs {{ vatAmount.toFixed(2) }}</span>
            </div>
          </div>
          <div class="pt-3 flex justify-between font-bold text-base text-gray-900">
            <span>Total</span>
            <span>Rs {{ total.toFixed(2) }}</span>
          </div>
        </section>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import axiosTenant from '@/api/axiosTenant'

const fulfillmentType = ref('pickup') // pickup or shipping
const paymentMethod = ref('cash')
const productQuery = ref('')
const searchResults = ref([])
const orderItems = ref([])
const customers = ref([{ id: 1, name: 'Walking Customer', phone: 'N/A' }])
const selectedCustomer = ref(null)
const shipping = ref(0)
const discount = ref(0)
const taxRate = ref(19)

const customerDetails = ref({
  name: '', phone: '', email: '', street: '', postcode: '', location: '', country: 'Germany'
})

// Calculations
const subtotal = computed(() => orderItems.value.reduce((acc, item) => acc + (item.price * item.qty), 0))
const discountAmount = computed(() => (subtotal.value * (discount.value / 100)))
const taxableAmount = computed(() => subtotal.value - discountAmount.value)
const vatAmount = computed(() => (taxableAmount.value * (taxRate.value / 100)))
const total = computed(() => taxableAmount.value + vatAmount.value + shipping.value)

// Reset shipping if pickup
watch(fulfillmentType, (val) => { if (val === 'pickup') shipping.value = 0 })

const searchProducts = async () => {
  if (productQuery.value.length < 2) { searchResults.value = []; return }
  try {
    const response = await axiosTenant.get('/products', { params: { search: productQuery.value } })
    if (response.data.success) {
      const flattened = []
      response.data.products.forEach(p => {
        if (p.variants?.length > 0) {
          p.variants.forEach(v => {
            flattened.push({
              id: p.id, variant_id: v.id, name: `${p.name} (${v.option_values?.map(o => o.name).join('/')})`,
              sku: v.sku || p.sku, price: v.price || p.price, image: v.media?.[0]?.cdn_url || p.media?.[0]?.cdn_url || 'https://via.placeholder.com/50'
            })
          })
        } else {
          flattened.push({ id: p.id, variant_id: null, name: p.name, sku: p.sku, price: p.price, image: p.media?.[0]?.cdn_url || 'https://via.placeholder.com/50' })
        }
      })
      searchResults.value = flattened
    }
  } catch (e) { console.error(e) }
}

const addToOrder = (item) => {
  const existing = orderItems.value.find(i => i.id === item.id && i.variant_id === item.variant_id)
  if (existing) { existing.qty++ }
  else { orderItems.value.unshift({ ...item, qty: 1 }) }
  productQuery.value = ''; searchResults.value = []
}

const updateQty = (idx, delta) => { if (orderItems.value[idx].qty + delta > 0) orderItems.value[idx].qty += delta }
const removeItem = (idx) => orderItems.value.splice(idx, 1)

const syncCustomerData = () => {
  if (selectedCustomer.value) {
    customerDetails.value = {
        name: selectedCustomer.value.name, phone: selectedCustomer.value.phone, email: selectedCustomer.value.email,
        street: selectedCustomer.value.address, postcode: selectedCustomer.value.postcode, location: selectedCustomer.value.city, country: 'Germany'
    }
  } else {
    customerDetails.value = { name: '', phone: '', email: '', street: '', postcode: '', location: '', country: 'Germany' }
  }
}

const createOrder = async () => {
  const payload = {
    fulfillment_type: fulfillmentType.value,
    payment_method: paymentMethod.value,
    customer: fulfillmentType.value === 'shipping' ? customerDetails.value : { name: 'Walk-in', email: customerDetails.value.email },
    items: orderItems.value,
    discount_percent: discount.value,
    vat_percent: taxRate.value,
    shipping: shipping.value,
    total: total.value
  }
  try {
    const response = await axiosTenant.post('/orders', payload)
    if (response.data.success) {
      alert("Order Created!")
      orderItems.value = []
      customerDetails.value = { name: '', phone: '', email: '', street: '', postcode: '', location: '', country: 'Germany' }
    }
  } catch (e) { alert("Failed") }
}
</script>

<style scoped>
input[type=number]::-webkit-inner-spin-button, input[type=number]::-webkit-outer-spin-button {
  -webkit-appearance: none; margin: 0;
}
</style>
