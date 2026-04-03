<template>
  <div class="min-h-screen bg-gradient-to-b from-[#f4f6f4] to-[#eef1ee] pb-12 font-sans text-[15px] text-gray-800">
    <header class="sticky top-0 z-50 border-b border-gray-200/90 bg-white/95 backdrop-blur-md px-4 py-3 flex flex-wrap gap-3 justify-between items-center shadow-sm">
      <div class="flex items-center gap-3">
        <button type="button" @click="$router.back()" class="p-2 rounded-lg text-gray-500 hover:bg-gray-100 transition" aria-label="Back">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </button>
        <div>
          <p class="text-[10px] font-extrabold uppercase tracking-[0.14em] text-[#275a19]">Point of sale</p>
          <h1 class="text-lg font-bold text-gray-900 leading-tight">New order</h1>
        </div>
      </div>
      <button
        type="button"
        @click="createOrder"
        :disabled="orderItems.length === 0 || saving"
        class="tenant-btn-submit px-5 py-2.5 text-sm font-bold disabled:opacity-45"
      >
        {{ saving ? 'Saving…' : 'Complete sale' }}
      </button>
    </header>

    <main class="max-w-6xl mx-auto mt-5 px-4 grid grid-cols-1 lg:grid-cols-12 gap-5">
      <!-- Products -->
      <div class="lg:col-span-7 space-y-4">
        <section class="rounded-xl border border-gray-200 bg-white p-1 shadow-sm">
          <div class="grid grid-cols-2 gap-1 p-1">
            <button
              type="button"
              @click="fulfillmentType = 'pickup'"
              :class="fulfillmentType === 'pickup' ? 'bg-[#275a19] text-white shadow' : 'bg-gray-50 text-gray-600 hover:bg-gray-100'"
              class="rounded-lg py-3 text-sm font-bold transition"
            >
              In-store pickup
            </button>
            <button
              type="button"
              @click="fulfillmentType = 'shipping'"
              :class="fulfillmentType === 'shipping' ? 'bg-[#275a19] text-white shadow' : 'bg-gray-50 text-gray-600 hover:bg-gray-100'"
              class="rounded-lg py-3 text-sm font-bold transition"
            >
              Ship to customer
            </button>
          </div>
        </section>

        <section class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
          <div class="p-4 border-b border-gray-100">
            <h2 class="text-sm font-bold text-gray-900 mb-1">Add products</h2>
            <p class="text-xs text-gray-500 mb-3">Type at least 2 letters to search by name or SKU, then tap a row to add.</p>
            <div class="relative">
              <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
              </span>
              <input
                v-model="productQuery"
                @input="debouncedSearch"
                type="search"
                autocomplete="off"
                placeholder="Search product name or SKU…"
                class="w-full pl-11 pr-3 py-3 rounded-xl border border-gray-200 text-base focus:ring-2 focus:ring-[#275a19]/30 focus:border-[#275a19] outline-none"
              />
              <div
                v-if="searchResults.length > 0"
                class="absolute z-50 left-0 right-0 mt-2 max-h-64 overflow-y-auto rounded-xl border border-gray-200 bg-white shadow-xl"
              >
                <button
                  v-for="product in searchResults"
                  :key="`${product.id}-${product.variant_id ?? 'base'}`"
                  type="button"
                  @click="addToOrder(product)"
                  class="w-full flex items-center justify-between gap-3 px-4 py-3 text-left hover:bg-gray-50 border-b border-gray-50 last:border-0"
                >
                  <div class="min-w-0">
                    <p class="font-semibold text-gray-900 truncate">{{ product.name }}</p>
                    <p class="text-xs text-gray-500 truncate">SKU {{ product.sku || '—' }}</p>
                  </div>
                  <span class="text-sm font-bold text-[#275a19] shrink-0">Rs {{ Number(product.price).toFixed(2) }}</span>
                </button>
              </div>
            </div>
          </div>

          <div class="tenant-data-table-wrap border-0 rounded-none">
            <table v-if="orderItems.length > 0" class="tenant-data-table">
              <thead>
                <tr>
                  <th>Item</th>
                  <th class="w-28 text-center">Qty</th>
                  <th class="w-24 text-right">Line</th>
                  <th class="w-10"></th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(item, index) in orderItems" :key="`${item.id}-${item.variant_id ?? 'x'}-${index}`">
                  <td>
                    <div class="flex items-center gap-3">
                      <img :src="item.image" alt="" class="w-12 h-12 rounded-lg object-cover border border-gray-100 bg-gray-50" />
                      <span class="font-medium text-gray-900">{{ item.name }}</span>
                    </div>
                  </td>
                  <td>
                    <div class="flex items-center justify-center gap-0 border border-gray-200 rounded-lg overflow-hidden bg-white max-w-[7rem] mx-auto">
                      <button type="button" @click="updateQty(index, -1)" class="px-3 py-2 text-lg font-bold hover:bg-gray-100 leading-none">−</button>
                      <input
                        v-model.number="item.qty"
                        type="number"
                        min="1"
                        class="w-10 text-center text-sm font-bold border-0 focus:ring-0 p-0"
                      />
                      <button type="button" @click="updateQty(index, 1)" class="px-3 py-2 text-lg font-bold hover:bg-gray-100 leading-none">+</button>
                    </div>
                  </td>
                  <td class="text-right font-semibold">Rs {{ (item.price * item.qty).toFixed(2) }}</td>
                  <td class="text-right">
                    <button type="button" @click="removeItem(index)" class="p-2 text-gray-400 hover:text-red-600 rounded-lg" aria-label="Remove">
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="2"/></svg>
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
            <div v-else class="py-14 text-center text-gray-500 text-sm">
              No items yet — search and add products above.
            </div>
          </div>
        </section>
      </div>

      <!-- Customer & pay -->
      <div class="lg:col-span-5 space-y-4">
        <section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm space-y-4">
          <h2 class="text-sm font-bold text-gray-900">Customer</h2>
          <div v-if="fulfillmentType === 'pickup'" class="space-y-2">
            <p class="text-xs text-gray-500">Optional email for receipt or invoice.</p>
            <input
              v-model="customerDetails.email"
              type="email"
              placeholder="customer@example.com"
              class="w-full rounded-xl border border-gray-200 px-3 py-3 text-base focus:ring-2 focus:ring-[#275a19]/25 focus:border-[#275a19] outline-none"
            />
          </div>
          <div v-else class="space-y-3">
            <input
              v-model="customerDetails.name"
              type="text"
              placeholder="Full name *"
              class="w-full rounded-xl border border-gray-200 px-3 py-3 text-base focus:ring-2 focus:ring-[#275a19]/25 outline-none"
            />
            <input
              v-model="customerDetails.phone"
              type="tel"
              placeholder="Phone"
              class="w-full rounded-xl border border-gray-200 px-3 py-3 text-base focus:ring-2 focus:ring-[#275a19]/25 outline-none"
            />
            <input
              v-model="customerDetails.email"
              type="email"
              placeholder="Email"
              class="w-full rounded-xl border border-gray-200 px-3 py-3 text-base focus:ring-2 focus:ring-[#275a19]/25 outline-none"
            />
            <input
              v-model="customerDetails.street"
              type="text"
              placeholder="Street address"
              class="w-full rounded-xl border border-gray-200 px-3 py-3 text-base focus:ring-2 focus:ring-[#275a19]/25 outline-none"
            />
            <div class="grid grid-cols-2 gap-2">
              <input v-model="customerDetails.postcode" type="text" placeholder="Postcode" class="rounded-xl border border-gray-200 px-3 py-3 text-base outline-none focus:ring-2 focus:ring-[#275a19]/25" />
              <input v-model="customerDetails.location" type="text" placeholder="City" class="rounded-xl border border-gray-200 px-3 py-3 text-base outline-none focus:ring-2 focus:ring-[#275a19]/25" />
            </div>
          </div>
        </section>

        <section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
          <h2 class="text-sm font-bold text-gray-900 mb-3">Payment</h2>
          <div class="grid grid-cols-2 gap-2">
            <button
              type="button"
              @click="paymentMethod = 'cash'"
              :class="paymentMethod === 'cash' ? 'ring-2 ring-[#275a19] bg-[#275a19]/10 border-[#275a19]' : 'border-gray-200'"
              class="flex flex-col items-center gap-2 rounded-xl border-2 py-4 transition"
            >
              <svg class="w-8 h-8 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
              </svg>
              <span class="text-sm font-bold">Cash</span>
            </button>
            <button
              type="button"
              @click="paymentMethod = 'card'"
              :class="paymentMethod === 'card' ? 'ring-2 ring-[#275a19] bg-[#275a19]/10 border-[#275a19]' : 'border-gray-200'"
              class="flex flex-col items-center gap-2 rounded-xl border-2 py-4 transition"
            >
              <svg class="w-8 h-8 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
              </svg>
              <span class="text-sm font-bold">Card</span>
            </button>
          </div>
        </section>

        <section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm sticky top-24">
          <h2 class="text-sm font-bold text-gray-900 mb-4">Summary</h2>
          <div class="space-y-2 text-sm border-b border-gray-100 pb-4">
            <div class="flex justify-between">
              <span class="text-gray-500">Subtotal</span>
              <span class="font-medium">Rs {{ subtotal.toFixed(2) }}</span>
            </div>
            <div v-if="fulfillmentType === 'shipping'" class="flex justify-between items-center gap-2">
              <span class="text-gray-500">Shipping</span>
              <input v-model.number="shipping" type="number" min="0" step="0.01" class="w-24 text-right rounded-lg border border-gray-200 px-2 py-1.5 text-sm" />
            </div>
            <div class="flex justify-between items-center gap-2">
              <span class="text-gray-500">Discount %</span>
              <input v-model.number="discount" type="number" min="0" max="100" class="w-16 text-right rounded-lg border border-gray-200 px-2 py-1.5 text-sm" />
            </div>
            <div class="flex justify-between items-center">
              <span class="text-gray-500">Tax %</span>
              <div class="flex items-center border border-gray-200 rounded-lg overflow-hidden">
                <button type="button" @click="taxRate = Math.max(0, taxRate - 1)" class="px-2 py-1 hover:bg-gray-100 font-bold">−</button>
                <input v-model.number="taxRate" type="number" class="w-12 text-center text-sm border-0 focus:ring-0 py-1" />
                <button type="button" @click="taxRate++" class="px-2 py-1 hover:bg-gray-100 font-bold">+</button>
              </div>
            </div>
            <div class="flex justify-between text-gray-500">
              <span>Tax amount</span>
              <span>Rs {{ vatAmount.toFixed(2) }}</span>
            </div>
          </div>
          <div class="pt-4 flex justify-between items-baseline">
            <span class="text-lg font-bold text-gray-900">Total</span>
            <span class="text-2xl font-extrabold text-[#275a19]">Rs {{ total.toFixed(2) }}</span>
          </div>
        </section>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import axiosTenant from '@/api/axiosTenant'
