<template>
  <div class="register-container">
    <div class="register-box">
      <h2>Create Your Account</h2>
      <form @submit.prevent="handleRegister">
        <div class="form-group">
          <label for="email">Email</label>
          <input
            type="email"
            id="email"
            v-model="form.email"
            class="form-control"
            required
          >
        </div>
        <div class="form-group">
          <label for="password">Password</label>
          <input
            type="password"
            id="password"
            v-model="form.password"
            class="form-control"
            required
          >
        </div>

        <div v-if="errorMsg" class="error-msg">{{ errorMsg }}</div>
        <div v-if="successMsg" class="success-msg">{{ successMsg }}</div>

        <button type="submit" class="btn btn-primary" :disabled="loading">
          {{ loading ? 'Creating Account...' : 'Register' }}
        </button>
      </form>

      <p class="mt-3">
        Already have an account?
        <router-link to="/login">Login here</router-link>
      </p>
    </div>
  </div>
</template>
<script>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import axiosTenant from '@/api/axiosTenant'
export default {
  name: 'TenantRegister',
  setup() {
    const router = useRouter()
    const loading = ref(false)
    const errorMsg = ref('')
    const successMsg = ref('')
    const form = ref({
      email: '',
      password: ''
    })
    const handleRegister = async () => {
      errorMsg.value = ''
      successMsg.value = ''
      loading.value = true
      if (!form.value.email || !form.value.password) {
        errorMsg.value = 'Email and password are required.'
        loading.value = false
        return
      }
      try {
        loading.value = true
        const response = await axiosTenant.post('/signup', form.value)
        if (response.data.status) {
          localStorage.setItem('tenant_token', response.data.tenant_token)
          localStorage.setItem('tenant_user', JSON.stringify(response.data.user))
          router.push('/dashboard')
        } else {
          errorMsg.value = response.data.message || 'Signup failed'
        }
      } catch (error) {
        errorMsg.value = error.response?.data?.message || 'Signup failed'
      } finally {
        loading.value = false
      }
    }
    return {
      form,
      loading,
      errorMsg,
      successMsg,
      handleRegister
    }
  }
}
</script>

<style scoped>
.register-container {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 100vh;
  background-color: #f8f9fa;
}

.register-box {
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

.error-msg {
  color: #d9534f;
  margin-bottom: 1rem;
  text-align: center;
}

.success-msg {
  color: #28a745;
  margin-bottom: 1rem;
  text-align: center;
}
</style>
