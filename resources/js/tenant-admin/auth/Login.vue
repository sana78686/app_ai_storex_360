<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-50 px-4">
    <div class="w-full max-w-md bg-white rounded-2xl shadow-lg p-8">

      <!-- Header -->
      <h2 class="text-2xl font-semibold text-gray-900 mb-2">
        Login to your store
      </h2>
      <p class="text-sm text-gray-500 mb-6">
        Welcome back! Please enter your details.
      </p>

      <!-- Form -->
      <form @submit.prevent="handleLogin" class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">
            Email address
          </label>
          <input
            type="email"
            v-model="form.email"
            required
            placeholder="you@example.com"
            class="w-full rounded-lg border border-gray-300 px-4 py-2.5
                   focus:outline-none focus:ring-2 focus:ring-blue-500"
          />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">
            Password
          </label>
          <input
            type="password"
            v-model="form.password"
            required
            placeholder="••••••••"
            class="w-full rounded-lg border border-gray-300 px-4 py-2.5
                   focus:outline-none focus:ring-2 focus:ring-blue-500"
          />
        </div>

        <!-- Actions -->
        <div class="flex items-center justify-between text-sm">
          <router-link
            to="/forgot-password"
            class="text-blue-600 font-medium hover:underline"
          >
            Forgot password?
          </router-link>
        </div>

        <button
          type="submit"
          :disabled="loading"
          class="w-full rounded-lg bg-blue-600 text-white py-2.5 font-medium
                 hover:bg-blue-700 transition disabled:opacity-60 disabled:cursor-not-allowed"
        >
          <span v-if="loading">Logging in...</span>
          <span v-else>Login</span>
        </button>
      </form>

      <!-- Error -->
      <div
        v-if="errorMsg"
        class="mt-4 text-sm text-red-600 bg-red-50 p-3 rounded-lg"
      >
        {{ errorMsg }}
      </div>

      <!-- Footer -->
      <div class="mt-6 text-center text-sm text-gray-500">
        Don’t have an account?
        <router-link
          to="/register"
          class="text-blue-600 font-medium hover:underline"
        >
          Register here
        </router-link>
      </div>

    </div>
  </div>
</template>


<script>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import axiosTenant from '@/api/axiosTenant'

export default {
  name: 'TenantLogin',
  setup() {
    const router = useRouter()
    const loading = ref(false)
    const form = ref({
      email: '',
      password: ''
    })
    const errorMsg = ref('')

    const handleLogin = async () => {

      errorMsg.value = ''
      try {
        loading.value = true
        const response = await axiosTenant.post('/login', form.value)
        // console.log('LOGIN RESPONSE', response.data)
        if (response.data) {
            // console.log('LOGIN RESPONSE', response.data)
          localStorage.setItem('tenant_token', response.data.token)
          localStorage.setItem('tenant_user', JSON.stringify(response.data.user))
          router.push('/dashboard')
        } else {
          errorMsg.value = response.data.message || 'Loginn failed'
        }
      } catch (error) {
        errorMsg.value = error.response?.data?.message || 'Login failed'
      } finally {
        loading.value = false
      }
    }

    return {
      form,
      loading,
      handleLogin,
      errorMsg
    }
  }
}
</script>

<style scoped>
.login-container {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 100vh;
  background-color: #f8f9fa;
}

.login-box {
  background: white;
  padding: 2rem;
  border-radius: 8px;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
  width: 100%;
  max-width: 400px;
}

.form-group {
  margin-bottom: 1rem;
}

.form-group label {
  display: block;
  margin-bottom: 0.5rem;
}

.form-control {
  width: 100%;
  padding: 0.5rem;
  border: 1px solid #ddd;
  border-radius: 4px;
}

.btn {
  width: 100%;
  margin-top: 1rem;
}
</style>
