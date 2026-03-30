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
          <h2 class="text-3xl font-extrabold tracking-tight text-slate-900">Sign in to your account</h2>
        </div>

        <form class="space-y-5" @submit.prevent="handleLogin" novalidate>
          <div class="form-field-outlined" :class="{ 'is-error': errors.email }">
            <input
              id="login-email"
              v-model="form.email"
              name="email"
              type="email"
              autocomplete="email"
              required
              placeholder=" "
            />
            <label for="login-email">Email address</label>
          </div>
          <span v-if="errors.email" class="mt-1 block text-sm text-red-600">{{ errors.email }}</span>

          <div class="form-field-outlined" :class="{ 'is-error': errors.password }">
            <input
              id="login-password"
              v-model="form.password"
              name="password"
              type="password"
              autocomplete="current-password"
              required
              placeholder=" "
            />
            <label for="login-password">Password</label>
          </div>
          <span v-if="errors.password" class="mt-1 block text-sm text-red-600">{{ errors.password }}</span>

          <div class="flex items-center">
            <input
              id="remember"
              v-model="form.remember"
              name="remember"
              type="checkbox"
              class="h-4 w-4 rounded border-slate-300 text-[#0064d2] focus:ring-[#0064d2]"
            />
            <label for="remember" class="ml-2 block text-sm text-slate-700">Remember me</label>
          </div>

          <button type="submit" :disabled="loading" class="central-auth-btn-submit">
            <span v-if="loading">Signing in...</span>
            <span v-else>Sign in</span>
          </button>

          <div v-if="errors.general" class="rounded-xl border border-red-100 bg-red-50 px-4 py-3 text-center text-sm font-medium text-red-600">
            {{ errors.general }}
          </div>
        </form>

        <div class="central-auth-divider">
          <div class="central-auth-divider-line" />
          <span class="central-auth-divider-text">or</span>
          <div class="central-auth-divider-line" />
        </div>

        <p class="central-auth-footer-muted">
          New here?
          <router-link to="/signup" class="ebay-link ml-1">Create account</router-link>
        </p>
        <p class="central-auth-footer-muted">
          Already have a tenant?
          <router-link to="/find-tenant" class="ebay-link ml-1">Find my dashboard</router-link>
        </p>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, reactive } from 'vue'
import { useRouter } from 'vue-router'
import axiosCentral from '@/api/axiosCentral'
import { APP_NAME, LOGO_URL } from '@central/brand'

export default {
  name: 'Login',
  setup() {
    const router = useRouter()
    const logoUrl = LOGO_URL
    const appName = APP_NAME
    const loading = ref(false)
    const errors = reactive({
      email: '',
      password: '',
      general: ''
    })

    const form = reactive({
      email: '',
      password: '',
      remember: false
    })

    const clearErrors = () => {
      errors.email = ''
      errors.password = ''
      errors.general = ''
    }

    const validateForm = () => {
      clearErrors()
      let isValid = true

      if (!form.email) {
        errors.email = 'Email is required'
        isValid = false
      } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.email)) {
        errors.email = 'Please enter a valid email'
        isValid = false
      }

      if (!form.password) {
        errors.password = 'Password is required'
        isValid = false
      } else if (form.password.length < 6) {
        errors.password = 'Password must be at least 6 characters'
        isValid = false
      }

      return isValid
    }

    const handleLogin = async () => {
      if (!validateForm()) {
        return
      }

      loading.value = true

      try {
        const response = await axiosCentral.post('/login', {
          email: form.email,
          password: form.password,
          remember: form.remember
        }, {
          headers: {
            Accept: 'application/json',
            'Content-Type': 'application/json'
          }
        })

        if (response.data.central_token) {
          localStorage.setItem('central_token', response.data.central_token)
          localStorage.setItem('user', JSON.stringify(response.data.user))

          form.email = ''
          form.password = ''
          form.remember = false

          await router.replace({ name: 'dashboard' })
        } else {
          errors.general = 'Invalid response from server'
          loading.value = false
        }
      } catch (error) {
        loading.value = false

        if (error.response?.data?.errors) {
          Object.keys(error.response.data.errors).forEach(key => {
            errors[key] = error.response.data.errors[key][0]
          })
        } else if (error.response?.data?.message) {
          errors.general = error.response.data.message
        } else {
          errors.general = 'Invalid email or password. Please try again.'
        }
      }
    }

    return {
      form,
      errors,
      loading,
      handleLogin,
      logoUrl,
      appName
    }
  }
}
</script>
