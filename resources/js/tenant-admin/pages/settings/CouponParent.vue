<template>
  <div>
    <CouponList v-if="!showForm" @add="handleAdd" @edit="handleEdit"/>
    <CouponForm v-else :couponId="editingId" @saved="handleSaved" @cancel="handleCancel"/>
  </div>
</template>


<script setup>
import { ref } from 'vue'
import CouponList from './CouponList.vue'
import CouponForm from './CouponForm.vue'

const showForm = ref(false)
const editingId = ref(null)

// Show form in "Add" mode
function handleAdd() {
  editingId.value = null
  showForm.value = true
}

// Show form in "Edit" mode
function handleEdit(id) {
  editingId.value = id
  showForm.value = true
}

// After save, go back to list
function handleSaved() {
  showForm.value = false
}

// Optional: cancel button in form
function handleCancel() {
  showForm.value = false
}
</script>
