<template>
  <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
    <h2 v-if="title" class="mb-4 text-lg font-semibold text-slate-900">
      {{ title }}
    </h2>
    <div class="space-y-3">
      <slot />
    </div>
  </div>
</template>

<script setup>
defineProps({
  title: {
    type: String,
    default: ''
  }
})
</script>

