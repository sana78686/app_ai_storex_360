<template>
  <div class="tenant-auth-page">
    <div class="tenant-auth-card">
      <h1 class="tenant-auth-card__title">Create your account</h1>
      <p class="tenant-auth-card__subtitle">
        Add your store admin user, or sign up faster with Google.
      </p>

      <form class="space-y-4" @submit.prevent="handleRegister" novalidate>
        <div class="tenant-float-field" :class="{ 'is-error': !!fieldErrors.name }">
          <input id="tenant-reg-name" v-model="form.name" type="text" autocomplete="name" required placeholder=" " />
          <label for="tenant-reg-name">Full name</label>
        </div>
        <span v-if="fieldErrors.name" class="block text-sm text-red-600">{{ fieldErrors.name }}</span>

        <div class="tenant-float-field" :class="{ 'is-error': !!fieldErrors.email }">
          <input id="tenant-reg-email" v-model="form.email" type="email" autocomplete="email" required placeholder=" " />
          <label for="tenant-reg-email">Email address</label>
        </div>
        <span v-if="fieldErrors.email" class="block text-sm text-red-600">{{ fieldErrors.email }}</span>

        <div class="tenant-float-field" :class="{ 'is-error': !!fieldErrors.password }">
          <input
            id="tenant-reg-password"
            v-model="form.password"
            name="password"
            type="password"
            autocomplete="new-password"
            required
            placeholder=" "
          />
          <label for="tenant-reg-password">Password</label>
        </div>
        <span v-if="fieldErrors.password" class="block text-sm text-red-600">{{ fieldErrors.password }}</span>

        <div class="tenant-float-field" :class="{ 'is-error': !!fieldErrors.password_confirmation }">
          <input
            id="tenant-reg-password2"
            v-model="form.password_confirmation"
            name="password_confirmation"
            type="password"
            autocomplete="new-password"
            required
            placeholder=" "
          />
          <label for="tenant-reg-password2">Confirm password</label>
        </div>
        <span v-if="fieldErrors.password_confirmation" class="block text-sm text-red-600">{{
          fieldErrors.password_confirmation
        }}</span>

        <button type="submit" class="tenant-auth-btn-primary" :disabled="loading">
          <span v-if="loading">Creating account…</span>
          <span v-else>Create account</span>
        </button>

        <div class="tenant-auth-divider">
          <div class="tenant-auth-divider__line" />
          <span class="tenant-auth-divider__text">or</span>
          <div class="tenant-auth-divider__line" />
        </div>

        <button
          type="button"
          class="tenant-auth-btn-google"
          :disabled="loading || googleLoading"
          @click="handleGoogleRegister"
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
        Already have an account?
        <router-link to="/dashboard/login" class="tenant-auth-link">Sign in</router-link>
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
  name: 'TenantRegister',
  setup() {
    const router = useRouter()
    const loading = ref(false)
    const googleLoading = ref(false)
    const errorMsg = ref('')
    const form = reactive({
      name: '',
      email: '',
      password: '',
      password_confirmation: '',
    })
    const fieldErrors = reactive({
      name: '',
      email: '',
      password: '',
      password_confirmation: '',
    })

    const clearErrors = () => {
      errorMsg.value = ''
      fieldErrors.name = ''
      fieldErrors.email = ''
      fieldErrors.password = ''
      fieldErrors.password_confirmation = ''
    }

    const handleRegister = async () => {
      clearErrors()
      loading.value = true
      try {
        const response = await axiosTenant.post('/signup', form)
        if (response.data?.token) {
          localStorage.setItem('tenant_token', response.data.token)
          localStorage.setItem('tenant_user', JSON.stringify(response.data.user))
          router.push('/dashboard')
        } else {
          errorMsg.value = response.data?.message || 'Signup failed'
        }
      } catch (error) {
        const data = error.response?.data
        if (data && typeof data === 'object' && !data.message) {
          fieldErrors.name = data.name?.[0] || ''
          fieldErrors.email = data.email?.[0] || ''
          fieldErrors.password = data.password?.[0] || ''
          fieldErrors.password_confirmation = data.password_confirmation?.[0] || ''
        }
        errorMsg.value = data?.message || 'Signup failed'
      } finally {
        loading.value = false
      }
    }

    const handleGoogleRegister = async () => {
      clearErrors()
      try {
        googleLoading.value = true
        await redirectToGoogleTenantAuth()
      } catch (e) {
        await Swal.fire({
          icon: 'error',
          title: 'Google sign-up',
          text: e.response?.data?.message || e.message || 'Could not start Google sign-up.',
        })
      } finally {
        googleLoading.value = false
      }
    }

    return {
      form,
      loading,
      googleLoading,
      errorMsg,
      fieldErrors,
      handleRegister,
      handleGoogleRegister,
    }
  },
}
</script>