import Swal from 'sweetalert2'
import { formatApiErrorHtml } from '@tenant/helpers/apiErrorMessage'

const fulfillmentType = ref('pickup')
const paymentMethod = ref('cash')
const productQuery = ref('')
const searchResults = ref([])
const orderItems = ref([])
const shipping = ref(0)
const discount = ref(0)
const taxRate = ref(19)
const saving = ref(false)

const customerDetails = ref({
  name: '',
  phone: '',
  email: '',
  street: '',
  postcode: '',
  location: '',
  country: 'Germany',
})

let searchTimer = null
const debouncedSearch = () => {
  clearTimeout(searchTimer)
  searchTimer = setTimeout(() => {
    searchProducts()
  }, 280)
}

const subtotal = computed(() => orderItems.value.reduce((acc, item) => acc + item.price * item.qty, 0))
const discountAmount = computed(() => subtotal.value * (discount.value / 100))
const taxableAmount = computed(() => Math.max(0, subtotal.value - discountAmount.value))
const vatAmount = computed(() => taxableAmount.value * (taxRate.value / 100))
const total = computed(() => taxableAmount.value + vatAmount.value + (fulfillmentType.value === 'shipping' ? Number(shipping.value) || 0 : 0))

watch(fulfillmentType, (v) => {
  if (v === 'pickup') shipping.value = 0
})

