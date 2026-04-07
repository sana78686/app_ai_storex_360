<template>
  <div class="coupon-form mx-auto max-w-3xl">
    <p class="mb-3 text-[0.9375rem] leading-relaxed text-gray-600">
      {{ isEdit ? 'Update promotion details below.' : 'Create a discount code for your storefront.' }}
    </p>

    <form class="tenant-settings-stack" @submit.prevent="submit">
      <div class="flex flex-col gap-2 sm:flex-row sm:items-start">
        <div class="tenant-float-field min-w-0 flex-1">
          <input id="coupon-code" v-model="form.code" required type="text" placeholder=" " />
          <label for="coupon-code">Promotion code</label>
        </div>
        <button type="button" class="tenant-btn-secondary tenant-btn-sm shrink-0 sm:mt-0" @click="generateCode">
          Auto generate
        </button>
      </div>

      <div class="tenant-float-field is-always-floated">
        <select id="coupon-type" v-model="form.type">
          <option value="percentage">Percentage</option>
          <option value="fixed">Fixed amount</option>
          <option value="free_shipping">Free shipping</option>
        </select>
        <label for="coupon-type">Discount type</label>
      </div>

      <div v-if="form.type !== 'free_shipping'" class="tenant-float-field">
        <input id="coupon-value" v-model="form.value" type="number" placeholder=" " />
        <label for="coupon-value">Value (e.g. 10 for 10%)</label>
      </div>

      <div class="tenant-float-field is-always-floated">
        <select id="coupon-recurring" v-model="form.recurring">
          <option value="once">One time</option>
          <option value="repeating">Repeating</option>
          <option value="forever">Forever</option>
        </select>
        <label for="coupon-recurring">Recurring</label>
      </div>

      <div v-if="form.recurring === 'repeating'" class="tenant-float-field">
        <input id="coupon-months" v-model="form.repeating_months" type="number" placeholder=" " />
        <label for="coupon-months">Months (repeating)</label>
      </div>

      <div class="tenant-settings-field-group">
        <span class="tenant-settings-file__label">Applies to categories</span>
        <div class="max-h-40 overflow-y-auto rounded-lg border border-gray-200 p-3">
          <div
            v-for="cat in categories"
            :key="cat.id"
            class="flex items-center gap-2 py-0.5"
          >
            <input
              v-model="form.applies_to"
              type="checkbox"
              :value="cat.id"
              class="h-4 w-4 rounded border-gray-300 text-[#275a19] focus:ring-[#275a19]"
            />
            <label class="text-sm text-gray-700">{{ cat.name }}</label>
          </div>
        </div>
      </div>

      <div class="tenant-float-field">
        <input id="coupon-min" v-model="form.min_order_amount" type="number" placeholder=" " />
        <label for="coupon-min">Minimum order amount (optional)</label>
      </div>

      <div class="tenant-float-field">
        <input id="coupon-maxd" v-model="form.max_discount" type="number" placeholder=" " />
        <label for="coupon-maxd">Maximum discount (optional)</label>
      </div>

      <div class="tenant-float-field is-always-floated">
        <input id="coupon-start" v-model="form.start_date" type="datetime-local" />
        <label for="coupon-start">Start date</label>
      </div>

      <div class="tenant-float-field is-always-floated">
        <input id="coupon-end" v-model="form.end_date" type="datetime-local" />
        <label for="coupon-end">End date</label>
      </div>

      <label class="flex cursor-pointer items-center gap-2 text-sm font-medium text-gray-800">
        <input v-model="form.active" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-[#275a19] focus:ring-[#275a19]" />
        <span>Active</span>
      </label>

      <div class="border-t border-gray-100 pt-3">
        <h3 class="mb-2 text-sm font-bold uppercase tracking-wide text-gray-500">Usage restrictions</h3>
        <div class="tenant-settings-stack">
          <label class="flex items-center gap-2 text-sm text-gray-700">
            <input v-model="form.first_order_only" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-[#275a19]" />
            First order only
          </label>
          <label class="flex items-center gap-2 text-sm text-gray-700">
            <input v-model="form.logged_in_only" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-[#275a19]" />
            Logged-in customers only
          </label>
          <label class="flex items-center gap-2 text-sm text-gray-700">
            <input v-model="form.apply_once_per_order" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-[#275a19]" />
            Apply once per order
          </label>
          <label class="flex items-center gap-2 text-sm text-gray-700">
            <input v-model="form.apply_only_if_products_match" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-[#275a19]" />
            Customer must have all cart items
          </label>
        </div>
      </div>

      <div class="border-t border-gray-100 pt-3">
        <h3 class="mb-2 text-sm font-bold uppercase tracking-wide text-gray-500">Usage limits</h3>
        <div class="tenant-settings-stack">
          <div class="tenant-float-field">
            <input id="coupon-ulimit" v-model="form.usage_limit" type="number" placeholder=" " />
            <label for="coupon-ulimit">Usage limit overall (optional)</label>
          </div>
          <div class="tenant-float-field">
            <input id="coupon-ulimit-c" v-model="form.usage_per_customer" type="number" placeholder=" " />
            <label for="coupon-ulimit-c">Usage per customer (optional)</label>
          </div>
        </div>
      </div>

      <div class="flex flex-wrap justify-end gap-2 border-t border-gray-100 pt-3">
        <button type="button" class="tenant-btn-secondary" @click="$emit('cancel')">Cancel</button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { reactive, computed, watch, ref, onMounted, onUnmounted } from 'vue'
import axiosTenant from '@/api/axiosTenant'
import { injectSettingsStickySave } from '@tenant/settings/settingsStickyContext'

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
onMounted(() => {
  fetchCoupon()
  fetchCategories()
  settingsSticky?.setSave(() => submit())
})

onUnmounted(() => {
  settingsSticky?.clearSave()
})

watch(() => props.couponId, fetchCoupon)
</script>



