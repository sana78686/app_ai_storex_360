<template>
  <section class="border-b border-slate-200 bg-white py-12">
    <div class="mx-auto max-w-7xl px-4">
      <div class="mb-6 flex items-center justify-between">
        <h2 class="text-2xl font-bold text-slate-900">{{ title }}</h2>
        <router-link
          v-if="showAllLink"
          :to="showAllLink"
          class="text-sm font-medium text-brand hover:text-brand-dark transition-colors"
        >
          {{ $t('common.showAll') }} →
        </router-link>
        <button
          v-else
          class="text-sm font-medium text-brand hover:text-brand-dark transition-colors"
        >
          {{ $t('common.showAll') }} →
        </button>
      </div>
      <div class="relative px-8">
        <div
          ref="scrollContainerRef"
          class="flex gap-4 overflow-x-auto scroll-smooth pb-4 [&::-webkit-scrollbar]:hidden [-ms-overflow-style:none] [scrollbar-width:none]"
        >
          <div
            v-for="product in products"
            :key="product.id"
            class="flex-shrink-0 w-[280px] sm:w-[300px]"
          >
            <ProductCard :product="product" />
          </div>
        </div>
        <button
          @click="scroll('left')"
          class="absolute left-0 top-1/2 -translate-y-1/2 rounded-full bg-white p-3 shadow-lg hover:bg-slate-50 transition-colors z-20 border border-slate-200"
          :aria-label="$t('landing.carouselPrev')"
          type="button"
        >
          <ChevronLeft class="h-5 w-5 text-slate-700" />
        </button>
        <button
          @click="scroll('right')"
          class="absolute right-0 top-1/2 -translate-y-1/2 rounded-full bg-white p-3 shadow-lg hover:bg-slate-50 transition-colors z-20 border border-slate-200"
          :aria-label="$t('landing.carouselNext')"
          type="button"
        >
          <ChevronRight class="h-5 w-5 text-slate-700" />
        </button>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref } from 'vue'
import { ChevronLeft, ChevronRight } from 'lucide-vue-next'
import ProductCard from './ProductCard.vue'

const props = defineProps({
  title: {
    type: String,
    required: true
  },
  products: {
    type: Array,
    required: true
  },
  showAllLink: {
    type: String,
    default: null
  }
})

const scrollContainerRef = ref(null)

const scroll = (direction) => {
  if (!scrollContainerRef.value) return

  const container = scrollContainerRef.value
  const scrollAmount = 400
  const scrollTo = direction === 'left'
    ? container.scrollLeft - scrollAmount
    : container.scrollLeft + scrollAmount

  container.scrollTo({
    left: scrollTo,
    behavior: 'smooth'
  })
}
</script>





