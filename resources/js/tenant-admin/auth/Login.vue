<template>
  <div class="tenant-auth-page">
    <div class="tenant-auth-card">
      <h1 class="tenant-auth-card__title">Sign in to your store</h1>
      <p class="tenant-auth-card__subtitle">
        Welcome back. Use your email or continue with Google.
      </p>

      <form class="space-y-4" @submit.prevent="handleLogin" novalidate>
        <div class="tenant-float-field" :class="{ 'is-error': !!errors.email }">
          <input
            id="tenant-login-email"
            v-model="form.email"
            type="email"
            name="email"
            autocomplete="email"
            required
            placeholder=" "
          />
          <label for="tenant-login-email">Email address</label>
        </div>
        <span v-if="errors.email" class="block text-sm text-red-600">{{ errors.email }}</span>

        <div class="tenant-float-field" :class="{ 'is-error': !!errors.password }">
          <input
            id="tenant-login-password"
            v-model="form.password"
            type="password"
            name="password"
            autocomplete="current-password"
            required
            placeholder=" "
          />
          <label for="tenant-login-password">Password</label>
        </div>
        <span v-if="errors.password" class="block text-sm text-red-600">{{ errors.password }}</span>

        <div class="flex justify-end text-sm">
          <router-link to="/dashboard/forgot-password" class="tenant-auth-link">
            Forgot password?
          </router-link>
        </div>

        <button type="submit" class="tenant-auth-btn-primary" :disabled="loading">
          <span v-if="loading">Signing in…</span>
          <span v-else>Sign in</span>
        </button>

        <button
          type="button"
          class="tenant-auth-btn-google"
          :disabled="loading || googleLoading"
          @click="handleGoogleLogin"
        >
          <img
            src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg"
            alt=""
            class="h-5 w-5"
            width="20"
            height="20"
          />
          <span v-if="googleLoading">Redirecting…</span>
          <span v-else>Continue with Google</span>
        </button>
      </form>

      <div v-if="errorMsg" class="mt-4 rounded-2xl border border-red-100 bg-red-50 px-4 py-3 text-center text-sm text-red-600">
        {{ errorMsg }}
      </div>

      <p class="tenant-auth-footer">
        Don’t have an account?
        <router-link to="/dashboard/register" class="tenant-auth-link">Register</router-link>
      </p>
    </div>
  </div>
</template>

<script>
import { ref, reactive } from 'vue'
import { useRouter } from 'vue-router'
import axiosTenant from '@/api/axiosTenant'
import Swal from 'sweetalert2'
import { redirectToGoogleTenantAuth } from '@/tenant-admin/helpers/googleTenantAuth'

export default {
  name: 'TenantLogin',
  setup() {
    const router = useRouter()
    const loading = ref(false)
    const googleLoading = ref(false)
    const form = reactive({
      email: '',
      password: '',
    })
    const errors = reactive({
      email: '',
      password: '',
    })
    const errorMsg = ref('')

    const handleLogin = async () => {
      errorMsg.value = ''
      errors.email = ''
      errors.password = ''

      try {
        loading.value = true
        const response = await axiosTenant.post('/login', form)
        if (response.data?.token) {
          localStorage.setItem('tenant_token', response.data.token)
          localStorage.setItem('tenant_user', JSON.stringify(response.data.user))
          router.push('/dashboard')
        } else {
          errorMsg.value = response.data?.message || 'Login failed'
        }
      } catch (error) {
        const data = error.response?.data
        if (data?.errors) {
          const flat = data.errors
          errors.email = flat.email?.[0] || ''
          errors.password = flat.password?.[0] || ''
        }
        errorMsg.value = data?.message || 'Login failed'
      } finally {
        loading.value = false
      }
    }

    const handleGoogleLogin = async () => {
      errorMsg.value = ''
      try {
        googleLoading.value = true
        await redirectToGoogleTenantAuth()
      } catch (e) {
        await Swal.fire({
          icon: 'error',
          title: 'Google sign-in',
          text: e.response?.data?.message || e.message || 'Could not start Google sign-in.',
        })
      } finally {
        googleLoading.value = false
      }
    }

    return {
      form,
      loading,
      googleLoading,
      errors,
      errorMsg,
      handleLogin,
      handleGoogleLogin,
    }
  },
}
</script>
