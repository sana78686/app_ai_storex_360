<template>
  <section class="mx-auto max-w-7xl px-4 py-8">
    <div class="mb-6">
      <h1 class="text-2xl font-bold text-slate-900">{{ categoryName }}</h1>
      <p class="mt-2 text-sm text-slate-600">
        {{ loading ? '…' : $t('categoryPage.productsFound', products.length) }}
      </p>
    </div>
    <div v-if="loading" class="py-12 text-center">
      <p class="text-slate-500">{{ $t('categoryPage.loading') }}</p>
    </div>
    <div v-else class="grid gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
      <ProductCard
        v-for="product in products"
        :key="product.id"
        :product="product"
      />
    </div>
    <div v-if="!loading && products.length === 0" class="py-12 text-center">
      <p class="text-slate-500">{{ $t('categoryPage.noProducts') }}</p>
    </div>
  </section>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { useRoute } from 'vue-router'
import { useI18n } from 'vue-i18n'
import axiosTenant from '@theme-prism/axios'
import ProductCard from '@theme-prism/component/ProductCard.vue'

const { t } = useI18n()

const route = useRoute()
const slug = computed(() => route.params.slug)

const categoryName = ref('')
const products = ref([])
const loading = ref(true)

function mapProductFromApi(apiProduct) {
  const mainMedia = apiProduct.media?.find(m => m.is_main) || apiProduct.media?.[0]
  const imageUrl = mainMedia?.cdn_url || 'https://images.unsplash.com/photo-1558980664-2506fca6bfc2?auto=format&fit=crop&w=600&q=80'
  const qty = apiProduct.stock ?? apiProduct.quantity ?? apiProduct.variants?.reduce((sum, v) => sum + (v.quantity || v.stock || 0), 0) ?? 0
  return {
    id: apiProduct.id,
    title: apiProduct.name,
    imageUrl,
    articleNumber: apiProduct.sku || `ART-${apiProduct.id}`,
    price: parseFloat(apiProduct.price) || 0,
    oldPrice: apiProduct.compare_at_price ? parseFloat(apiProduct.compare_at_price) : null,
    available: qty > 0
  }
}

async function loadCategoryAndProducts() {
  if (!slug.value) {
    loading.value = false
    return
  }
  loading.value = true
  categoryName.value = ''
  products.value = []
  try {
    const catRes = await axiosTenant.get(`/categories/by-slug/${slug.value}`)
    const category = catRes.data?.category
    if (!category) {
      loading.value = false
      return
    }
    categoryName.value = category.name

    const productsRes = await axiosTenant.get('/products', {
      params: { category_id: category.id }
    })
    const list = productsRes.data?.products ?? []
    products.value = list.map(mapProductFromApi)
  } catch (err) {
    if (err.response?.status === 404) {
      categoryName.value = slug.value
      products.value = []
    } else {
      console.error('Category/products load failed:', err)
      categoryName.value = slug.value
      products.value = []
    }
  } finally {
    loading.value = false
  }
}

watch(slug, loadCategoryAndProducts, { immediate: true })

watch(categoryName, (name) => {
  document.title = name ? t('categoryPage.pageTitle', { name }) : t('categoryPage.pageTitleDefault')
}, { immediate: true })
</script>
