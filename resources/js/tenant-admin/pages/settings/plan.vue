<template>
  <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6 max-w-auto mx-auto">
    <h2 class="text-lg font-semibold flex items-center gap-2 mb-4">
      <i class="fas fa-receipt text-gray-500"></i>
      Plan
    </h2>

    <!-- If subscription is loaded -->
    <div
      v-if="subscription"
      class="border border-gray-200 rounded-lg p-4 flex flex-col sm:flex-row sm:items-center sm:justify-between"
    >
      <div class="flex items-center  gap-2  sm:mb-0">
        <span class="font-medium text-gray-700">{{ subscription.plan_name }}</span>

        <span
           class="bg-green-100 text-green-800 text-sm font-medium px-3 py-1 rounded-full"
        >
          {{ trialDaysRemaining }} days remaining
        </span>

        <!-- <span
          v-else
          class="bg-gray-100 text-gray-800 text-sm font-medium px-3 py-1 rounded-full"
        >
          Active
        </span> -->
      </div>

      <div class="flex gap-2 justify-end">
        <!-- Cancel Trial -->
        <button
          v-if="subscription.status === 'trial'"
          @click="cancelTrial"
          class="px-4 py-2 text-sm font-medium text-red-600 border border-red-200 rounded-md hover:bg-red-50 transition"
        >
          Cancel trial
        </button>

        <!-- Choose Plan -->
        <button
          @click="choosePlan"
          class="px-4 py-2 text-sm font-medium text-white bg-gray-900 rounded-md hover:bg-gray-800 transition"
        >
          Choose plan
        </button>

        <!-- Renew (for active) -->
        <!-- <button
          v-if="subscription.status === 'active'"
          @click="renewPlan"
          class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-500 transition"
        >
          Renew plan
        </button> -->
      </div>
    </div>

    <!-- Loading -->
    <div v-else class="text-gray-500 text-sm mt-2">
      Loading your subscription details...
    </div>

    <p class="text-sm text-gray-500 mt-4">
      View the
      <a href="#" class="text-blue-600 hover:underline">terms of service</a>
      and
      <a href="#" class="text-blue-600 hover:underline">privacy policy</a>.
    </p>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axiosTenant from '@/api/axiosTenant'

const subscription = ref(null)

onMounted(async () => {
  try {
    const { data } = await axiosTenant.get('/my-subscription')
    subscription.value = data
  } catch (error) {
    console.error('Failed to load subscription:', error)
  }
})

// ✅ Compute remaining trial days
const trialDaysRemaining = computed(() => {
  if (!subscription.value || !subscription.value.ends_at) return 0
  const end = new Date(subscription.value.ends_at)
  const today = new Date()
  const diff = Math.ceil((end - today) / (1000 * 60 * 60 * 24))
  return diff > 0 ? diff : 0
})

// ✅ Renew plan
async function renewPlan() {
  try {
    const { data } = await axiosTenant.post('/subscription/renew')
    if (data.checkout_url) {
      window.location.href = data.checkout_url
    } else {
      alert(data.message || 'Subscription renewed successfully')
      location.reload()
    }
  } catch (error) {
    alert('Renewal failed.')
  }
}

// ✅ Cancel trial
async function cancelTrial() {
  if (confirm('Are you sure you want to cancel your trial?')) {
    try {
      await axiosTenant.post('/subscription/cancel')
      alert('Trial canceled.')
      location.reload()
    } catch (error) {
      alert('Failed to cancel trial.')
    }
  }
}

// ✅ Choose plan
function choosePlan() {
  window.location.href = '/dashboard/pricing' // Redirect to your pricing page
}

function formatDate(date) {
  return new Date(date).toLocaleDateString()
}
</script>
