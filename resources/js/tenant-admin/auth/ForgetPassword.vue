<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-50 px-4">
    <div class="w-full max-w-md bg-white rounded-2xl shadow-lg p-8">

      <h2 class="text-2xl font-semibold text-gray-900 mb-2">
        Forgot your password?
      </h2>
      <p class="text-sm text-gray-500 mb-6">
        Enter your email and we’ll send you a reset link.
      </p>

      <form @submit.prevent="submit" class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">
            Email address
          </label>
          <input
            v-model="email"
            type="email"
            placeholder="you@example.com"
            required
            class="w-full rounded-lg border border-gray-300 px-4 py-2.5
                   focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
          />
        </div>

        <button
          type="submit"
          :disabled="loading"
          class="w-full rounded-lg bg-blue-600 text-white py-2.5 font-medium
                 hover:bg-blue-700 transition disabled:opacity-60 disabled:cursor-not-allowed"
        >
          <span v-if="loading">Sending link...</span>
          <span v-else>Send reset link</span>
        </button>
      </form>

      <!-- Messages -->
      <div v-if="message" class="mt-4 text-sm text-green-600 bg-green-50 p-3 rounded-lg">
        {{ message }}
      </div>
      <div v-if="error" class="mt-4 text-sm text-red-600 bg-red-50 p-3 rounded-lg">
        {{ error }}
      </div>

      <div class="mt-6 text-center text-sm text-gray-500">
        Remembered your password?
        <router-link to="/login" class="text-blue-600 font-medium hover:underline">
          Back to login
        </router-link>
      </div>
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
      email: email.value
    })
    message.value = res.data.message
  } catch (e) {
    error.value = e.response?.data?.message || 'Something went wrong'
  } finally {
    loading.value = false
  }
}
</script>
