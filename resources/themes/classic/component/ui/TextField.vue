<template>
  <label :class="['block', className]">
    <span v-if="label" class="mb-1 block text-sm font-medium text-slate-700">
      {{ label }}
    </span>
    <input
      :type="type"
      :placeholder="placeholder"
      :required="required"
      :value="modelValue"
      @input="$emit('update:modelValue', $event.target.value)"
      :class="[
        'mt-1 h-10 w-full rounded-md border border-slate-300 px-3 text-sm outline-none transition hover:border-brand/60 focus:border-brand focus:ring-1 focus:ring-brand',
        error && 'border-red-300 focus:border-red-500 focus:ring-red-500'
      ]"
    />
    <span v-if="error" class="mt-1 block text-xs text-red-600">
      {{ error }}
    </span>
  </label>
</template>

<script setup>
defineProps({
  label: {
    type: String,
    default: ''
  },
  type: {
    type: String,
    default: 'text'
  },
  placeholder: {
    type: String,
    default: ''
  },
  required: {
    type: Boolean,
    default: false
  },
  error: {
    type: String,
    default: ''
  },
  className: {
    type: String,
    default: ''
  },
  modelValue: {
    type: String,
    default: ''
  }
})

defineEmits(['update:modelValue'])
</script>

