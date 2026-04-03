<template>
  <div class="tenant-auth-page">
    <div class="tenant-auth-card">
      <h1 class="tenant-auth-card__title">Forgot your password?</h1>
      <p class="tenant-auth-card__subtitle">Enter your email and we’ll send you a reset link.</p>

      <form class="space-y-4" @submit.prevent="submit" novalidate>
        <div class="tenant-float-field">
          <input id="tenant-forgot-email" v-model="email" type="email" autocomplete="email" required placeholder=" " />
          <label for="tenant-forgot-email">Email address</label>
        </div>

        <button type="submit" class="tenant-auth-btn-primary" :disabled="loading">
          <span v-if="loading">Sending link…</span>
          <span v-else>Send reset link</span>
        </button>
      </form>

      <div v-if="message" class="mt-4 rounded-2xl border border-emerald-100 bg-emerald-50 px-4 py-3 text-center text-sm text-emerald-800">
        {{ message }}
      </div>
      <div v-if="error" class="mt-4 rounded-2xl border border-red-100 bg-red-50 px-4 py-3 text-center text-sm text-red-600">
        {{ error }}
      </div>

      <p class="tenant-auth-footer">
        Remembered your password?
        <router-link to="/dashboard/login" class="tenant-auth-link">Back to sign in</router-link>
      </p>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import axiosTenant from '@/api/axiosTenant'

const email = ref('')
const loading = ref(false)
const message = ref('')
const error = ref('')

const submit = async () => {
  error.value = ''
  message.value = ''
  loading.value = true

  try {
    const res = await axiosTenant.post('/forgot-password', {
      email: email.value,
    })
    message.value = res.data.message
  } catch (e) {
    error.value = e.response?.data?.message || 'Something went wrong'
  } finally {
    loading.value = false
  }
}
</script>
