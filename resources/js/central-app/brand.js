/** Central app display name and assets (override via .env: VITE_APP_NAME, VITE_BRAND_LOGO_URL, VITE_BRAND_FAVICON_URL). */
export const APP_NAME = import.meta.env.VITE_APP_NAME || 'AI StoreX 360'

/** Default files: public/assets/logo/aistorex360-logo.png, public/assets/logo/favicon.png */
export const LOGO_URL = import.meta.env.VITE_BRAND_LOGO_URL || '/assets/logo/aistorex360-logo.png'
export const FAVICON_URL = import.meta.env.VITE_BRAND_FAVICON_URL || '/assets/logo/favicon.png'
