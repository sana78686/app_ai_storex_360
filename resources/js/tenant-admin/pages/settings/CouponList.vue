<template>
  <div class="coupon-list space-y-5 text-[0.9375rem] leading-relaxed text-gray-700">
    <div class="flex flex-wrap items-center justify-between gap-3">
      <p class="text-[0.9375rem] text-gray-600">Manage discount codes for your store.</p>
      <button type="button" class="tenant-btn-submit tenant-btn-sm" @click="$emit('add')">Add coupon</button>
    </div>

    <div v-if="coupons.data?.length" class="tenant-data-table-wrap">
      <table class="tenant-data-table">
        <thead>
          <tr>
            <th>Code</th>
            <th>Type</th>
            <th>Discount</th>
            <th>Min order</th>
            <th>Status</th>
            <th class="min-w-[140px]">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="coupon in coupons.data" :key="coupon.id">
            <td class="font-medium text-gray-900">{{ coupon.code }}</td>
            <td class="capitalize">{{ coupon.type }}</td>
            <td>{{ coupon.discount_amount ?? '—' }}</td>
            <td>{{ coupon.min_order_amount ?? '—' }}</td>
            <td>
              <span
                class="tenant-badge"
                :class="coupon.status ? 'tenant-badge--success' : 'tenant-badge--neutral'"
              >
                {{ coupon.status ? 'Active' : 'Inactive' }}
              </span>
            </td>
            <td>
              <div class="flex flex-wrap gap-2">
                <button type="button" class="tenant-btn-outline-accent tenant-btn-sm" @click="$emit('edit', coupon.id)">
                  Edit
                </button>
                <button type="button" class="tenant-btn-danger tenant-btn-sm" @click="deleteCoupon(coupon.id)">
                  Delete
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <div
      v-else
      class="rounded-xl border border-dashed border-gray-200 bg-gray-50/50 px-4 py-12 text-center text-gray-500"
    >
      No coupons yet. Add one to get started.
    </div>

    <div
      v-if="coupons.meta && coupons.meta.last_page > 1"
      class="flex flex-wrap items-center justify-center gap-3 text-sm text-gray-600"
    >
      <button
        type="button"
        class="tenant-btn-secondary tenant-btn-sm"
        :disabled="!coupons.links.prev"
        @click="fetchCoupons(coupons.meta.current_page - 1)"
      >
        Previous
      </button>
      <span class="font-medium">Page {{ coupons.meta.current_page }} of {{ coupons.meta.last_page }}</span>
      <button
        type="button"
        class="tenant-btn-secondary tenant-btn-sm"
        :disabled="!coupons.links.next"
        @click="fetchCoupons(coupons.meta.current_page + 1)"
      >
        Next
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axiosTenant from '@/api/axiosTenant'
import Swal from 'sweetalert2'

const coupons = ref({ data: [], meta: {}, links: {} })

async function fetchCoupons(page = 1) {
  try {
    const response = await axiosTenant.get(`/coupons?page=${page}`)
    coupons.value = response.data
  } catch (err) {
    await Swal.fire({
      icon: 'error',
      title: 'Coupons',
      text: 'Failed to fetch coupons: ' + err.message,
    })
  }
}

async function deleteCoupon(id) {
  const r = await Swal.fire({
    icon: 'warning',
    title: 'Delete coupon?',
    text: 'Are you sure you want to delete this coupon?',
    showCancelButton: true,
    confirmButtonText: 'Yes, delete',
    cancelButtonText: 'Cancel',
  })
  if (!r.isConfirmed) return

  try {
    await axiosTenant.delete(`/coupons/${id}`)
    await Swal.fire({ icon: 'success', title: 'Deleted', text: 'Coupon deleted.' })
    fetchCoupons(coupons.value.meta.current_page)
  } catch (err) {
    await Swal.fire({
      icon: 'error',
      title: 'Delete failed',
      text: err.response?.data?.message || err.message,
    })
  }
}

onMounted(() => fetchCoupons())
</script>
