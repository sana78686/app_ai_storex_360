<template>
  <div class="max-w-4xl mx-auto bg-white shadow-xl rounded-xl p-8">
    <h2 class="text-3xl font-semibold mb-6 border-b pb-3">
      {{ isEdit ? 'Edit Promotion / Coupon' : 'Create New Promotion' }}
    </h2>

    <form class="grid grid-cols-2 gap-6" @submit.prevent="submit">

      <!-- Promotion Code -->
      <div class="col-span-2">
        <label class="label">Promotion Code</label>
        <div class="flex gap-2">
          <input v-model="form.code" required class="input" placeholder="Enter code e.g., SAVE10"/>
          <button type="button" @click="generateCode" class="btn-secondary">Auto Generate</button>
        </div>
      </div>

      <!-- Type -->
      <div>
        <label class="label">Discount Type</label>
        <select v-model="form.type" class="input">
          <option value="percentage">Percentage</option>
          <option value="fixed">Fixed Amount</option>
          <option value="free_shipping">Free Shipping</option>
        </select>
      </div>

      <!-- Discount Value -->
      <div v-if="form.type !== 'free_shipping'">
        <label class="label">Value</label>
        <input type="number" v-model="form.value" class="input" placeholder="e.g., 10 (means 10%)"/>
      </div>

      <!-- Recurring -->
      <div>
        <label class="label">Recurring</label>
        <select v-model="form.recurring" class="input">
          <option value="once">One Time</option>
          <option value="repeating">Repeating</option>
          <option value="forever">Forever</option>
        </select>
      </div>

      <div v-if="form.recurring === 'repeating'">
        <label class="label">For how many months?</label>
        <input type="number" v-model="form.repeating_months" class="input"/>
      </div>

      <!-- Apply To -->
      <!-- Applies To (Categories) -->
<!-- Applies To (Categories with Checkboxes) -->
<div class="col-span-2">
  <label class="label mb-2">Applies To Categories</label>

  <div class="grid grid-cols-2 gap-2 max-h-40 overflow-y-auto border p-3 rounded">
    <div
      v-for="cat in categories"
      :key="cat.id"
      class="flex items-center gap-2"
    >
      <input
        type="checkbox"
        :value="cat.id"
        v-model="form.applies_to"
        class="h-4 w-4"
      />
      <label>{{ cat.name }}</label>
    </div>
  </div>
</div>



      <!-- Minimum Spend -->
      <div>
        <label class="label">Minimum Order Amount</label>
        <input type="number" v-model="form.min_order_amount" class="input" placeholder="Optional"/>
      </div>

      <!-- Maximum Discount -->
      <div>
        <label class="label">Maximum Discount (Optional)</label>
        <input type="number" v-model="form.max_discount" class="input"/>
      </div>

      <!-- Start Date -->
      <div>
        <label class="label">Start Date</label>
        <input type="datetime-local" v-model="form.start_date" class="input"/>
      </div>

      <!-- End Date -->
      <div>
        <label class="label">End Date</label>
        <input type="datetime-local" v-model="form.end_date" class="input"/>
      </div>

      <!-- Status -->
      <div class="flex items-center gap-2 mt-4">
        <input type="checkbox" v-model="form.active" class="h-4 w-4"/>
        <label class="font-medium">Active</label>
      </div>

      <!-- Usage Restrictions -->
      <div class="col-span-2 border-t pt-4 mt-4">
        <h3 class="font-semibold text-lg mb-3">Usage Restrictions</h3>

        <div class="grid grid-cols-2 gap-4">

          <div class="flex gap-2">
            <input type="checkbox" v-model="form.first_order_only"/>
            <label>First order only</label>
          </div>

          <div class="flex gap-2">
            <input type="checkbox" v-model="form.logged_in_only"/>
            <label>Only for logged-in customers</label>
          </div>

          <div class="flex gap-2">
            <input type="checkbox" v-model="form.apply_once_per_order"/>
            <label>Apply once per order</label>
          </div>

          <div class="flex gap-2">
            <input type="checkbox" v-model="form.apply_only_if_products_match"/>
            <label>Customer must have all items in cart</label>
          </div>

        </div>
      </div>

      <!-- Usage Limits -->
      <div class="col-span-2 border-t pt-4 mt-4">
        <h3 class="font-semibold text-lg mb-3">Usage Limits</h3>

        <div class="grid grid-cols-2 gap-6">

          <div>
            <label class="label">Usage Limit Overall</label>
            <input type="number" v-model="form.usage_limit" class="input" placeholder="Unlimited if empty"/>
          </div>

          <div>
            <label class="label">Usage Per Customer</label>
            <input type="number" v-model="form.usage_per_customer" class="input" placeholder="Unlimited if empty"/>
          </div>

        </div>
      </div>

      <!-- Submit Buttons -->
      <div class="col-span-2 flex justify-end gap-3 mt-8">
        <button type="button" @click="$emit('cancel')" class="btn-secondary">Cancel</button>
        <button type="submit" class="btn-primary">{{ isEdit ? 'Update' : 'Save' }}</button>
      </div>

    </form>
  </div>
</template>

<script setup>
import { reactive, computed, watch, ref, onMounted } from "vue"
import axiosTenant from "@/api/axiosTenant"

const props = defineProps({
  couponId: Number
})

const emit = defineEmits(["saved", "cancel"])

const loading = ref(false)
const categories = ref([])

const form = reactive({
  code: "",
  type: "percentage",
  value: null,
  recurring: "once",
  repeating_months: null,
  applies_to: [],  // 👉 now stores category IDs
  min_order_amount: null,
  max_discount: null,
  start_date: null,
  end_date: null,
  active: true,
  first_order_only: false,
  logged_in_only: false,
  apply_once_per_order: false,
  apply_only_if_products_match: false,
  usage_limit: null,
  usage_per_customer: null,
})

// ✅ Fetch Categories
const fetchCategories = async () => {
  try {
    const res = await axiosTenant.get('/categories')
    categories.value = res.data.categories || []
  } catch (e) {
    console.error('❌ Error fetching categories:', e)
  }
}
function generateCode() { form.code = "PROMO" + Math.floor(Math.random() * 9999) }
// Edit check
const isEdit = computed(() => props.couponId !== null)

// Fetch coupon
async function fetchCoupon() {
  if (!isEdit.value) return;

  const res = await axiosTenant.get(`/coupons/${props.couponId}`);

  // Assign all fields
  Object.assign(form, res.data.data);

  // Extract selected category IDs
 form.applies_to = res.data.data.categories.map(cat => cat.id);

}

async function submit() {
  if (isEdit.value) {
    await axiosTenant.put(`/coupons/${props.couponId}`, form)
  } else {
    await axiosTenant.post(`/coupons`, form)
  }
  emit("saved")
}
// Mount
onMounted(() => {
  fetchCoupon()
  fetchCategories()
})

// Watch dynamic ID
watch(() => props.couponId, fetchCoupon)
</script>



<style scoped>
.label {
  @apply block text-sm font-medium mb-1;
}
.input {
  @apply w-full border rounded px-3 py-2 focus:ring focus:ring-blue-200 focus:border-blue-500;
}
.btn-primary {
  @apply px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700;
}
.btn-secondary {
  @apply px-6 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300;
}
</style>
