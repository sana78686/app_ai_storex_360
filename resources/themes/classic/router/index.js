import TenantPrismFrontendLayout from '@theme-classic/layouts/TenantPrismFrontendLayout.vue'
import { createRouter, createWebHistory } from 'vue-router'
import axiosTenant from '@theme-classic/axios'
import PrismCustomerFrontendLayout from '@theme-classic/layouts/PrismCustomerFrontendLayout.vue'

// ================================
// FRONTEND (Public Store)
// ================================


const frontendRoutes = [

  {
    path: '/',
    component: TenantPrismFrontendLayout, // THIS IS THE WRAPPER
    children: [
      {
        path: '',
        name: 'frontend-home',
        component: () => import('@theme-classic/pages/Landing.vue'),
      },
     {
         path: 'produkte',
         name: 'frontend-products',
         component: () => import('@theme-classic/pages/ProductsPage.vue'),
   // meta: { requiresAuth: true }
      },
   {
        path: 'kategorie/:slug',
        name: 'frontend-category',
        component: () => import('@theme-classic/pages/CategoryPage.vue')
  },
     {
        path: 'produkt/:id',
        name: 'frontend-product-detail',
        component: () =>import('@theme-classic/pages/ProductDetailPage.vue')
     },
     {
        path: 'registrieren',
        name: 'frontend-register',
        component: () => import('@theme-classic/pages/RegisterPage.vue')
    },
   {
        path: 'anmelden',
        name: 'frontend-login',
        component: () => import('@theme-classic/pages/LoginPage.vue')
   },
  {
        path:'warenkorb',
        name:'frontend-cart',
        component: () => import('@theme-classic/pages/CartPage.vue')
  },
  {
         path: 'wunschliste',
         name: 'frontend-wishlist',
         component: () => import('@theme-classic/pages/WishlistPage.vue')
 },
 {
         path: 'kasse',
         name: 'frontend-checkout',
         component: () => import('@theme-classic/pages/CheckoutPage.vue')
 },

 //-------static pages ---------

 {
        path: 'impressum',
        name: 'imprint',
        component: () => import('@theme-classic/pages/ImpressumPage.vue')
},
 {
        path: 'agb',
        name: 'terms',
        component: () => import('@theme-classic/pages/TermsPage.vue')
  },
  {
        path: 'datenschutz',
        name: 'privacy',
        component: () => import('@theme-classic/pages/PrivacyPolicyPage.vue')
 },
{
        path: 'widerruf',
        name: 'cancellation',
        component: () => import('@theme-classic/pages/CancellationPolicyPage.vue')
},
 {
        path: 'versand-zahlung',
        name: 'shipping-payment',
        component: () => import('@theme-classic/pages/ShippingPaymentPage.vue')
 },
 {
        path: 'entsorgung',
        name: 'waste-disposal',
        component: () => import('@theme-classic/pages/WasteDisposalPage.vue')
},
{
        path: 'elektrogeraete',
        name: 'electro-devices',
        component: () => import('@theme-classic/pages/ElectroDevicesPage.vue')
},
 {
        path: 'ueber-uns',
        name: 'about',
        component: () => import('@theme-classic/pages/AboutPage.vue')
},
{
        path: 'presse',
        name: 'press',
        component: () => import('@theme-classic/pages/PressPage.vue')
},
 {
        path: 'faq',
        name: 'faq',
        component: () => import('@theme-classic/pages/FAQPage.vue')
},
{
        path: 'kontakt',
        name: 'contact',
        component: () => import('@theme-classic/pages/ContactPage.vue')
},]
},
  ]
// ================================
// CUSTOMER DASHBOARD
// ================================
 const customerRoutes = {
    path: '/konto',
    component: PrismCustomerFrontendLayout,
    meta: { requiresAuth: true },
    children: [
      {
        path: '',
        name: 'customer-dashboard',
        component: () => import('@theme-classic/pages/customer/CustomerDashboard.vue')
      },
      {
        path: 'bestellungen',
        name: 'customer-orders',
        component: () => import('@theme-classic/pages/customer/CustomerOrdersPage.vue')
      },
      {
        path: 'bestellungen/:orderId',
        name: 'customer-order-tracking',
        component: () => import('@theme-classic/pages/customer/CustomerOrderTrackingPage.vue')
      },
      {
        path: 'wunschliste',
        name: 'customer-wishlist',
        component: () => import('@theme-classic/pages/WishlistPage.vue')
      },
      {
        path: 'zahlungen',
        name: 'customer-payments',
        component: () => import('@theme-classic/pages/customer/CustomerPaymentPage.vue')
      },
      {
        path: 'adressen',
        name: 'customer-addresses',
        component: () => import('@theme-classic/pages/customer/CustomerAddressPage.vue')
      },
      {
        path: 'benachrichtigungen',
        name: 'customer-notifications',
        component: () => import('@theme-classic/pages/customer/CustomerNotificationsPage.vue')
      },
      {
        path: 'einstellungen',
        name: 'customer-settings',
        component: () => import('@theme-classic/pages/customer/CustomerSettingsPage.vue')
      },
    ],
  }
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
    customerRoutes,

    {
      path: '/:pathMatch(.*)*',
      name: 'NotFound',
      component: () => import('@theme-classic/pages/NotFound.vue'),
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
      return next({ name: 'frontend-login' })
    }
  }
  next()
})





