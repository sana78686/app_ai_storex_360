<template>
  <section class="mx-auto max-w-7xl px-4 py-8">
    <div class="mb-6 flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-bold text-slate-900">Wunschliste</h1>
        <p class="mt-1 text-sm text-slate-600">
          {{ wishlistStore.items.length }} {{ wishlistStore.items.length === 1 ? 'Artikel' : 'Artikel' }} gespeichert
        </p>
      </div>
      <button
        v-if="wishlistStore.items.length > 0"
        @click="handleClearWishlist"
        class="rounded-full border border-slate-300 px-4 py-2 text-sm hover:bg-slate-50 flex items-center gap-2"
      >
        <Trash2 class="h-4 w-4" />
        Liste leeren
      </button>
    </div>

    <div v-if="wishlistStore.items.length === 0" class="text-center py-12">
      <Heart class="mx-auto h-16 w-16 text-slate-300 mb-4" />
      <h2 class="mb-2 text-xl font-bold text-slate-900">Ihre Wunschliste ist leer</h2>
      <p class="mb-6 text-slate-600">
        Fügen Sie Artikel hinzu, die Sie später kaufen möchten.
      </p>
      <router-link to="/">
        <button class="rounded-full bg-brand px-6 py-3 text-sm font-semibold text-white hover:bg-brand-dark">
          Weiter einkaufen
        </button>
      </router-link>
    </div>

    <div v-else class="grid gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
      <div
        v-for="item in wishlistStore.items"
        :key="item.id"
        class="group relative flex flex-col rounded-lg border border-slate-200 bg-white shadow-sm transition hover:shadow-md"
      >
        <router-link :to="`/produkt/${item.id}`" class="relative aspect-square overflow-hidden rounded-t-lg bg-slate-100">
          <img
            :src="item.imageUrl"
            :alt="item.title"
            class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105"
          />
        </router-link>
        <div class="flex flex-1 flex-col gap-1 p-3">
          <p class="text-[11px] text-slate-500">Art.-Nr.: {{ item.articleNumber }}</p>
          <router-link :to="`/produkt/${item.id}`">
            <h3 class="line-clamp-2 text-[13px] font-medium text-slate-900 hover:text-brand transition-colors cursor-pointer">
              {{ item.title }}
            </h3>
          </router-link>
          <div class="mt-1 flex items-baseline gap-2">
            <span class="text-lg font-semibold text-slate-900">
              {{ item.price.toFixed(2) }} €
            </span>
            <span v-if="item.oldPrice" class="text-[11px] text-slate-500 line-through">
              {{ item.oldPrice.toFixed(2) }} €
            </span>
          </div>
          <div class="mt-1">
            <span
              v-if="item.available"
              class="inline-flex items-center rounded-full bg-[#e6f7ef] px-2 py-0.5 text-[11px] font-semibold text-[#00994d]"
            >
              Sofort verfügbar
            </span>
            <span
              v-else
              class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-semibold text-slate-500"
            >
              Aktuell nicht verfügbar
            </span>
          </div>
          <div class="mt-3 flex gap-2">
            <button
              @click="handleAddToCart(item)"
              :disabled="!item.available || adding === item.id"
              class="flex-1 rounded-full bg-[#00994d] text-xs font-semibold text-white hover:bg-[#007a3d] disabled:opacity-50 disabled:cursor-not-allowed py-2"
            >
              {{ adding === item.id ? 'Wird hinzugefügt...' : item.available ? 'In den Warenkorb' : 'Nicht verfügbar' }}
            </button>
            <button
              @click="handleRemoveFromWishlist(item.id)"
              class="rounded-full border border-slate-300 p-2 hover:bg-slate-50 transition-colors"
              aria-label="Von Wunschliste entfernen"
            >
              <Heart class="h-4 w-4 fill-red-500 text-red-500" />
            </button>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref } from 'vue'
import { Heart, Trash2 } from 'lucide-vue-next'
import { useWishlistStore } from '@theme-prism/stores/wishlistStore'
import { useCartStore } from '@theme-prism/stores/cartStore'

const wishlistStore = useWishlistStore()
const cartStore = useCartStore()
const adding = ref(null)

const handleAddToCart = (item) => {
  if (!item.available) return

  adding.value = item.id
  cartStore.addItem(item, 1)
  setTimeout(() => {
    adding.value = null
  }, 500)
}

const handleRemoveFromWishlist = (productId) => {
  wishlistStore.removeItem(productId)
}

const handleClearWishlist = () => {
  if (confirm('Möchten Sie wirklich alle Artikel von Ihrer Wunschliste entfernen?')) {
    wishlistStore.clearWishlist()
  }
}
</script>

