<template>
  <div>
    <Breadcrumb :items="breadcrumbItems" />
    <section class="mx-auto max-w-6xl px-4 py-8">
      <h1 v-if="cartStore.items.length > 0" class="mb-6 text-2xl font-bold text-slate-900">{{ $t('cartPage.title') }}</h1>

    <div v-if="cartStore.items.length === 0" class="text-center py-12">
      <ShoppingCart class="mx-auto h-16 w-16 text-slate-300 mb-4" />
      <h1 class="mb-2 text-2xl font-bold text-slate-900">{{ $t('cartPage.emptyTitle') }}</h1>
      <p class="mb-6 text-slate-600">
        {{ $t('cartPage.emptyDescription') }}
      </p>
      <router-link to="/">
        <button class="rounded-full bg-brand px-6 py-3 text-sm font-semibold text-white hover:bg-brand-dark">
          {{ $t('cartPage.continueShopping') }}
        </button>
      </router-link>
    </div>

    <div v-else class="grid gap-6 sm:gap-8 lg:grid-cols-3">
      <!-- Cart Items -->
      <div class="lg:col-span-2 space-y-3 sm:space-y-4">
        <div
          v-for="item in cartStore.items"
          :key="item.id"
          class="flex flex-col sm:flex-row gap-3 sm:gap-4 rounded-lg border border-slate-200 bg-white p-3 sm:p-4 shadow-sm"
        >
          <div class="relative h-32 w-full sm:h-24 sm:w-24 flex-shrink-0 overflow-hidden rounded-md bg-slate-100">
            <img
              :src="item.imageUrl"
              :alt="item.title"
              class="h-full w-full object-cover"
            />
          </div>

          <div class="flex flex-1 flex-col justify-between min-w-0">
            <div class="mb-3 sm:mb-0">
              <router-link :to="`/produkt/${item.id}`">
                <h3 class="font-medium text-sm sm:text-base text-slate-900 hover:text-brand transition-colors line-clamp-2">
                  {{ item.title }}
                </h3>
              </router-link>
              <p class="mt-1 text-xs text-slate-500">
                {{ $t('cartPage.articleNumber') }}: {{ item.articleNumber }}
              </p>
            </div>

            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 sm:gap-4">
              <div class="flex items-center gap-2 w-full sm:w-auto">
                <button
                  @click="updateQuantity(item.id, item.quantity - 1)"
                  class="flex h-10 w-10 sm:h-8 sm:w-8 items-center justify-center rounded border-2 border-slate-300 hover:bg-slate-50 active:bg-slate-100 touch-manipulation"
                  :aria-label="$t('cartPage.decreaseQuantity')"
                >
                  <Minus class="h-4 w-4" />
                </button>
                <span class="w-12 sm:w-14 text-center text-sm font-medium">
                  {{ item.quantity }}
                </span>
                <button
                  @click="updateQuantity(item.id, item.quantity + 1)"
                  class="flex h-10 w-10 sm:h-8 sm:w-8 items-center justify-center rounded border-2 border-slate-300 hover:bg-slate-50 active:bg-slate-100 touch-manipulation"
                  :aria-label="$t('cartPage.increaseQuantity')"
                >
                  <Plus class="h-4 w-4" />
                </button>
              </div>

              <div class="flex items-center justify-between sm:justify-end gap-4 w-full sm:w-auto">
                <div class="text-left sm:text-right">
                  <p class="font-semibold text-base sm:text-sm text-slate-900">
                    {{ (item.price * item.quantity).toFixed(2) }} €
                  </p>
                  <p v-if="item.oldPrice" class="text-xs text-slate-500 line-through">
                    {{ (item.oldPrice * item.quantity).toFixed(2) }} €
                  </p>
                </div>
                <button
                  @click="removeItem(item.id)"
                  class="text-slate-400 hover:text-red-600 active:text-red-700 transition-colors touch-manipulation min-h-[44px] min-w-[44px] flex items-center justify-center"
                  :aria-label="$t('cartPage.remove')"
                >
                  <Trash2 class="h-5 w-5" />
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Order Summary -->
      <div class="lg:col-span-1">
        <div class="sticky top-20 sm:top-24 rounded-lg border border-slate-200 bg-white p-4 sm:p-6 shadow-sm">
          <h2 class="mb-4 text-lg font-semibold text-slate-900">{{ $t('cartPage.orderSummary') }}</h2>

          <div class="space-y-3 border-b border-slate-200 pb-4">
            <div class="flex justify-between text-sm">
              <span class="text-slate-600">{{ $t('cartPage.subtotal') }}</span>
              <span class="font-medium">{{ cartStore.totalPrice.toFixed(2) }} €</span>
            </div>
            <div class="flex justify-between text-sm">
              <span class="text-slate-600">{{ $t('cartPage.shipping') }}</span>
              <span class="font-medium">
                {{ cartStore.totalPrice >= 50 ? $t('cartPage.shippingFree') : '5.99 €' }}
              </span>
            </div>
          </div>

          <div class="mt-4 flex justify-between border-t border-slate-200 pt-4">
            <span class="text-lg font-bold text-slate-900">{{ $t('cartPage.total') }}</span>
            <span class="text-lg font-bold text-slate-900">
              {{ (cartStore.totalPrice + (cartStore.totalPrice >= 50 ? 0 : 5.99)).toFixed(2) }} €
            </span>
          </div>

          <p v-if="cartStore.totalPrice < 50" class="mt-2 text-xs text-slate-600">
            {{ $t('cartPage.freeShippingRemaining', { amount: (50 - cartStore.totalPrice).toFixed(2) }) }}
          </p>

          <div class="mt-6 space-y-3 relative" data-checkout-dropdown>
            <!-- Logged in: single checkout button -->
            <button
              v-if="authStore.isAuthenticated"
              type="button"
              @click="goToCheckout"
              class="w-full rounded-full bg-brand py-3 text-sm font-semibold text-white hover:bg-brand-dark"
            >
              {{ $t('cartPage.checkout') }}
            </button>
            <!-- Guest: dropdown with Login, Register, Continue as guest -->
            <template v-else>
              <button
                type="button"
                @click="showCheckoutOptions = !showCheckoutOptions"
                class="w-full rounded-full bg-brand py-3 text-sm font-semibold text-white hover:bg-brand-dark flex items-center justify-center gap-2"
              >
                {{ $t('cartPage.checkout') }}
                <ChevronDown
                  :class="['h-4 w-4 transition-transform', showCheckoutOptions && 'rotate-180']"
                />
              </button>
              <div
                v-if="showCheckoutOptions"
                class="absolute left-0 right-0 top-full mt-2 z-10 rounded-lg border border-slate-200 bg-white p-3 shadow-lg space-y-2"
              >
                <p class="text-xs font-medium text-slate-600 px-1">{{ $t('cartPage.checkoutOptionsTitle') }}</p>
                <button
                  type="button"
                  @click="goToLogin"
                  class="w-full rounded-full border-2 border-slate-200 py-2.5 text-sm font-medium text-slate-700 hover:border-brand hover:text-brand hover:bg-brand/5 transition-colors"
                >
                  {{ $t('cartPage.checkoutLogin') }}
                </button>
                <button
                  type="button"
                  @click="goToRegister"
                  class="w-full rounded-full border-2 border-slate-200 py-2.5 text-sm font-medium text-slate-700 hover:border-brand hover:text-brand hover:bg-brand/5 transition-colors"
                >
                  {{ $t('cartPage.checkoutRegister') }}
                </button>
                <button
                  type="button"
                  @click="goToGuestCheckout"
                  class="w-full rounded-full bg-slate-100 py-2.5 text-sm font-medium text-slate-800 hover:bg-slate-200 transition-colors"
                >
                  {{ $t('cartPage.checkoutGuest') }}
                </button>
              </div>
            </template>
            <button
              @click="cartStore.clearCart()"
              class="w-full rounded-full border border-slate-300 py-2 text-sm hover:bg-slate-50"
            >
              {{ $t('cartPage.emptyCart') }}
            </button>
          </div>

          <div class="mt-6 space-y-2 text-xs text-slate-600">
            <div class="flex items-center gap-2">
              <span>✓</span>
              <span>{{ $t('cartPage.securePayment') }}</span>
            </div>
            <div class="flex items-center gap-2">
              <span>✓</span>
              <span>{{ $t('cartPage.freeReturns') }}</span>
            </div>
            <div class="flex items-center gap-2">
              <span>✓</span>
              <span>{{ $t('cartPage.fastDelivery') }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>
    </section>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useI18n } from 'vue-i18n'
