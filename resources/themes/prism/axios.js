import axios from 'axios'

const axiosTenant = axios.create({
  baseURL: '/api/v1',
  headers: {
    Accept: 'application/json'
    // ❌ DO NOT SET Content-Type HERE
  }
})

axiosTenant.interceptors.request.use(
  config => {
    const token = localStorage.getItem('tenant_token')
    if (token) {
      config.headers.Authorization = `Bearer ${token}`
    }
    return config
  },
  error => Promise.reject(error)
)

axiosTenant.interceptors.response.use(
  response => response,
  error => {
    if (error.response?.status === 401) {
      localStorage.removeItem('tenant_token')
      import('@theme-prism/stores/authStore').then(m => m.useAuthStore().logout()).catch(() => {})
    }
    return Promise.reject(error)
  }
)

export default axiosTenant
