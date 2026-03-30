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
          <h2 class="text-2xl font-extrabold tracking-tight text-slate-900">Find Your Tenant Dashboard</h2>
          <p class="mt-2 text-sm text-slate-500">Enter your registered email and we will redirect you.</p>
        </div>

        <div class="space-y-5">
          <div class="form-field-outlined">
            <input
              id="lookup-email"
              v-model.trim="email"
              type="email"
              autocomplete="email"
              placeholder=" "
            />
            <label for="lookup-email">Email address</label>
          </div>

          <p v-if="message" :class="messageType === 'error' ? 'text-red-600' : 'text-green-600'" class="text-sm font-medium">
            {{ message }}
          </p>

          <button
            type="button"
            class="central-auth-btn-submit"
            :disabled="loading"
            @click="lookupTenant"
          >
            {{ loading ? 'Checking...' : 'Find Dashboard' }}
          </button>
        </div>

        <div class="central-auth-divider">
          <div class="central-auth-divider-line" />
          <span class="central-auth-divider-text">or</span>
          <div class="central-auth-divider-line" />
        </div>

        <p class="central-auth-footer-muted">
          <router-link to="/" class="ebay-link">Back to start</router-link>
        </p>
        <p class="central-auth-footer-muted">
          Have an account?
          <router-link to="/login" class="ebay-link ml-1">Log in</router-link>
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import axiosCentral from '@/api/axiosCentral'
import { APP_NAME, LOGO_URL } from '@central/brand'

const appName = APP_NAME
const logoUrl = LOGO_URL

const email = ref('')
const loading = ref(false)
const message = ref('')
const messageType = ref('error')

async function lookupTenant() {
  message.value = ''

  if (!email.value) {
    messageType.value = 'error'
    message.value = 'Please enter your email.'
    return
  }

  loading.value = true
  try {
    const response = await axiosCentral.get('/tenants/lookup-by-email', {
      params: { email: email.value }
    })

    if (response.data?.exists && response.data?.tenant_domain) {
      messageType.value = 'success'
      message.value = 'Tenant found. Redirecting...'
      window.location.href = response.data.tenant_domain
      return
    }

    messageType.value = 'error'
    message.value = "We couldn't find an account for this email. Please sign up first."
  } catch (error) {
    messageType.value = 'error'
    if (error?.response?.status === 404 || error?.response?.data?.exists === false) {
      message.value = "We couldn't find an account for this email. Please sign up first."
    } else {
      message.value = 'Something went wrong while checking your email. Please try again.'
    }
  } finally {
    loading.value = false
  }
}
</script>
