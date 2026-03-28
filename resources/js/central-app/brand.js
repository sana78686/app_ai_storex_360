/** Central app display name and assets (override via .env: VITE_APP_NAME, VITE_BRAND_LOGO_URL). */
export const APP_NAME = import.meta.env.VITE_APP_NAME || 'AI StoreX 360'

/** Keep existing filenames on disk until you replace images under public/assets/logo/. */
export const LOGO_URL = import.meta.env.VITE_BRAND_LOGO_URL || '/assets/logo/saletodaystore-logo.png'
