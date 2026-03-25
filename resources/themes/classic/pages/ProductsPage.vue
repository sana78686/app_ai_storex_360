<template>
  <div>
    <Breadcrumb :items="breadcrumbItems" />
    <section class="mx-auto max-w-7xl px-3 sm:px-4 py-6 sm:py-8">
      <div class="mb-4 sm:mb-6">
        <h1 class="text-xl sm:text-2xl font-bold text-slate-900">{{ title }}</h1>
        <p class="mt-2 text-xs sm:text-sm text-slate-600">
          {{ products.length }} Produkte gefunden
        </p>
      </div>
    <div class="grid gap-3 sm:gap-4 grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
      <ProductCard
        v-for="product in products"
        :key="product.id"
        :product="product"
      />
    </div>
    <div v-if="products.length === 0" class="py-12 text-center">
      <p class="text-slate-500">Keine Produkte gefunden.</p>
    </div>
    </section>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useRoute } from 'vue-router'
import { categoryProducts } from '@theme-classic/data/products'
import ProductCard from '@theme-classic/component/ProductCard.vue'
import Breadcrumb from '@theme-classic/component/Breadcrumb.vue'

const route = useRoute()

const allProducts = Object.values(categoryProducts).flat()

const filter = computed(() => route.query.filter)

const title = computed(() => {
  if (filter.value === 'beliebt') return 'BELIEBTESTE PRODUKTE'
  if (filter.value === 'empfehlungen') return 'UNSERE EMPFEHLUNGEN'
  return 'ALLE PRODUKTE'
})

const products = computed(() => {
  if (filter.value === 'beliebt' || filter.value === 'empfehlungen') {
    return allProducts.slice(0, 20)
  }
  return allProducts
})

const breadcrumbItems = computed(() => [
  { label: 'Startseite', to: '/' },
  { label: title.value, to: null }
])
</script>






