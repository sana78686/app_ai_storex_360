<template>
  <nav
    v-if="items.length > 0"
    class="border-b border-slate-200 bg-white"
    aria-label="Breadcrumb"
  >
    <div class="mx-auto max-w-7xl px-4 py-2 sm:py-3">
      <ol class="flex items-center space-x-2 text-xs sm:text-sm text-slate-600 overflow-x-auto">
        <li
          v-for="(item, index) in items"
          :key="index"
          class="flex items-center"
        >
          <router-link
            v-if="item.to && index < items.length - 1"
            :to="item.to"
            class="hover:text-brand transition-colors whitespace-nowrap"
          >
            {{ item.label }}
          </router-link>
          <span
            v-else
            :class="[
              'whitespace-nowrap',
              index === items.length - 1 ? 'text-slate-900 font-medium' : ''
            ]"
          >
            {{ item.label }}
          </span>
          <ChevronRight
            v-if="index < items.length - 1"
            class="mx-2 h-4 w-4 flex-shrink-0 text-slate-400"
          />
        </li>
      </ol>
    </div>
  </nav>
</template>

<script setup>
import { ChevronRight } from 'lucide-vue-next'

defineProps({
  items: {
    type: Array,
    required: true,
    validator: (items) => {
      return items.every(item => item.label && typeof item.label === 'string')
    }
  }
})
</script>
