<template>
  <article class="flex h-full flex-col rounded-lg border border-slate-200 bg-white text-xs shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
    <router-link :to="`/produkt/${product.id}`" class="relative aspect-square overflow-hidden rounded-t-lg bg-slate-100 group">
      <img
        :src="product.imageUrl"
        :alt="product.title"
        class="h-full w-full object-cover transition-transform duration-300 hover:scale-105"
      />
      <button
        @click.stop.prevent="handleToggleWishlist"
        class="absolute top-2 right-2 rounded-full bg-white/90 p-2 shadow-md opacity-0 transition-opacity group-hover:opacity-100 hover:bg-white z-10"
        aria-label="Zur Wunschliste hinzufügen"
      >
        <Heart :class="['h-4 w-4', isInWishlist ? 'fill-red-500 text-red-500' : 'text-slate-700']" />
      </button>
    </router-link>
    <div class="flex flex-1 flex-col gap-1 p-3">
      <p class="text-[11px] text-slate-500">Art.-Nr.: {{ product.articleNumber }}</p>
      <router-link :to="`/produkt/${product.id}`">
        <h3 class="line-clamp-2 text-[13px] font-medium text-slate-900 hover:text-brand transition-colors cursor-pointer">
          {{ product.title }}
        </h3>
      </router-link>
      <div class="mt-1 flex items-baseline gap-2">
        <span class="text-lg font-semibold text-slate-900">
          {{ product.price.toFixed(2) }} €
        </span>
        <span v-if="product.oldPrice" class="text-[11px] text-slate-500 line-through">
          {{ product.oldPrice.toFixed(2) }} €
        </span>
      </div>
      <div class="mt-1">
        <span
          v-if="product.available"
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
      <div class="mt-3">
        <button
          @click="handleAddToCart"
          :disabled="!product.available || adding"
          class="w-full rounded-full bg-[#00994d] text-xs font-semibold text-white hover:bg-[#007a3d] disabled:opacity-50 disabled:cursor-not-allowed py-2"
        >
          {{ adding ? 'Wird hinzugefügt...' : product.available ? 'In den Warenkorb' : 'Nicht verfügbar' }}
        </button>
      </div>
    </div>
  </article>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Heart } from 'lucide-vue-next'
// import { useCartStore } from '@theme-prism/store/cartStore'
// import { useWishlistStore } from '@theme-prism/component/wishlistStore'
import { useCartStore } from '@theme-prism/stores/cartStore'
import { useWishlistStore } from '@theme-prism/stores/wishlistStore'


const props = defineProps({
  product: {
    type: Object,
    required: true
  }
})

const cartStore = useCartStore()
const wishlistStore = useWishlistStore()
const adding = ref(false)

const isInWishlist = computed(() => wishlistStore.isInWishlist(props.product.id))

const handleAddToCart = (e) => {
  e.preventDefault()
  e.stopPropagation()

  if (!props.product.available) return

  adding.value = true
  cartStore.addItem(props.product, 1)
  setTimeout(() => {
    adding.value = false
  }, 500)
}

const handleToggleWishlist = () => {
  wishlistStore.toggleItem(props.product)
}
</script>

