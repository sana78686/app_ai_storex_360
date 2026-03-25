<template>
  <section class="mx-auto max-w-md px-4 py-12">
    <!-- Step 1: Set password (guest only, when token present) -->
    <div
      v-if="setPasswordToken && step === 'password'"
      class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm"
    >
      <h2 class="text-lg font-semibold text-slate-900 mb-2">{{ $t('checkoutPage.setPasswordTitle') }}</h2>
      <p class="text-sm text-slate-600 mb-4">{{ $t('orderSuccessPage.setPasswordHeading') }}</p>
      <form @submit.prevent="submitPassword" class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">{{ $t('loginPage.passwordLabel') }}</label>
          <input
            v-model="passwordForm.password"
            type="password"
            minlength="6"
            class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-brand focus:ring-1 focus:ring-brand"
            :placeholder="$t('checkoutPage.setPasswordPlaceholder')"
          />
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">{{ $t('checkoutPage.setPasswordConfirm') }}</label>
          <input
            v-model="passwordForm.password_confirmation"
            type="password"
            minlength="6"
            class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-brand focus:ring-1 focus:ring-brand"
            :placeholder="$t('checkoutPage.setPasswordPlaceholder')"
          />
          <p v-if="passwordError" class="mt-1 text-sm text-red-600">{{ passwordError }}</p>
        </div>
        <div class="flex gap-3">
          <button
            type="submit"
            :disabled="submitting"
            class="flex-1 rounded-full bg-brand py-2.5 text-sm font-semibold text-white hover:bg-brand-dark disabled:opacity-50"
          >
            {{ submitting ? '…' : $t('checkoutPage.setPasswordSubmit') }}
          </button>
          <button
            type="button"
            @click="skipPassword"
            class="rounded-full border border-slate-300 py-2.5 px-4 text-sm font-medium text-slate-700 hover:bg-slate-50"
          >
            {{ $t('checkoutPage.setPasswordSkip') }}
          </button>
        </div>
      </form>
    </div>

    <!-- Step 2: Thank you -->
    <div
      v-else
      class="rounded-lg border border-slate-200 bg-white p-8 shadow-sm text-center"
    >
      <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-green-100 text-green-600">
        <Check class="h-8 w-8" />
      </div>
      <h1 class="text-2xl font-bold text-slate-900 mb-2">{{ $t('orderSuccessPage.thankYouTitle') }}</h1>
      <p class="text-slate-600 mb-4">{{ $t('orderSuccessPage.thankYouMessage') }}</p>
      <p v-if="orderNumber" class="text-sm font-medium text-slate-700 mb-6">
        {{ $t('orderSuccessPage.orderNumber') }}: <span class="text-brand">{{ orderNumber }}</span>
      </p>
      <p v-if="redirectIn > 0" class="text-sm text-slate-500 mb-6">
        {{ $t('orderSuccessPage.redirectIn', { seconds: redirectIn }) }}
      </p>
      <div class="flex flex-col sm:flex-row gap-3 justify-center">
        <router-link
          to="/"
          class="inline-flex justify-center rounded-full bg-brand px-6 py-3 text-sm font-semibold text-white hover:bg-brand-dark"
        >
          {{ $t('orderSuccessPage.backToShop') }}
        </router-link>
        <router-link
          to="/konto/bestellungen"
          class="inline-flex justify-center rounded-full border-2 border-slate-300 px-6 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50"
        >
          {{ $t('orderSuccessPage.viewOrders') }}
        </router-link>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref, onMounted, onUnmounted, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useI18n } from 'vue-i18n'
import axiosTenant from '@theme-prism/axios'
import { Check } from 'lucide-vue-next'

const route = useRoute()
const router = useRouter()
const { t } = useI18n()

const step = ref('password')
const setPasswordToken = ref(null)
const orderNumber = ref('')
const passwordForm = ref({ password: '', password_confirmation: '' })
const passwordError = ref('')
const submitting = ref(false)
const redirectIn = ref(5)
let countdown = null

function startRedirectCountdown() {
  if (countdown) clearInterval(countdown)
  redirectIn.value = 5
  countdown = setInterval(() => {
    redirectIn.value--
    if (redirectIn.value <= 0) {
      if (countdown) clearInterval(countdown)
      router.replace('/')
    }
  }, 1000)
}

onMounted(() => {
  setPasswordToken.value = route.query.token || null
  orderNumber.value = route.query.order_number || ''
  if (!setPasswordToken.value) {
    step.value = 'thankyou'
    startRedirectCountdown()
  }
})

watch(step, (newStep) => {
  if (newStep === 'thankyou') {
    startRedirectCountdown()
  }
})

onUnmounted(() => {
  if (countdown) clearInterval(countdown)
})

function skipPassword() {
  step.value = 'thankyou'
}

async function submitPassword() {
  passwordError.value = ''
  if (passwordForm.value.password.length < 6) {
    passwordError.value = t('registerPage.passwordMinLength')
    return
  }
  if (passwordForm.value.password !== passwordForm.value.password_confirmation) {
    passwordError.value = t('registerPage.passwordsMismatch')
    return
  }
  submitting.value = true
  try {
    const { data } = await axiosTenant.post('/checkout/set-password', {
      set_password_token: setPasswordToken.value,
      password: passwordForm.value.password,
      password_confirmation: passwordForm.value.password_confirmation
    })
    if (data.success) {
      step.value = 'thankyou'
    } else {
      passwordError.value = data.message || 'Could not set password.'
    }
  } catch (err) {
    passwordError.value = err.response?.data?.message || err.message || 'Could not set password.'
  } finally {
    submitting.value = false
  }
}
</script>