function variantLabel(v) {
  const opts = v.option_values || v.optionValues || []
  if (!opts.length) return ''
  return opts.map((o) => o.name).filter(Boolean).join(' / ')
}

const searchProducts = async () => {
  if (productQuery.value.length < 2) {
    searchResults.value = []
    return
  }
  try {
    const response = await axiosTenant.get('/products', { params: { search: productQuery.value } })
    if (!response.data.success) {
      searchResults.value = []
      return
    }
    const flattened = []
    for (const p of response.data.products || []) {
      const variants = p.variants || []
      if (variants.length > 0) {
        for (const v of variants) {
          const label = variantLabel(v)
          flattened.push({
            id: p.id,
            variant_id: v.id,
            name: label ? `${p.name} (${label})` : `${p.name} — ${v.title || 'Variant'}`,
            sku: v.sku || p.sku,
            price: Number(v.price ?? p.price ?? 0),
            image: v.media?.[0]?.cdn_url || p.media?.[0]?.cdn_url || 'https://via.placeholder.com/80',
          })
        }
      } else {
        flattened.push({
          id: p.id,
          variant_id: null,
          name: p.name,
          sku: p.sku,
          price: Number(p.price ?? 0),
          image: p.media?.[0]?.cdn_url || 'https://via.placeholder.com/80',
        })
      }
    }
    searchResults.value = flattened
  } catch (e) {
    console.error(e)
    searchResults.value = []
  }
}

