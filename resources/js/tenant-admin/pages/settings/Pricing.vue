<template>
  <div class="bg-gray-50 min-h-screen py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-6xl mx-auto text-center mb-12">
      <h1 class="text-3xl font-bold text-gray-900 mb-2">Choose your plan</h1>
      <p class="text-gray-500">Start free, upgrade anytime. No credit card required for trial.</p>
    </div>

    <!-- Plans Grid -->
    <div v-if="plans.length" class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-6xl mx-auto">
      <div
        v-for="plan in plans"
        :key="plan.id"
        class="bg-white border border-gray-200 rounded-xl shadow-sm p-6 flex flex-col justify-between hover:shadow-md transition"
      >
        <!-- Header -->
        <div>
          <h2 class="text-xl font-semibold text-gray-900 mb-1">{{ plan.name }}</h2>
          <p class="text-gray-500 text-sm mb-4">{{ plan.interval === 'year' ? 'Billed yearly' : 'Billed monthly' }}</p>

          <!-- Price -->
          <div class="mb-4">
            <div class="text-2xl font-bold text-gray-900">
              {{ plan.currency?.toUpperCase() }}{{ plan.price }}
              <span class="text-sm font-normal text-gray-500">/{{ plan.interval }}</span>
            </div>
          </div>

          <!-- Select button -->
          <button
            @click="selectPlan(plan)"
            class="w-full bg-gray-900 hover:bg-gray-800 text-white font-semibold py-2 rounded-md mt-2 transition"
          >
            Select {{ plan.name }}
          </button>

          <!-- Features -->
          <ul v-if="Array.isArray(plan.features)" class="mt-6 text-sm text-gray-700 text-left space-y-2">
            <li v-for="(feature, index) in plan.features" :key="index" class="flex items-start gap-2">
              <span class="text-green-500">✓</span>
              <span>{{ feature }}</span>
            </li>
          </ul>
        </div>
      </div>
    </div>

    <!-- Loading / Empty State -->
    <div v-else class="text-center text-gray-500 mt-12">
      <span v-if="loading">Loading plans...</span>
      <span v-else>No plans available.</span>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axiosTenant from '@/api/axiosTenant'
import { useRouter } from 'vue-router'
import Swal from 'sweetalert2'

const plans = ref([])
const loading = ref(true)
const router = useRouter()

onMounted(async () => {
  try {
    const { data } = await axiosTenant.get('/plans') // Laravel route
    if (data.success) {
      plans.value = data.plans.map(plan => ({
        ...plan,
        // Ensure features are parsed from JSON if stored as text
        features: typeof plan.features === 'string' ? JSON.parse(plan.features || '[]') : plan.features
      }))
    }
  } catch (error) {
    console.error('Failed to load plans:', error)
  } finally {
    loading.value = false
  }
})

async function selectPlan(plan) {
  try {
    const { data } = await axiosTenant.post('/create-checkout-session', {
      plan_id: plan.id
    })

    if (data.url) {
      window.location.href = data.url // redirect to Stripe Checkout
    } else {
      await Swal.fire({
        icon: 'error',
        title: 'Checkout',
        text: 'Failed to get checkout link.',
      })
    }
  } catch (error) {
    console.error('Error creating checkout session:', error)
    await Swal.fire({
      icon: 'error',
      title: 'Checkout',
      text: 'Something went wrong while redirecting to checkout.',
    })
  }
}

</script>

<style scoped>
/* Optional: smooth hover animation */
button {
  transition: all 0.2s ease;
}
</style>
