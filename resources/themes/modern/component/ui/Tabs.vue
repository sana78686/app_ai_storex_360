<template>
  <div :class="containerClass">
    <!-- Top Bar Tabs -->
    <div
      v-if="variant === 'top'"
      class="border-b border-slate-200"
    >
      <nav class="flex space-x-1 overflow-x-auto" aria-label="Tabs">
        <button
          v-for="(tab, index) in tabs"
          :key="index"
          @click="activeTab = index"
          :class="[
            'whitespace-nowrap px-4 py-3 text-sm font-medium border-b-2 transition-colors',
            'focus:outline-none focus:ring-2 focus:ring-brand focus:ring-offset-2',
            activeTab === index
              ? 'border-brand text-brand'
              : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300'
          ]"
          :aria-selected="activeTab === index"
          :aria-controls="`tab-panel-${index}`"
        >
          <slot name="tab" :tab="tab" :index="index" :active="activeTab === index">
            {{ tab.label }}
          </slot>
        </button>
      </nav>
    </div>

    <!-- Sidebar Tabs -->
    <div v-else-if="variant === 'sidebar'" class="flex gap-6">
      <aside class="w-64 flex-shrink-0">
        <nav class="space-y-1" aria-label="Tabs">
          <button
            v-for="(tab, index) in tabs"
            :key="index"
            @click="activeTab = index"
            :class="[
              'w-full text-left px-4 py-3 rounded-lg text-sm font-medium transition-colors',
              'focus:outline-none focus:ring-2 focus:ring-brand focus:ring-offset-2',
              activeTab === index
                ? 'bg-brand text-white'
                : 'text-slate-700 hover:bg-slate-100'
            ]"
            :aria-selected="activeTab === index"
            :aria-controls="`tab-panel-${index}`"
          >
            <slot name="tab" :tab="tab" :index="index" :active="activeTab === index">
              {{ tab.label }}
            </slot>
          </button>
        </nav>
      </aside>

      <div class="flex-1">
        <div
          v-for="(tab, index) in tabs"
          :key="index"
          :id="`tab-panel-${index}`"
          v-show="activeTab === index"
          :class="panelClass"
        >
          <slot name="panel" :tab="tab" :index="index" :active="activeTab === index">
            {{ tab.content }}
          </slot>
        </div>
      </div>
    </div>

    <!-- Default: Top Bar with Content Below -->
    <div v-else>
      <div class="border-b border-slate-200">
        <nav class="flex space-x-1 overflow-x-auto" aria-label="Tabs">
          <button
            v-for="(tab, index) in tabs"
            :key="index"
            @click="activeTab = index"
            :class="[
              'whitespace-nowrap px-4 py-3 text-sm font-medium border-b-2 transition-colors',
              'focus:outline-none focus:ring-2 focus:ring-brand focus:ring-offset-2',
              activeTab === index
                ? 'border-brand text-brand'
                : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300'
            ]"
            :aria-selected="activeTab === index"
            :aria-controls="`tab-panel-${index}`"
          >
            <slot name="tab" :tab="tab" :index="index" :active="activeTab === index">
              {{ tab.label }}
            </slot>
          </button>
        </nav>
      </div>

      <div class="mt-4">
        <div
          v-for="(tab, index) in tabs"
          :key="index"
          :id="`tab-panel-${index}`"
          v-show="activeTab === index"
          :class="panelClass"
        >
          <slot name="panel" :tab="tab" :index="index" :active="activeTab === index">
            {{ tab.content }}
          </slot>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue'

const props = defineProps({
  tabs: {
    type: Array,
    required: true
  },
  defaultTab: {
    type: Number,
    default: 0
  },
  variant: {
    type: String,
    default: 'default', // 'default', 'top', 'sidebar'
    validator: (value) => ['default', 'top', 'sidebar'].includes(value)
  },
  containerClass: {
    type: String,
    default: ''
  },
  panelClass: {
    type: String,
    default: ''
  }
})

const emit = defineEmits(['tab-change'])

const activeTab = ref(props.defaultTab)

watch(activeTab, (newTab) => {
  emit('tab-change', newTab)
})

defineExpose({
  activeTab,
  setTab: (index) => {
    activeTab.value = index
  }
})
</script>
