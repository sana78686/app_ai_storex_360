<template>
  <div>
    <h2>Coupons</h2>

    <button @click="$emit('add')">Add Coupon</button>

    <table border="1" cellpadding="8" cellspacing="0" width="100%">
      <thead>
        <tr>
          <th>Code</th>
          <th>Type</th>
          <th>Discount</th>
          <th>Min Order</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="coupon in coupons.data" :key="coupon.id">
          <td>{{ coupon.code }}</td>
          <td>{{ coupon.type }}</td>
          <td>{{ coupon.discount_amount ?? '-' }}</td>
          <td>{{ coupon.min_order_amount ?? '-' }}</td>
          <td>{{ coupon.status ? 'Active' : 'Inactive' }}</td>
          <td>
            <!-- Emit 'edit' event with coupon id -->
            <button @click="$emit('edit', coupon.id)">Edit</button>
            <button @click="deleteCoupon(coupon.id)">Delete</button>
          </td>
        </tr>
      </tbody>
    </table>

    <div v-if="coupons.meta">
      <button :disabled="!coupons.links.prev" @click="fetchCoupons(coupons.meta.current_page - 1)">Prev</button>
      Page {{ coupons.meta.current_page }} of {{ coupons.meta.last_page }}
      <button :disabled="!coupons.links.next" @click="fetchCoupons(coupons.meta.current_page + 1)">Next</button>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axiosTenant from '@/api/axiosTenant'

const coupons = ref({ data: [], meta: {}, links: {} })

async function fetchCoupons(page = 1) {
  try {
    const response = await axiosTenant.get(`/coupons?page=${page}`)
    coupons.value = response.data
  } catch (err) {
    alert('Failed to fetch coupons: ' + err.message)
  }
}

async function deleteCoupon(id) {
  if (!confirm('Are you sure you want to delete this coupon?')) return

  try {
    await axiosTenant.delete(`/coupons/${id}`)
    alert('Coupon deleted!')
    fetchCoupons(coupons.value.meta.current_page)
  } catch (err) {
    alert('Failed to delete coupon: ' + (err.response?.data?.message || err.message))
  }
}

onMounted(() => fetchCoupons())
</script>
