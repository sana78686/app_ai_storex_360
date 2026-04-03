<template>
  <div>
    <h1 class="mb-4 text-2xl font-bold tracking-tight text-gray-900 sm:text-3xl">
      {{ t('dashboardHome.title') }}
    </h1>
    <div
      class="rounded-2xl border border-gray-200/80 bg-white p-6 shadow-sm sm:p-8 md:p-10"
    >
      <p class="text-base leading-relaxed text-gray-600 sm:text-lg">
        {{ t('dashboardHome.lead') }}
      </p>
      <p class="mt-5 text-sm leading-relaxed text-gray-500 sm:text-base">
        {{ t('dashboardHome.sub') }}
      </p>
    </div>
  </div>
</template>

<script setup>
import { useI18n } from 'vue-i18n'

const { t } = useI18n()
</script>
