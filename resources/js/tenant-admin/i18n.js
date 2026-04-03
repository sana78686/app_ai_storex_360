import { createI18n } from 'vue-i18n'
import en from './i18n/en.js'
import de from './i18n/de.js'

/** Same key as tenant storefront (`TenantPrismFrontendLayout`) so language stays in sync. */
const SITE_LANG_KEY = 'site-lang'

function readStoredLocale() {
  if (typeof localStorage === 'undefined') {
    return 'en'
  }
  const stored = localStorage.getItem(SITE_LANG_KEY)
  return stored === 'de' || stored === 'en' ? stored : 'en'
}

const messages = {
//   en: {
//     welcome: 'Welcome to AI StoreX 360!',
//     login: 'Login',
//     logout: 'Logout',
//     users: 'Users',
//     permissions: 'Permissions',
//     // ...add more keys as needed
//   },
//   de: {
//     welcome: 'Willkommen bei AI StoreX 360!',
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
  locale: readStoredLocale(),
  fallbackLocale: 'en',
  messages,
})

export default i18n
