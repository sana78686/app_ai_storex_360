<template>
  <div>
    <p>Signing you in via Google...</p>
  </div>
</template>

<script setup>
import { onMounted } from 'vue'
import axiosCentral from '@/api/axiosCentral'

onMounted(async () => {
  const params = new URLSearchParams(window.location.search)
  const code = params.get('code')

  if (!code) {
    alert('No code found in URL. Please try logging in again.')
    return
  }

  try {
    // Send code to backend
    const response = await axiosCentral.post('/auth/social/callback/google', { code })
    const data = response.data

    // Save auth token and user info
    if (data.tenant_token && data.user && data.tenant_domain) {
      localStorage.setItem('tenant_token', data.tenant_token)
      localStorage.setItem('tenant_user', JSON.stringify(data.user))
      localStorage.setItem('tenant_id', data.tenant_id)

      // Optional: pass token in query if needed by tenant
      // const redirectUrl = `${data.tenant_domain}/dashboard?token=${data.tenant_token}`

      // Redirect to tenant's dashboard
      window.location.href = `${data.tenant_domain}/dashboard`

    } else {
      alert('Login succeeded but tenant information is incomplete.')
      console.error('Missing tenant_domain or token:', data)
    }

  } catch (error) {
    const errorMsg = error.response?.data?.message || error.response?.data?.error || error.message
    alert('Social login failed: ' + errorMsg)
    console.error('Social login error:', error)
  }
})

</script> 









