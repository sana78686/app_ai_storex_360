<template>
  <span class="tenant-field-tip" @mouseleave="open = false">
    <button
      type="button"
      class="tenant-field-tip__trigger"
      :aria-expanded="open"
      :aria-label="ariaLabel"
      @click.prevent="open = !open"
      @focus="open = true"
      @blur="onBlur"
    >
      <svg class="tenant-field-tip__icon" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
        <path
          fill-rule="evenodd"
          d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z"
          clip-rule="evenodd"
        />
      </svg>
    </button>
    <Transition name="tenant-tip-fade">
      <div v-show="open" class="tenant-field-tip__popover" role="tooltip">
        <p v-if="label" class="tenant-field-tip__label">{{ label }}</p>
        <p class="tenant-field-tip__text">{{ text }}</p>
      </div>
    </Transition>
  </span>
</template>

<script setup>
import { ref } from 'vue'

defineProps({
  text: { type: String, required: true },
  label: { type: String, default: 'On your storefront' },
  ariaLabel: { type: String, default: 'What customers see' },
})

const open = ref(false)

function onBlur(e) {
  requestAnimationFrame(() => {
    if (!e.currentTarget?.contains(document.activeElement)) {
      open.value = false
    }
  })
}
</script>