const addToOrder = (item) => {
  const existing = orderItems.value.find((i) => i.id === item.id && i.variant_id === item.variant_id)
  if (existing) existing.qty += 1
  else orderItems.value.push({ ...item, qty: 1 })
  productQuery.value = ''
  searchResults.value = []
}

const updateQty = (idx, delta) => {
  const next = orderItems.value[idx].qty + delta
  if (next >= 1) orderItems.value[idx].qty = next
}

const removeItem = (idx) => orderItems.value.splice(idx, 1)

const createOrder = async () => {
  if (fulfillmentType.value === 'shipping' && !customerDetails.value.name?.trim()) {
    await Swal.fire({ icon: 'warning', title: 'Customer name', text: 'Please enter the customer name for shipping.' })
    return
  }
  saving.value = true
  const payload = {
    fulfillment_type: fulfillmentType.value,
    payment_method: paymentMethod.value,
    customer:
      fulfillmentType.value === 'shipping'
        ? { ...customerDetails.value }
        : { name: 'Walk-in', email: customerDetails.value.email || null },
    items: orderItems.value.map((i) => ({
      id: i.id,
      variant_id: i.variant_id,
      name: i.name,
      sku: i.sku,
      price: i.price,
      qty: i.qty,
    })),
    discount_percent: discount.value,
    vat_percent: taxRate.value,
    shipping: fulfillmentType.value === 'shipping' ? shipping.value : 0,
    total: total.value,
  }
  try {
    const response = await axiosTenant.post('/orders', payload)
    if (response.data.success) {
      const order = response.data.data
      const orderId = order?.id
      if (orderId) {
        try {
          await axiosTenant.post(`/orders/${orderId}/pay`, {
            method: paymentMethod.value === 'card' ? 'card' : 'cash',
          })
        } catch (payErr) {
          console.warn('Order created but payment step failed', payErr)
        }
      }
      await Swal.fire({
        icon: 'success',
        title: 'Sale complete',
        text: orderId ? `Order #${orderId} recorded.` : 'Order saved.',
      })
      orderItems.value = []
      customerDetails.value = {
        name: '',
        phone: '',
        email: '',
        street: '',
        postcode: '',
        location: '',
        country: 'Germany',
      }
      discount.value = 0
    }
  } catch (e) {
    await Swal.fire({
      icon: 'error',
      title: 'Could not save order',
      html: formatApiErrorHtml(e),
      width: 520,
    })
  } finally {
    saving.value = false
  }
}
</script>

<style scoped>
input[type='number']::-webkit-inner-spin-button,
input[type='number']::-webkit-outer-spin-button {
  -webkit-appearance: none;
  margin: 0;
}
</style>
