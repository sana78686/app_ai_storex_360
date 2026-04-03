import axiosCentral from '@/api/axiosCentral'

/**
 * Starts Google OAuth for tenant dashboard access (central API + shared callback URL).
 */
export async function redirectToGoogleTenantAuth() {
  localStorage.setItem('social_auth_provider', 'google')
  const { data } = await axiosCentral.get('/auth/social/redirect/google')
  if (data?.url) {
    window.location.href = data.url
    return
  }
  throw new Error('Failed to get Google sign-in URL')
}
