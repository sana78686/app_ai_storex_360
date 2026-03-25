<template>
  <div class="relative flex flex-col items-center justify-center h-screen overflow-hidden px-4 py-12">

    <div class="absolute inset-0 bg-slate-50 -z-20"></div>

    <div class="absolute inset-0 -z-10">
      <div class="absolute top-[-15%] left-[-10%] w-[600px] h-[600px] bg-indigo-200/40 rounded-full blur-[120px]"></div>

      <div class="absolute bottom-[-10%] right-[-5%] w-[500px] h-[500px] bg-blue-100/50 rounded-full blur-[100px]"></div>

      <div class="absolute top-[30%] left-[-5%] w-[300px] h-[300px] bg-purple-100/30 rounded-full blur-[80px]"></div>
    </div>

    <div
      class="absolute inset-0 -z-10 opacity-[0.06] mix-blend-multiply"
      style="background-image: url('https://www.transparenttextures.com/patterns/cubes.png');"
    ></div>

    <div class="mb-2 relative z-10 animate-fade-in">
      <img :src="logoUrl" alt="SaleTodayStore Logo" class="h-24 w-auto object-contain" />
    </div>

    <div class="bg-white p-6 sm:p-10 rounded-3xl shadow-2xl shadow-indigo-200/60 w-full max-w-[460px] border border-white/50 backdrop-blur-sm relative z-10 transition-transform duration-500 hover:scale-[1.01]">

      <div class="text-center mb-8">
        <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight mb-2" v-if="selectedPlanId">
          Complete Your Registration
        </h2>
        <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight mb-2" v-else>
          Create Your Account
        </h2>

        <p v-if="selectedPlanId" class="text-sm font-semibold text-indigo-600 bg-indigo-50/80 inline-block px-4 py-1.5 rounded-full border border-indigo-100 mb-4">
          Selected Plan: {{ selectedPlanName }}
        </p>
        <!-- <p class="text-slate-500 text-sm">Fill in your details to get started with SaleTodayStore.</p> -->
      </div>

      <form @submit.prevent="onSubmit" class="space-y-5">
        <div class="group">
          <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5 ml-1">Email Address</label>
          <input
            type="email"
            v-model="email"
            placeholder="name@company.com"
            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all"
            required
          />
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5 ml-1">Password</label>
          <div class="relative">
            <input
              :type="showPassword ? 'text' : 'password'"
              v-model="password"
              @input="validatePassword"
              placeholder="••••••••"
              class="w-full bg-slate-50 border rounded-xl px-4 py-3 text-slate-900 focus:outline-none focus:ring-2 transition-all"
              :class="passwordError
                ? 'border-red-400 focus:ring-red-400/20'
                : 'border-slate-200 focus:ring-indigo-500/20 focus:border-indigo-500'"
              required
            />
    <button
  type="button"
  @click="showPassword = !showPassword"
  class="absolute right-3 top-3.5 text-slate-400 hover:text-slate-600 transition-colors focus:outline-none"
>
  <svg v-if="!showPassword" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
  </svg>

  <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
    <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
  </svg>
</button>
          </div>

          <p v-if="passwordError" class="text-red-500 text-[11px] font-medium mt-1.5 ml-1 flex items-start leading-tight">
            <span class="mr-1">⚠️</span> {{ passwordError }}
          </p>
          <p v-else class="text-slate-400 text-[11px] mt-1.5 ml-1 leading-tight">
            Use 8+ characters with uppercase, lowercase, numbers & symbols.
          </p>
        </div>

        <div v-if="error" class="bg-red-50 border border-red-100 text-red-600 text-xs py-3 px-4 rounded-xl text-center font-medium animate-pulse">
          {{ error }}
        </div>
        <div v-if="success" class="bg-green-50 border border-green-100 text-green-600 text-xs py-3 px-4 rounded-xl text-center font-medium">
          {{ success }}
        </div>

        <button
          type="submit"
          :disabled="loading || !email || !validatePassword()"
          :class="[
            'w-full font-bold py-2 rounded-2xl transition-all duration-300 shadow-lg relative overflow-hidden',
            (loading || !email || !validatePassword() )
              ? 'bg-slate-100 text-slate-400 cursor-not-allowed shadow-none'
              : 'bg-indigo-600 text-white hover:bg-indigo-700 shadow-indigo-200 hover:shadow-indigo-300 active:scale-[0.98]'
          ]"
        >
          <span v-if="loading" class="flex items-center justify-center gap-2">
            <svg class="animate-spin h-5 w-5 text-current" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
            Creating Account...
          </span>
          <span v-else>Create your account</span>
        </button>
      </form>

      <div class="flex items-center my-2">
        <div class="flex-grow border-t border-slate-100"></div>
        <span class="mx-4 text-[10px] font-black uppercase tracking-[0.2em] text-slate-300">or</span>
        <div class="flex-grow border-t border-slate-100"></div>
      </div>

      <div class="text-center text-sm font-medium text-slate-500">
        Already have an account?
        <router-link to="/" class="text-indigo-600 font-bold hover:text-indigo-700 hover:underline transition-all ml-1">
          Log in →
        </router-link>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Smooth fade-in for the whole view */
.animate-fade-in {
  animation: fadeIn 0.8s ease-out;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(-10px); }
  to { opacity: 1; transform: translateY(0); }
}

/* Custom transitions for inputs */
input {
  transition: all 0.2s ease-in-out;
}
</style>

<script setup>

import { ref } from 'vue'
import { useReCaptcha } from 'vue-recaptcha-v3'
import axiosCentral from '@/api/axiosCentral'
import { useRouter, useRoute } from 'vue-router'

const recaptchaToken = ref(null)
// const siteKey = import.meta.env.VITE_RECAPTCHA_SITE_KEY

const { executeRecaptcha } = useReCaptcha()
const logoUrl = '/assets/logo/saletodaystore-logo.png'


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

/**
 * STRICT password regex
 */
const passwordRegex =
  /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&#^()_+\-=\[\]{};':"\\|,.<>\/]).{8,}$/

/**
 * Validate password ONLY
 */
const validatePassword = () => {
  if (!password.value) {
    passwordError.value = ''
    return false
  }

  if (!passwordRegex.test(password.value)) {
    passwordError.value =
      'Password must be at least 8 characters and include uppercase, lowercase, number, and special character.'
    return false
  }

  passwordError.value = ''
  return true
}

/**
 * Submit handler
 */
async function onSubmit() {
  error.value = ''
  loading.value = true

  try {
    recaptchaToken.value = await executeRecaptcha('signup')

    // store response in a variable
    const response = await axiosCentral.post('tenants', {
      email: email.value,
      password: password.value,
      recaptcha_token: recaptchaToken.value,
    })

    // now response.data is defined
    router.push({
      path: '/tenant/confirm',
      query: {
        tenant_id: response.data.tenant_id, // ✅ works now
      },
    })
  } catch (err) {
    // Handle validation errors
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