import { ShoppingCart, Trash2, Plus, Minus, ChevronDown } from 'lucide-vue-next'
import { useRouter } from 'vue-router'
import axiosTenant from '@theme-prism/axios'
import { useCartStore } from '@theme-prism/stores/cartStore'
import { useAuthStore } from '@theme-prism/stores/authStore'
// import Breadcrumb from '@theme-prism/components/Breadcrumb.vue'

const cartStore = useCartStore()
const router = useRouter()
const authStore = useAuthStore()
const showCheckoutOptions = ref(false)

async function updateQuantity(productId, quantity) {
  cartStore.updateQuantity(productId, quantity)
  try {
    const token = cartStore.getGuestToken()
    await axiosTenant.put('/cart/items', { product_id: productId, quantity }, { headers: { 'X-Guest-Token': token } })
  } catch (_) {
    // Keep Pinia as source of truth; don't overwrite cart
  }
}

async function removeItem(productId) {
  cartStore.removeItem(productId)
  try {
    const token = cartStore.getGuestToken()
    await axiosTenant.delete('/cart/items', { data: { product_id: productId }, headers: { 'X-Guest-Token': token } })
  } catch (_) {
    // Keep Pinia as source of truth
  }
}

const { t } = useI18n()
const breadcrumbItems = computed(() => [
  { label: t('cartPage.breadcrumbHome'), to: '/' },
  { label: t('cartPage.breadcrumbCart'), to: null }
])

const checkoutRedirectPath = computed(() => router.resolve({ name: 'frontend-checkout' }).fullPath)

function goToCheckout() {
  router.push({ name: 'frontend-checkout' })
}

function goToLogin() {
  showCheckoutOptions.value = false
  router.push({ name: 'frontend-login', query: { redirect: checkoutRedirectPath.value } })
}

function goToRegister() {
  showCheckoutOptions.value = false
  router.push({ name: 'frontend-register', query: { redirect: checkoutRedirectPath.value } })
}

function goToGuestCheckout() {
  showCheckoutOptions.value = false
  router.push({ name: 'frontend-checkout' })
}

function closeCheckoutOptions(e) {
  if (!e.target.closest('[data-checkout-dropdown]')) {
    showCheckoutOptions.value = false
  }
}

onMounted(() => {
  document.addEventListener('click', closeCheckoutOptions)
})
onUnmounted(() => {
  document.removeEventListener('click', closeCheckoutOptions)
})
</script>






