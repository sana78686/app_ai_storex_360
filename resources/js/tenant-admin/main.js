import '@css/app.css'
import '@css/tenant-font.css'
import '@css/tenant-admin-theme.css'
import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import i18n from './i18n'
import { setupTenantSwal } from './setupTenantSwal'

setupTenantSwal()

createApp(App)
  .use(router)
  .use(i18n)
  .mount('#app')
