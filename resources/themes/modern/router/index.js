import TenantFrontendLayout from '@theme-prism/layouts/TenantFrontendLayout.vue'
import { createRouter, createWebHistory } from 'vue-router'
import axiosTenant from '@theme-prism/axios'
import CustomerAccountNav from '@theme-prism/pages/customer/CustomerAccountNav.vue'
// ================================
// FRONTEND (Public Store)
// ================================


const frontendRoutes = [

  {
    path: '/',
    component: TenantFrontendLayout, // THIS IS THE WRAPPER
    children: [
      {
        path: '',
        name: 'frontend-home',
        component: () => import('@theme-prism/pages/Landing.vue'),
      },
     {
         path: 'produkte',
         name: 'frontend-products',
         component: () => import('@theme-prism/pages/ProductsPage.vue'),
   // meta: { requiresAuth: true }
      },
   {
        path: 'kategorie/:slug',
        name: 'frontend-category',
        component: () => import('@theme-prism/pages/CategoryPage.vue')
  },
     {
        path: 'produkt/:id',
        name: 'frontend-product-detail',
        component: () =>import('@theme-prism/pages/ProductDetailPage.vue')
     },
     {
        path: 'registrieren',
        name: 'frontend-register',
        component: () => import('@theme-prism/pages/RegisterPage.vue')
    },
   {
        path: 'anmelden',
        name: 'frontend-login',
        component: () => import('@theme-prism/pages/LoginPage.vue')
   },
  {
        path:'warenkorb',
        name:'frontend-cart',
        component: () => import('@theme-prism/pages/CartPage.vue')
  },
  {
         path: 'wunschliste',
         name: 'frontend-wishlist',
         component: () => import('@theme-prism/pages/WishlistPage.vue')
 },
 {
         path: 'kasse',
         name: 'frontend-checkout',
         component: () => import('@theme-prism/pages/CheckoutPage.vue')
 },
 {
         path: 'bestellung-danke',
         name: 'frontend-thank-you',
         component: () => import('@theme-prism/pages/ThankYouPage.vue')
 },
 {
         path: 'bestellung-erfolg',
         name: 'frontend-order-success',
         component: () => import('@theme-prism/pages/OrderSuccessPage.vue')
 },

 //-------static pages ---------

 {
        path: 'impressum',
        name: 'imprint',
        component: () => import('@theme-prism/pages/ImpressumPage.vue')
},
 {
        path: 'agb',
        name: 'terms',
        component: () => import('@theme-prism/pages/TermsPage.vue')
  },
  {
        path: 'datenschutz',
        name: 'privacy',
        component: () => import('@theme-prism/pages/PrivacyPolicyPage.vue')
 },
{
        path: 'widerruf',
        name: 'cancellation',
        component: () => import('@theme-prism/pages/CancellationPolicyPage.vue')
},
 {
        path: 'versand-zahlung',
        name: 'shipping-payment',
        component: () => import('@theme-prism/pages/ShippingPaymentPage.vue')
 },
 {
        path: 'entsorgung',
        name: 'waste-disposal',
        component: () => import('@theme-prism/pages/WasteDisposalPage.vue')
},
{
        path: 'elektrogeraete',
        name: 'electro-devices',
        component: () => import('@theme-prism/pages/ElectroDevicesPage.vue')
},
 {
        path: 'ueber-uns',
        name: 'about',
        component: () => import('@theme-prism/pages/AboutPage.vue')
},
{
        path: 'presse',
        name: 'press',
        component: () => import('@theme-prism/pages/PressPage.vue')
},
 {
        path: 'faq',
        name: 'faq',
        component: () => import('@theme-prism/pages/FAQPage.vue')
},
{
        path: 'kontakt',
        name: 'contact',
        component: () => import('@theme-prism/pages/ContactPage.vue')
},
{
    path: 'konto',
    component: () => import('@theme-prism/layouts/CustomerAccountFrontendLayout.vue'),
    meta: { requiresAuth: true },
    children: [
      {
        path: '',
        name: 'customer-dashboard',
        component: () => import('@theme-prism/pages/customer/CustomerDashboard.vue')
      },
      {
        path: 'bestellungen',
        name: 'customer-orders',
        component: () => import('@theme-prism/pages/customer/CustomerOrdersPage.vue')
      },
      {
        path: 'bestellungen/:orderId',
        name: 'customer-order-tracking',
        component: () => import('@theme-prism/pages/customer/CustomerOrderTrackingPage.vue')
      },
      {
        path: 'wunschliste',
        name: 'customer-wishlist',
        component: () => import('@theme-prism/pages/WishlistPage.vue')
      },
      {
        path: 'zahlungen',
        name: 'customer-payments',
        component: () => import('@theme-prism/pages/customer/CustomerPaymentPage.vue')
      },
      {
        path: 'adressen',
        name: 'customer-addresses',
        component: () => import('@theme-prism/pages/customer/CustomerAddressPage.vue')
      },
      {
        path: 'benachrichtigungen',
        name: 'customer-notifications',
        component: () => import('@theme-prism/pages/customer/CustomerNotificationsPage.vue')
      },
      {
        path: 'einstellungen',
        name: 'customer-settings',
        component: () => import('@theme-prism/pages/customer/CustomerSettingsPage.vue')
      },
    ],
  },
  ],
  }
]
// ================================
// ADMIN / SYSTEM
// ================================


// ================================
// ROUTER
// ================================
const router = createRouter({
  history: createWebHistory(),
  scrollBehavior () {
    return { top: 0 }
  },
  routes: [


    ...frontendRoutes,
    {
      path: '/:pathMatch(.*)*',
      name: 'NotFound',
      component: () => import('@theme-prism/pages/NotFound.vue'),
    },
  ],
})





// ================================
// AUTH GUARD
// ================================
router.beforeEach((to, from, next) => {
  if (to.matched.some(route => route.meta.requiresAuth)) {
    const token = localStorage.getItem('tenant_token')
    if (!token) {
      return next({ name: 'frontend-login', query: { redirect: to.fullPath } })
    }
  }
  next()
})





export default router
