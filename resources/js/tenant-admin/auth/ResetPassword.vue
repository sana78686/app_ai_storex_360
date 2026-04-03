<template>
  <div class="tenant-auth-page">
    <div class="tenant-auth-card">
      <h1 class="tenant-auth-card__title">Reset your password</h1>
      <p class="tenant-auth-card__subtitle">Choose a strong password to keep your account secure.</p>

      <form class="space-y-4" @submit.prevent="submit" novalidate>
        <div class="tenant-float-field">
          <input
            id="tenant-reset-pass"
            v-model="password"
            name="password"
            type="password"
            autocomplete="new-password"
            required
            placeholder=" "
          />
          <label for="tenant-reset-pass">New password</label>
        </div>

        <div class="tenant-float-field">
          <input
            id="tenant-reset-pass2"
            v-model="password_confirmation"
            name="password_confirmation"
            type="password"
            autocomplete="new-password"
            required
            placeholder=" "
          />
          <label for="tenant-reset-pass2">Confirm password</label>
        </div>

        <button type="submit" class="tenant-auth-btn-primary" :disabled="loading">
          <span v-if="loading">Resetting…</span>
          <span v-else>Reset password</span>
        </button>
      </form>

      <div v-if="message" class="mt-4 rounded-2xl border border-emerald-100 bg-emerald-50 px-4 py-3 text-center text-sm text-emerald-800">
        {{ message }}
      </div>
      <div v-if="error" class="mt-4 rounded-2xl border border-red-100 bg-red-50 px-4 py-3 text-center text-sm text-red-600">
        {{ error }}
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import axiosTenant from '@/api/axiosTenant'
import Swal from 'sweetalert2'

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
      password_confirmation: password_confirmation.value,
    })

    message.value = 'Password reset successfully'
    await Swal.fire({
      icon: 'success',
      title: 'All set',
      text: 'You can sign in with your new password.',
    })
    router.push('/dashboard/login')
  } catch (e) {
    error.value = e.response?.data?.message || 'Reset failed'
  } finally {
    loading.value = false
  }
}
</script>
