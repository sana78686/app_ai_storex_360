import axios from 'axios'
import '@css/app.css'
import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import i18n from './i18n'

async function bootstrap() {
  axios.defaults.withCredentials = true
  try {
    await axios.get('/sanctum/csrf-cookie', {
      withCredentials: true,
      headers: { Accept: 'application/json' },
    })
  } catch (e) {
    console.warn('Sanctum CSRF cookie could not be loaded', e)
  }

  createApp(App).use(router).use(i18n).mount('#app')
}

bootstrap()
