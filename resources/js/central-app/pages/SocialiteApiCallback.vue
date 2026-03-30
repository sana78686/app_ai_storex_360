<template>
  <div class="flex min-h-screen flex-col items-center justify-center gap-4 bg-slate-50 px-4">
    <img :src="logoUrl" :alt="`${appName} logo`" class="h-16 w-auto object-contain" />
    <p class="text-slate-600">Signing you in...</p>
  </div>
</template>

<script setup>
import { onMounted } from 'vue'
import axiosCentral from '@/api/axiosCentral'
import { APP_NAME, LOGO_URL } from '@central/brand'

const logoUrl = LOGO_URL
const appName = APP_NAME

onMounted(async () => {
  const params = new URLSearchParams(window.location.search)
  const code = params.get('code')
  const provider = localStorage.getItem('social_auth_provider') || 'google'

  if (!code) {
    alert('No code found in URL. Please try logging in again.')
    return
  }

  try {
    const response = await axiosCentral.post(`/auth/social/callback/${provider}`, { code })
    const data = response.data

    if (data.tenant_token && data.user && data.tenant_domain) {
      localStorage.setItem('tenant_token', data.tenant_token)
      localStorage.setItem('tenant_user', JSON.stringify(data.user))
      localStorage.setItem('tenant_id', data.tenant_id)
      localStorage.removeItem('social_auth_provider')

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









