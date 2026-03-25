<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-50 px-4">
    <div class="w-full max-w-md bg-white rounded-2xl shadow-lg p-8">

      <h2 class="text-2xl font-semibold text-gray-900 mb-2">
        Reset your password
      </h2>
      <p class="text-sm text-gray-500 mb-6">
        Choose a strong password to keep your account secure.
      </p>

      <form @submit.prevent="submit" class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">
            New password
          </label>
          <input
            v-model="password"
            type="password"
            required
            class="w-full rounded-lg border border-gray-300 px-4 py-2.5
                   focus:outline-none focus:ring-2 focus:ring-blue-500"
          />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">
            Confirm password
          </label>
          <input
            v-model="password_confirmation"
            type="password"
            required
            class="w-full rounded-lg border border-gray-300 px-4 py-2.5
                   focus:outline-none focus:ring-2 focus:ring-blue-500"
          />
        </div>

        <button
          type="submit"
          :disabled="loading"
          class="w-full rounded-lg bg-blue-600 text-white py-2.5 font-medium
                 hover:bg-blue-700 transition disabled:opacity-60 disabled:cursor-not-allowed"
        >
          <span v-if="loading">Resetting...</span>
          <span v-else>Reset password</span>
        </button>
      </form>

      <!-- Messages -->
      <div v-if="message" class="mt-4 text-sm text-green-600 bg-green-50 p-3 rounded-lg">
        {{ message }}
      </div>
      <div v-if="error" class="mt-4 text-sm text-red-600 bg-red-50 p-3 rounded-lg">
        {{ error }}
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import axiosTenant from '@/api/axiosTenant'

const route = useRoute()
const router = useRouter()

const password = ref('')
const password_confirmation = ref('')
const loading = ref(false)
const error = ref('')
const message = ref('')

const submit = async () => {
  error.value = ''
  message.value = ''
  loading.value = true

  try {
    await axiosTenant.post('/reset-password', {
      email: route.query.email,
      token: route.query.token,
      password: password.value,
      password_confirmation: password_confirmation.value
    })

    message.value = 'Password reset successfully'
    setTimeout(() => router.push('/login'), 1500)
  } catch (e) {
    error.value = e.response?.data?.message || 'Reset failed'
  } finally {
    loading.value = false
  }
}
</script>
