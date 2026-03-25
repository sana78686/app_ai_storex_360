import { createI18n } from 'vue-i18n'
import en from '@theme-prism/i18n/en.js'
import de from '@theme-prism/i18n/de.js'

const messages = {
//   en: {
//     welcome: 'Welcome to SaleTodayStore!',
//     login: 'Login',
//     logout: 'Logout',
//     users: 'Users',
//     permissions: 'Permissions',
//     // ...add more keys as needed
//   },
//   de: {
//     welcome: 'Willkommen bei SaleTodayStore!',
//     login: 'Anmelden',
//     logout: 'Abmelden',
//     users: 'Benutzer',
//     permissions: 'Berechtigungen',
//     // ...add more keys as needed
//   }
 en,
  de
}

const i18n = createI18n({
  legacy: false,
  locale: 'en', // default
  fallbackLocale: 'en',
  messages,
})

export default i18n
