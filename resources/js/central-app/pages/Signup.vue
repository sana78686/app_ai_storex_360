<template>
  <div class="central-auth-page">
    <div class="central-auth-slate-bg" aria-hidden="true" />
    <div class="central-auth-blobs" aria-hidden="true" />
    <div class="central-auth-texture" aria-hidden="true" />

    <div class="relative z-10 mb-2 central-auth-fade-in">
      <img :src="logoUrl" :alt="`${appName} logo`" class="h-24 w-auto object-contain" />
    </div>

    <div class="central-auth-card">
      <div class="central-auth-stripe" aria-hidden="true" />
      <div class="central-auth-card-body">
        <div class="mb-8 text-center">
          <h2 v-if="selectedPlanId" class="mb-2 text-2xl font-extrabold tracking-tight text-slate-900">
            Complete Your Registration
          </h2>
          <h2 v-else class="mb-2 text-2xl font-extrabold tracking-tight text-slate-900">
            Create Your Account
          </h2>

          <p
            v-if="selectedPlanId"
            class="inline-block rounded-full border border-[#0064D2]/25 bg-[#0064D2]/[0.06] px-4 py-1.5 text-sm font-semibold text-[#0064D2]"
          >
            Selected Plan: {{ selectedPlanName }}
          </p>
        </div>

        <form class="space-y-5" @submit.prevent="onSubmit">
          <div class="form-field-outlined">
            <input
              id="signup-email"
              v-model="email"
              type="email"
              autocomplete="email"
              required
              placeholder=" "
            />
            <label for="signup-email">Email address</label>
          </div>

          <div>
            <div
              class="form-field-outlined form-field-outlined--suffix"
              :class="{ 'is-error': !!passwordError }"
            >
              <input
                id="signup-password"
                :type="showPassword ? 'text' : 'password'"
                v-model="password"
                autocomplete="new-password"
                required
                placeholder=" "
                @input="onPasswordInput"
              />
              <label for="signup-password">Password</label>
              <span class="form-field-outlined__suffix">
                <button type="button" @click="showPassword = !showPassword" tabindex="-1">
                  <svg v-if="!showPassword" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                  </svg>
                  <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                  </svg>
                </button>
              </span>
            </div>

            <p v-if="passwordError" class="mt-2 flex items-start text-sm font-medium leading-tight text-red-600">
              <span class="mr-1">⚠️</span> {{ passwordError }}
            </p>
            <p v-else class="mt-2 text-sm leading-tight text-slate-500">
              Use 8+ characters with uppercase, lowercase, numbers & symbols.
            </p>
          </div>

          <div v-if="error" class="animate-pulse rounded-xl border border-red-100 bg-red-50 px-4 py-3 text-center text-xs font-medium text-red-600">
            {{ error }}
          </div>
          <div v-if="success" class="rounded-xl border border-green-100 bg-green-50 px-4 py-3 text-center text-xs font-medium text-green-600">
            {{ success }}
          </div>

          <button type="submit" :disabled="loading || !canSubmit" class="central-auth-btn-submit">
            <span v-if="loading" class="flex items-center justify-center gap-2">
              <svg class="h-5 w-5 animate-spin text-current" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
              </svg>
              Creating Account...
            </span>
            <span v-else>Create your account</span>
          </button>
        </form>

        <div class="central-auth-divider">
          <div class="central-auth-divider-line" />
          <span class="central-auth-divider-text">or</span>
          <div class="central-auth-divider-line" />
        </div>

        <p class="central-auth-footer-muted">
          Already have an account?
          <router-link to="/login" class="ebay-link ml-1">Log in →</router-link>
        </p>
        <p class="central-auth-footer-muted">
          Already have a tenant?
          <router-link to="/find-tenant" class="ebay-link ml-1">Find my dashboard</router-link>
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import axiosCentral from '@/api/axiosCentral'
import { useRouter, useRoute } from 'vue-router'
import { APP_NAME, LOGO_URL } from '@central/brand'

const appName = APP_NAME
const logoUrl = LOGO_URL

const route = useRoute()
const router = useRouter()

const selectedPlanId = route.query.plan_id
const selectedPlanName = route.query.plan_name

const email = ref('')
const password = ref('')
const showPassword = ref(false)

const loading = ref(false)
const error = ref('')
const success = ref('')
const passwordError = ref('')

const passwordRegex =
  /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&#^()_+\-=\[\]{};':"\\|,.<>\/]).{8,}$/

const canSubmit = computed(() => {
  if (!email.value || !password.value) return false
  return passwordRegex.test(password.value)
})

function onPasswordInput() {
  if (!password.value) {
    passwordError.value = ''
    return
  }
  if (!passwordRegex.test(password.value)) {
    passwordError.value =
      'Password must be at least 8 characters and include uppercase, lowercase, number, and special character.'
  } else {
    passwordError.value = ''
  }
}

async function onSubmit() {
  error.value = ''
  if (!canSubmit.value) return

  loading.value = true

  try {
    const response = await axiosCentral.post('tenants', {
      email: email.value,
      password: password.value
    })

    router.push({
      path: '/tenant/confirm',
      query: {
        tenant_id: response.data.tenant_id
      }
    })
  } catch (err) {
    if (err.response?.status === 422) {
      const errors = err.response?.data?.errors
      if (errors) {
        const errorMessages = Object.values(errors).flat()
        error.value = errorMessages.join(', ') || 'Validation failed. Please check your input.'
      } else {
        error.value = err.response?.data?.message || 'Validation failed. Please check your input.'
      }
    } else {
      error.value = err.response?.data?.message || err.response?.data?.error || 'Signup failed.'
    }
  } finally {
    loading.value = false
  }
}
</script>
