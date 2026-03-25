import '@css/app.css'
import { createApp } from 'vue'
import { createPinia } from 'pinia'
import piniaPluginPersistedstate from 'pinia-plugin-persistedstate'

import App from '@theme-prism/App.vue'
import router from '@theme-prism/router'
import i18n from '@theme-prism/i18n'

const app = createApp(App)

const pinia = createPinia()
pinia.use(piniaPluginPersistedstate)

app
  .use(pinia)   // ✅ Pinia FIRST
  .use(router)
  .use(i18n)
  .mount('#app')
