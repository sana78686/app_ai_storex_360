<template>
  <div class="tenant-dashboard-page space-y-3 pb-8">
    <!-- Page header (body start — breadcrumb + title, Gull-style) -->
    <div class="flex flex-col gap-3 sm:flex-row sm:flex-wrap sm:items-end sm:justify-between">
      <div class="min-w-0">
        <p class="tenant-dashboard-page__breadcrumb">Orders · Point of sale</p>
        <h1 class="tenant-dashboard-page__title mt-1">New sale</h1>
        <p class="mt-1 text-sm text-gray-500">Search, filter, add to cart, enter customer, complete payment — one screen.</p>
      </div>
      <div class="flex flex-wrap items-center gap-2">
        <div class="inline-flex rounded-lg border border-gray-200 bg-white p-0.5 shadow-sm">
          <button
            type="button"
            @click="fulfillmentType = 'pickup'"
            :class="fulfillmentType === 'pickup' ? 'bg-[#275a19] text-white shadow-sm' : 'text-gray-600 hover:bg-gray-50'"
            class="rounded-md px-3 py-2 text-xs font-bold sm:text-sm"
          >
            In-store pickup
          </button>
          <button
            type="button"
            @click="fulfillmentType = 'shipping'"
            :class="fulfillmentType === 'shipping' ? 'bg-[#275a19] text-white shadow-sm' : 'text-gray-600 hover:bg-gray-50'"
            class="rounded-md px-3 py-2 text-xs font-bold sm:text-sm"
          >
            Delivery
          </button>
        </div>
        <button
          type="button"
          @click="createOrder"
          :disabled="orderItems.length === 0 || saving"
          class="tenant-btn-submit px-4 py-2.5 text-sm font-bold disabled:opacity-40"
        >
          {{ saving ? 'Saving…' : 'Complete sale' }}
        </button>
      </div>
    </div>

    <!-- Main panel -->
    <div class="tenant-dashboard-panel overflow-hidden">
      <div class="grid grid-cols-1 xl:grid-cols-12 xl:divide-x divide-gray-100">
        <!-- Filters -->
        <aside class="xl:col-span-2 border-b border-gray-100 bg-gray-50/60 p-4 xl:border-b-0">
          <button
            type="button"
            class="mb-4 flex w-full items-center justify-between rounded-lg bg-[#275a19] px-3 py-2.5 text-left text-sm font-bold text-white shadow-md transition hover:bg-[#1f4814]"
            @click="scrollToOrder"
          >
            <span>Current order</span>
            <span class="rounded-full bg-white/20 px-2 py-0.5 text-xs font-extrabold">{{ cartCount }}</span>
          </button>

          <div class="mb-5">
            <p class="tenant-pos-filter-heading">Price</p>
            <div class="space-y-1.5">
              <label
                v-for="opt in priceOptions"
                :key="opt.value"
                class="flex cursor-pointer items-center gap-2 rounded-lg px-2 py-1.5 text-sm hover:bg-white"
              >
                <input v-model="priceTier" type="radio" name="pos-price" :value="opt.value" class="text-[#275a19] focus:ring-[#275a19]" />
                <span>{{ opt.label }}</span>
              </label>
            </div>
          </div>

          <div class="mb-5">
            <p class="tenant-pos-filter-heading">Category</p>
            <div class="max-h-48 space-y-1 overflow-y-auto pr-1">
              <label class="flex cursor-pointer items-center gap-2 rounded-lg px-2 py-1.5 text-sm hover:bg-white">
                <input v-model="filterCategoryId" type="radio" :value="''" class="text-[#275a19] focus:ring-[#275a19]" />
                <span>All categories</span>
              </label>
              <label
                v-for="cat in categories"
                :key="cat.id"
                class="flex cursor-pointer items-center gap-2 rounded-lg px-2 py-1.5 text-sm hover:bg-white"
              >
                <input v-model="filterCategoryId" type="radio" :value="String(cat.id)" class="text-[#275a19] focus:ring-[#275a19]" />
                <span class="truncate">{{ cat.translation?.name || cat.name || '—' }}</span>
              </label>
            </div>
          </div>

          <div v-if="brands.length">
            <p class="tenant-pos-filter-heading">Brand</p>
            <div class="max-h-40 space-y-1 overflow-y-auto pr-1">
              <label
                v-for="b in brands"
                :key="b.id"
                class="flex cursor-pointer items-center gap-2 rounded-lg px-2 py-1.5 text-sm hover:bg-white"
              >
                <input
                  type="checkbox"
                  :value="b.id"
                  v-model="filterBrandIds"
                  class="rounded border-gray-300 text-[#275a19] focus:ring-[#275a19]"
                />
                <span class="truncate">{{ b.name }}</span>
              </label>
            </div>
          </div>
        </aside>

        <!-- Product catalog -->
        <section class="xl:col-span-5 border-b border-gray-100 p-4 xl:border-b-0">
          <div class="mb-3 flex flex-wrap items-center gap-2">
            <div class="inline-flex rounded-lg border border-gray-200 bg-gray-50 p-0.5">
              <button
                type="button"
                @click="viewMode = 'grid'"
                :class="viewMode === 'grid' ? 'bg-white shadow text-[#275a19]' : 'text-gray-500'"
                class="rounded-md p-2"
                aria-label="Grid view"
              >
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
              </button>
              <button
                type="button"
                @click="viewMode = 'list'"
                :class="viewMode === 'list' ? 'bg-white shadow text-[#275a19]' : 'text-gray-500'"
                class="rounded-md p-2"
                aria-label="List view"
              >
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
              </button>
            </div>
            <div class="relative min-w-[12rem] flex-1">
              <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
              </span>
              <input
                v-model="productQuery"
                type="search"
                placeholder="Search name or SKU…"
                autocomplete="off"
                class="w-full rounded-lg border border-gray-200 py-2 pl-9 pr-3 text-sm outline-none focus:border-[#275a19] focus:ring-2 focus:ring-[#275a19]/20"
              />
            </div>
          </div>

          <div v-if="loadingCatalog" class="py-16 text-center text-sm text-gray-500">Loading products…</div>
          <div v-else-if="!displayRows.length" class="py-16 text-center text-sm text-gray-500">No products match your filters.</div>

          <!-- Grid -->
          <div v-else-if="viewMode === 'grid'" class="grid grid-cols-1 gap-3 sm:grid-cols-2">
            <article
              v-for="row in displayRows"
              :key="row.key"
              class="flex flex-col overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm transition hover:border-[#275a19]/40"
            >
              <div class="relative aspect-[4/3] bg-gray-100">
                <img :src="row.image" alt="" class="h-full w-full object-cover" />
              </div>
              <div class="flex flex-1 flex-col p-3">
                <p class="line-clamp-2 text-sm font-semibold text-gray-900">{{ row.name }}</p>
                <p v-if="row.brandName" class="mt-0.5 text-xs text-gray-500">{{ row.brandName }}</p>
                <p class="mt-2 text-base font-bold text-[#275a19]">Rs {{ row.price.toFixed(2) }}</p>
                <div class="mt-3 flex gap-2">
                  <button type="button" class="tenant-btn-submit flex-1 py-2 text-xs font-bold" @click="addToOrder(row)">Add</button>
                </div>
              </div>
            </article>
          </div>

          <!-- List -->
          <div v-else class="tenant-data-table-wrap border border-gray-200">
            <table class="tenant-data-table text-sm">
              <thead>
                <tr>
                  <th class="w-14"> </th>
                  <th>Product</th>
                  <th class="w-28 text-right">Price</th>
                  <th class="w-24 text-right"> </th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="row in displayRows" :key="row.key">
                  <td>
                    <img :src="row.image" alt="" class="h-10 w-10 rounded-md object-cover" />
                  </td>
                  <td>
                    <div class="font-medium text-gray-900">{{ row.name }}</div>
                    <div v-if="row.brandName" class="text-xs text-gray-500">{{ row.brandName }}</div>
                    <div class="text-xs text-gray-400">SKU {{ row.sku || '—' }}</div>
                  </td>
                  <td class="text-right font-semibold text-[#275a19]">Rs {{ row.price.toFixed(2) }}</td>
                  <td class="text-right">
                    <button type="button" class="tenant-btn-outline-accent tenant-btn-sm py-1 text-xs" @click="addToOrder(row)">Add</button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </section>

        <!-- Order + customer + pay -->
        <div id="pos-order-panel" class="xl:col-span-5 bg-gray-50/40 p-4">
          <div class="space-y-4">
            <section class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
              <div class="mb-3 flex items-center justify-between">
                <h2 class="text-sm font-bold text-gray-900">Customer</h2>
                <button type="button" class="text-xs font-semibold text-[#275a19] hover:underline" @click="setWalkIn">Walk-in</button>
              </div>
              <div v-if="fulfillmentType === 'pickup'" class="space-y-2">
                <input
                  v-model="customerDetails.name"
                  type="text"
                  placeholder="Name (optional)"
                  class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-[#275a19]/20"
                />
                <input
                  v-model="customerDetails.email"
                  type="email"
                  placeholder="Email (optional, for receipt)"
                  class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-[#275a19]/20"
                />
              </div>
              <div v-else class="space-y-2">
                <input
                  v-model="customerDetails.name"
                  type="text"
                  placeholder="Full name *"
                  class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-[#275a19]/20"
                />
                <input
                  v-model="customerDetails.phone"
                  type="tel"
                  placeholder="Phone"
                  class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-[#275a19]/20"
                />
                <input
                  v-model="customerDetails.email"
                  type="email"
                  placeholder="Email"
                  class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-[#275a19]/20"
                />
                <input
                  v-model="customerDetails.street"
                  type="text"
                  placeholder="Street address"
                  class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-[#275a19]/20"
                />
                <div class="grid grid-cols-2 gap-2">
                  <input v-model="customerDetails.postcode" type="text" placeholder="Postcode" class="rounded-lg border border-gray-200 px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-[#275a19]/20" />
                  <input v-model="customerDetails.location" type="text" placeholder="City" class="rounded-lg border border-gray-200 px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-[#275a19]/20" />
                </div>
              </div>
            </section>

            <section class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
              <h2 class="mb-3 text-sm font-bold text-gray-900">Order lines</h2>
              <div v-if="!orderItems.length" class="py-6 text-center text-sm text-gray-500">No items — add products from the catalog.</div>
              <ul v-else class="max-h-56 space-y-2 overflow-y-auto pr-1">
                <li
                  v-for="(item, index) in orderItems"
                  :key="`${item.id}-${item.variant_id}-${index}`"
                  class="flex gap-2 rounded-lg border border-gray-100 bg-gray-50/80 p-2 text-sm"
                >
                  <img :src="item.image" alt="" class="h-12 w-12 shrink-0 rounded-md object-cover" />
                  <div class="min-w-0 flex-1">
                    <p class="font-medium leading-tight text-gray-900">{{ item.name }}</p>
                    <p class="text-xs text-gray-500">Rs {{ item.price.toFixed(2) }} × {{ item.qty }}</p>
                    <div class="mt-1 flex items-center gap-1">
                      <button type="button" class="rounded border border-gray-200 bg-white px-2 py-0.5 text-xs font-bold hover:bg-gray-100" @click="updateQty(index, -1)">−</button>
                      <span class="w-6 text-center text-xs font-bold">{{ item.qty }}</span>
                      <button type="button" class="rounded border border-gray-200 bg-white px-2 py-0.5 text-xs font-bold hover:bg-gray-100" @click="updateQty(index, 1)">+</button>
                      <button type="button" class="ml-auto text-xs text-red-600 hover:underline" @click="removeItem(index)">Remove</button>
                    </div>
                  </div>
                  <div class="shrink-0 text-right font-semibold text-gray-800">Rs {{ (item.price * item.qty).toFixed(2) }}</div>
                </li>
              </ul>
            </section>

            <section class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
              <h2 class="mb-3 text-sm font-bold text-gray-900">Payment</h2>
              <div class="grid grid-cols-2 gap-2">
                <button
                  type="button"
                  @click="paymentMethod = 'cash'"
                  :class="paymentMethod === 'cash' ? 'ring-2 ring-[#275a19] bg-[#275a19]/10' : 'border-gray-200'"
                  class="rounded-lg border-2 py-3 text-sm font-bold"
                >
                  Cash
                </button>
                <button
                  type="button"
                  @click="paymentMethod = 'card'"
                  :class="paymentMethod === 'card' ? 'ring-2 ring-[#275a19] bg-[#275a19]/10' : 'border-gray-200'"
                  class="rounded-lg border-2 py-3 text-sm font-bold"
                >
                  Card
                </button>
              </div>
            </section>

            <section class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
              <h2 class="mb-3 text-sm font-bold text-gray-900">Summary</h2>
              <div class="space-y-2 text-sm text-gray-600">
                <div class="flex justify-between">
                  <span>Subtotal</span>
                  <span class="font-medium text-gray-900">Rs {{ subtotal.toFixed(2) }}</span>
                </div>
                <div v-if="fulfillmentType === 'shipping'" class="flex items-center justify-between gap-2">
                  <span>Shipping</span>
                  <input v-model.number="shipping" type="number" min="0" step="0.01" class="w-24 rounded border border-gray-200 px-2 py-1 text-right text-sm" />
                </div>
                <div class="flex items-center justify-between gap-2">
                  <span>Discount %</span>
                  <input v-model.number="discount" type="number" min="0" max="100" class="w-16 rounded border border-gray-200 px-2 py-1 text-right text-sm" />
                </div>
                <div class="flex items-center justify-between">
                  <span>Tax %</span>
                  <div class="flex items-center rounded border border-gray-200">
                    <button type="button" class="px-2 py-1 text-xs" @click="taxRate = Math.max(0, taxRate - 1)">−</button>
                    <input v-model.number="taxRate" type="number" class="w-10 border-0 p-1 text-center text-xs focus:ring-0" />
                    <button type="button" class="px-2 py-1 text-xs" @click="taxRate++">+</button>
                  </div>
                </div>
                <div class="flex justify-between text-gray-500">
                  <span>Tax</span>
                  <span>Rs {{ vatAmount.toFixed(2) }}</span>
                </div>
              </div>
              <div class="mt-4 flex items-baseline justify-between border-t border-gray-100 pt-3">
                <span class="text-base font-bold text-gray-900">Total</span>
                <span class="text-xl font-extrabold text-[#275a19]">Rs {{ total.toFixed(2) }}</span>
              </div>
            </section>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue'
import axiosTenant from '@/api/axiosTenant'
import Swal from 'sweetalert2'
import { formatApiErrorHtml } from '@tenant/helpers/apiErrorMessage'

const fulfillmentType = ref('pickup')
const paymentMethod = ref('cash')
const productQuery = ref('')
const viewMode = ref('grid')
const priceTier = ref('all')
const filterCategoryId = ref('')
const filterBrandIds = ref([])
const flatRows = ref([])
const loadingCatalog = ref(false)
const orderItems = ref([])
const categories = ref([])
const brands = ref([])
const shipping = ref(0)
const discount = ref(0)
const taxRate = ref(19)
const saving = ref(false)

const priceOptions = [
  { value: 'all', label: 'All prices' },
  { value: 'lt1000', label: 'Under 1,000' },
  { value: 'mid', label: '1,000 – 10,000' },
  { value: 'gt10000', label: 'Over 10,000' },
]

const customerDetails = ref({
  name: '',
  phone: '',
  email: '',
  street: '',
  postcode: '',
  location: '',
  country: 'Germany',
})

function variantLabel(v) {
  const opts = v.option_values || v.optionValues || []
  if (!opts.length) return ''
  return opts.map((o) => o.name).filter(Boolean).join(' / ')
}

function flattenProducts(products) {
  const out = []
  for (const p of products || []) {
    const brandName = p.brand?.name ?? null
    const categoryIds = (p.categories || []).map((c) => c.id)
    const image = p.media?.[0]?.cdn_url || 'https://via.placeholder.com/320x240?text=Product'
    const variants = p.variants || []
    if (variants.length > 0) {
      for (const v of variants) {
        const label = variantLabel(v)
        const name = label ? `${p.name} (${label})` : `${p.name} — ${v.title || 'Variant'}`
        out.push({
          key: `v-${p.id}-${v.id}`,
          id: p.id,
          variant_id: v.id,
          name,
          sku: v.sku || p.sku,
          price: Number(v.price ?? p.price ?? 0),
          image: v.media?.[0]?.cdn_url || image,
          brandName,
          categoryIds,
        })
      }
    } else {
      out.push({
        key: `p-${p.id}`,
        id: p.id,
        variant_id: null,
        name: p.name,
        sku: p.sku,
        price: Number(p.price ?? 0),
        image,
        brandName,
        categoryIds,
      })
    }
  }
  return out
}

const displayRows = computed(() => {
  let rows = flatRows.value
  if (filterCategoryId.value) {
    const cid = Number(filterCategoryId.value)
    if (!Number.isNaN(cid)) {
      rows = rows.filter((r) => r.categoryIds.includes(cid))
    }
  }
  if (priceTier.value === 'lt1000') rows = rows.filter((r) => r.price < 1000)
  else if (priceTier.value === 'mid') rows = rows.filter((r) => r.price >= 1000 && r.price <= 10000)
  else if (priceTier.value === 'gt10000') rows = rows.filter((r) => r.price > 10000)

  const q = productQuery.value.trim().toLowerCase()
  if (q.length >= 1) {
    rows = rows.filter((r) => {
      const name = (r.name || '').toLowerCase()
      const sku = (r.sku || '').toLowerCase()
      return name.includes(q) || sku.includes(q)
    })
  }
  return rows
})

const cartCount = computed(() => orderItems.value.reduce((n, i) => n + i.qty, 0))

const subtotal = computed(() => orderItems.value.reduce((acc, item) => acc + item.price * item.qty, 0))
const discountAmount = computed(() => subtotal.value * (discount.value / 100))
const taxableAmount = computed(() => Math.max(0, subtotal.value - discountAmount.value))
const vatAmount = computed(() => taxableAmount.value * (taxRate.value / 100))
const total = computed(
  () => taxableAmount.value + vatAmount.value + (fulfillmentType.value === 'shipping' ? Number(shipping.value) || 0 : 0)
)

let loadTimer = null
function scheduleLoadCatalog() {
  clearTimeout(loadTimer)
  loadTimer = setTimeout(() => loadCatalog(), 320)
}

async function loadCatalog() {
  loadingCatalog.value = true
  try {
    const params = { limit: 180 }
    if (filterBrandIds.value.length) params.brand_ids = filterBrandIds.value.join(',')
    const q = productQuery.value.trim()
    if (q.length >= 2) params.search = q
    const res = await axiosTenant.get('/products', { params })
    if (res.data.success) {
      flatRows.value = flattenProducts(res.data.products || [])
    } else {
      flatRows.value = []
    }
  } catch (e) {
    console.error(e)
    flatRows.value = []
  } finally {
    loadingCatalog.value = false
  }
}

watch(filterBrandIds, () => loadCatalog(), { deep: true })
watch(productQuery, () => scheduleLoadCatalog())

watch(fulfillmentType, (v) => {
  if (v === 'pickup') shipping.value = 0
})

function flattenCategoryTree(roots) {
  const out = []
  const walk = (nodes) => {
    for (const n of nodes || []) {
      out.push(n)
      if (n.children?.length) walk(n.children)
    }
  }
  walk(roots)
  return out
}

onMounted(async () => {
  try {
    const [cRes, bRes] = await Promise.all([
      axiosTenant.get('/categories').catch(() => ({ data: {} })),
      axiosTenant.get('/brands').catch(() => ({ data: {} })),
    ])
    categories.value = flattenCategoryTree(cRes.data?.categories || [])
    brands.value = bRes.data?.brands || []
  } catch (_) {
    /* ignore */
  }
  await loadCatalog()
})

function scrollToOrder() {
  document.getElementById('pos-order-panel')?.scrollIntoView({ behavior: 'smooth', block: 'start' })
}

function setWalkIn() {
  customerDetails.value = {
    name: 'Walk-in',
    phone: '',
    email: '',
    street: '',
    postcode: '',
    location: '',
    country: 'Germany',
  }
}

const addToOrder = (row) => {
  const existing = orderItems.value.find((i) => i.id === row.id && i.variant_id === row.variant_id)
  if (existing) existing.qty += 1
  else {
    orderItems.value.push({
      id: row.id,
      variant_id: row.variant_id,
      name: row.name,
      sku: row.sku,
      price: row.price,
      image: row.image,
      qty: 1,
    })
  }
}

const updateQty = (idx, delta) => {
  const next = orderItems.value[idx].qty + delta
  if (next >= 1) orderItems.value[idx].qty = next
}

const removeItem = (idx) => orderItems.value.splice(idx, 1)

const createOrder = async () => {
  if (fulfillmentType.value === 'shipping' && !customerDetails.value.name?.trim()) {
    await Swal.fire({ icon: 'warning', title: 'Customer name', text: 'Enter the customer name for delivery.' })
    return
  }
  saving.value = true
  const payload = {
    fulfillment_type: fulfillmentType.value,
    payment_method: paymentMethod.value,
    customer:
      fulfillmentType.value === 'shipping'
        ? { ...customerDetails.value }
        : { name: customerDetails.value.name?.trim() || 'Walk-in', email: customerDetails.value.email || null },
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
