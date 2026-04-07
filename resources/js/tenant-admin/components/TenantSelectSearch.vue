<template>
  <div class="tenant-select-search relative">
    <input
      :id="inputId || undefined"
      type="text"
      class="tenant-input-shopify tenant-form-input-global w-full px-3 py-2"
      role="combobox"
      :aria-expanded="open"
      :aria-controls="listId"
      autocomplete="off"
      :placeholder="placeholder"
      :value="open ? filter : displayText"
      @focus="onFocus"
      @blur="onBlur"
      @input="onType"
      @keydown.down.prevent="moveHighlight(1)"
      @keydown.up.prevent="moveHighlight(-1)"
      @keydown.enter.prevent="selectHighlighted"
      @keydown.escape="close"
    />
    <ul
      v-show="open && filtered.length"
      :id="listId"
      class="tenant-select-search__list absolute z-[70] mt-0.5 max-h-52 w-full overflow-auto rounded-md border border-[#e3e3e3] bg-white py-1 shadow-lg"
      role="listbox"
    >
      <li
        v-for="(opt, idx) in filtered"
        :key="String(opt.value)"
        role="option"
        :aria-selected="idx === highlight"
        class="cursor-pointer px-3 py-2 text-[13px] text-[#303030]"
        :class="idx === highlight ? 'bg-[#f3f4f6]' : 'hover:bg-[#fafafa]'"
        @mousedown.prevent="choose(opt)"
      >
        {{ opt.label }}
      </li>
    </ul>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'

const props = defineProps({
  modelValue: { type: [String, Number], default: '' },
  options: { type: Array, default: () => [] },
  placeholder: { type: String, default: 'Type to search…' },
  inputId: { type: String, default: '' },
  labelForValue: { type: Function, default: null },
})

const emit = defineEmits(['update:modelValue'])

const open = ref(false)
const filter = ref('')
const highlight = ref(0)
const listId = `tenant-select-list-${Math.random().toString(36).slice(2, 9)}`

const normalized = computed(() =>
  (props.options || []).map((o) => {
    if (o && typeof o === 'object' && 'value' in o && 'label' in o) return o
    const v = o
    return { value: v, label: String(v) }
  }),
)

const displayText = computed(() => {
  const v = props.modelValue
  if (v === '' || v == null) return ''
  if (props.labelForValue) return props.labelForValue(v) || ''
  const hit = normalized.value.find((o) => String(o.value) === String(v))
  return hit ? hit.label : String(v)
})

const filtered = computed(() => {
  const q = (filter.value || '').trim().toLowerCase()
  if (!q) return normalized.value
  return normalized.value.filter((o) => String(o.label).toLowerCase().includes(q))
})

watch(filtered, (list) => {
  if (highlight.value >= list.length) highlight.value = 0
})

function onFocus() {
  open.value = true
  filter.value = ''
  highlight.value = 0
}

function onBlur() {
  setTimeout(() => {
    open.value = false
    filter.value = ''
  }, 180)
}

function onType(e) {
  open.value = true
  filter.value = e.target.value
  highlight.value = 0
}

function close() {
  open.value = false
  filter.value = ''
}

function choose(opt) {
  emit('update:modelValue', opt.value)
  close()
}

function moveHighlight(delta) {
  const n = filtered.value.length
  if (!n) return
  highlight.value = (highlight.value + delta + n) % n
}

function selectHighlighted() {
  const opt = filtered.value[highlight.value]
  if (opt) choose(opt)
}
</script>
