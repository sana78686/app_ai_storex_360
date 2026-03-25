<template>
  <section class="mx-auto max-w-md px-4 py-12 text-center">
    <div class="rounded-lg border border-slate-200 bg-white p-8 shadow-sm">
      <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-green-100 text-green-600">
        <Check class="h-8 w-8" />
      </div>
      <h1 class="text-2xl font-bold text-slate-900 mb-2">{{ $t('orderSuccessPage.thankYouTitle') }}</h1>
      <p class="text-slate-600 mb-6">{{ $t('orderSuccessPage.thankYouMessage') }}</p>
      <p v-if="redirectIn > 0" class="text-sm text-slate-500 mb-6">
        {{ $t('orderSuccessPage.redirectIn', { seconds: redirectIn }) }}
      </p>
      <router-link
        to="/"
        class="inline-flex justify-center rounded-full bg-brand px-6 py-3 text-sm font-semibold text-white hover:bg-brand-dark"
      >
        {{ $t('orderSuccessPage.backToShop') }}
      </router-link>
    </div>
  </section>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import { Check } from 'lucide-vue-next'

const router = useRouter()
const redirectIn = ref(5)
let countdown = null

onMounted(() => {
  countdown = setInterval(() => {
    redirectIn.value--
    if (redirectIn.value <= 0) {
      clearInterval(countdown)
      router.replace('/')
    }
  }, 1000)
})

onUnmounted(() => {
  if (countdown) clearInterval(countdown)
})
</script>