export default router


// import TenantPrismFrontendLayout from '@theme-classic/layouts/TenantPrismFrontendLayout.vue'
// import { createRouter, createWebHistory } from 'vue-router'
// import axiosTenant from '@theme-classic/axios'
// import PrismCustomerFrontendLayout from '@theme-classic/layouts/PrismCustomerFrontendLayout.vue'

// // ================================
// // FRONTEND (Public Store)
// // ================================
// const frontendRoutes = [
//   {
//     path: '/',
//     component: TenantPrismFrontendLayout, // Wrapper
//     children: [
//       { path: '', name: 'frontend-home', component: () => import('@theme-classic/pages/Landing.vue') },
//       { path: 'products', name: 'frontend-products', component: () => import('@theme-classic/pages/ProductsPage.vue') },
//       { path: 'category/:slug', name: 'frontend-category', component: () => import('@theme-classic/pages/CategoryPage.vue') },
//       { path: 'product/:id', name: 'frontend-product-detail', component: () => import('@theme-classic/pages/ProductDetailPage.vue') },
//       { path: 'register', name: 'frontend-register', component: () => import('@theme-classic/pages/RegisterPage.vue') },
//       { path: 'login', name: 'frontend-login', component: () => import('@theme-classic/pages/LoginPage.vue') },
//       { path: 'cart', name: 'frontend-cart', component: () => import('@theme-classic/pages/CartPage.vue') },
//       { path: 'wishlist', name: 'frontend-wishlist', component: () => import('@theme-classic/pages/WishlistPage.vue') },
//       { path: 'checkout', name: 'frontend-checkout', component: () => import('@theme-classic/pages/CheckoutPage.vue') },

//       // Static pages
//       { path: 'imprint', name: 'imprint', component: () => import('@theme-classic/pages/ImpressumPage.vue') },
//       { path: 'terms', name: 'terms', component: () => import('@theme-classic/pages/TermsPage.vue') },
//       { path: 'privacy', name: 'privacy', component: () => import('@theme-classic/pages/PrivacyPolicyPage.vue') },
//       { path: 'cancellation', name: 'cancellation', component: () => import('@theme-classic/pages/CancellationPolicyPage.vue') },
//       { path: 'shipping-payment', name: 'shipping-payment', component: () => import('@theme-classic/pages/ShippingPaymentPage.vue') },
//       { path: 'waste-disposal', name: 'waste-disposal', component: () => import('@theme-classic/pages/WasteDisposalPage.vue') },
//       { path: 'electro-devices', name: 'electro-devices', component: () => import('@theme-classic/pages/ElectroDevicesPage.vue') },
//       { path: 'about', name: 'about', component: () => import('@theme-classic/pages/AboutPage.vue') },
//       { path: 'press', name: 'press', component: () => import('@theme-classic/pages/PressPage.vue') },
//       { path: 'faq', name: 'faq', component: () => import('@theme-classic/pages/FAQPage.vue') },
//       { path: 'contact', name: 'contact', component: () => import('@theme-classic/pages/ContactPage.vue') },
//     ]
//   }
// ]

// // ================================
// // CUSTOMER DASHBOARD
// // ================================
// const customerRoutes = {
//   path: '/account',
//   component: PrismCustomerFrontendLayout,
//   meta: { requiresAuth: true },
//   children: [
//     { path: '', name: 'customer-dashboard', component: () => import('@theme-classic/pages/customer/CustomerDashboard.vue') },
//     { path: 'orders', name: 'customer-orders', component: () => import('@theme-classic/pages/customer/CustomerOrdersPage.vue') },
//     { path: 'orders/:orderId', name: 'customer-order-tracking', component: () => import('@theme-classic/pages/customer/CustomerOrderTrackingPage.vue') },
//     { path: 'wishlist', name: 'customer-wishlist', component: () => import('@theme-classic/pages/WishlistPage.vue') },
//     { path: 'payments', name: 'customer-payments', component: () => import('@theme-classic/pages/customer/CustomerPaymentPage.vue') },
//     { path: 'addresses', name: 'customer-addresses', component: () => import('@theme-classic/pages/customer/CustomerAddressPage.vue') },
//     { path: 'notifications', name: 'customer-notifications', component: () => import('@theme-classic/pages/customer/CustomerNotificationsPage.vue') },
//     { path: 'settings', name: 'customer-settings', component: () => import('@theme-classic/pages/customer/CustomerSettingsPage.vue') },
//   ]
// }

// // ================================
// // ROUTER
// // ================================
// const router = createRouter({
//   history: createWebHistory(),
//   routes: [
//     ...frontendRoutes,
//     customerRoutes,
//     { path: '/:pathMatch(.*)*', name: 'NotFound', component: () => import('@theme-classic/pages/NotFound.vue') },
//   ],
// })

// // ================================
// // AUTH GUARD
// // ================================
// router.beforeEach((to, from, next) => {
//   if (to.matched.some(route => route.meta.requiresAuth)) {
//     const token = localStorage.getItem('tenant_token')
//     if (!token) return next({ name: 'frontend-login' })
//   }
//   next()
// })

// export default router
