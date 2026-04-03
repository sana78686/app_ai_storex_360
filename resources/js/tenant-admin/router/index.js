// import TenantPrismFrontendLayout from '@/tenant-admin/layout/TenantPrismFrontendLayout.vue'
import { createRouter, createWebHistory } from 'vue-router'
// import axiosTenant from '@/api/axiosTenant'
// import PrismCustomerFrontendLayout from '../views/layout/PrismCustomerFrontendLayout.vue'

// ================================
// FRONTEND (Public Store)
// ================================



// ================================
// CUSTOMER DASHBOARD
// ================================

// ================================
// ADMIN / SYSTEM
// ================================
const systemRoutes = [
    // { path: '/maintenance', name: 'Maintenance', component: () => import('@/tenant-admin/Maintenance.vue' )},

   {
  path: '/dashboard/login',
  name: 'admin-login',
  component: () => import('../auth/Login.vue')
},
{
  path: '/dashboard/forgot-password',
  name: 'admin-forgot-password',
  component: () => import('../auth/ForgetPassword.vue')
},
{
  path: '/dashboard/reset-password',
  name: 'admin-reset-password',
  component: () => import('../auth/ResetPassword.vue')
},
{
  path: '/dashboard/register',
  name: 'tenant-register',
  component: () => import('../auth/Register.vue')
},
    {
        path: '/dashboard',
        name: 'dashboard',
        component: () => import('@/tenant-admin/pages/Default.vue'),
        // component: () => import('@/tenant-admin/pages/Dashboard.vue'),
        meta: { requiresAuth: true },
        children: [
            {
                path: '',
                name: 'dashboard-home',
                component: () => import('@/tenant-admin/pages/DashboardHome.vue'),
                meta: { requiresAuth: true }

            },
            {
                path: 'users',
                name: 'tenant-users',
                component: () => import('@/tenant-admin/pages/settings/Users.vue'),
                meta: { requiresAuth: true }
            },
            {
                path: 'category',
                name: 'category',
                component: () => import('@/tenant-admin/pages/Category.vue'),
                meta: { requiresAuth: true }
            },
            {
                path: 'permissions',
                name: 'permissions',
                component: () => import('@/tenant-admin/pages/Permission.vue'),
                meta: { requiresAuth: true }
            },
            {
                path: 'order/create',
                name: 'order-create',
                component: () => import('@/tenant-admin/pages/OrderCreate.vue'),
                meta: { requiresAuth: true }
            },
            {
                path: 'products/create',
                name: 'product-create',
                component: () => import('@/tenant-admin/pages/ProductCreatePage.vue'),
                meta: { requiresAuth: true }
            },
            {
                path: 'products/:id/edit',
                name: 'product/edit',
                component: () => import('@/tenant-admin/pages/ProductEdit.vue'),
                meta: { requiresAuth: true }
            },
            {
                path: 'products',
                name: 'product-list',
                component: () => import('@/tenant-admin/pages/InventoryList.vue'),
                // component: () => import('@/tenant-admin/pages/product/ProductList.vue'),
                meta: { requiresAuth: true }
            },
            {
                path: 'roles',
                name: 'roles',
                component: () => import('@/tenant-admin/pages/settings/Role.vue'),
                meta: { requiresAuth: true }
            },
            {
                path: 'attributes',
                name: 'attributes',
                component: () => import('@/tenant-admin/pages/Attribute.vue'),
                meta: { requiresAuth: true }
            },
            // {
            //     path: 'settings',
            //     name: 'settings',
            //     component: () => import('@/tenant-admin/pages/Settings.vue'),
            //     meta: { requiresAuth: true }
            // },
            {
                path: 'pricing',
                name: 'pricing',
                component: () => import('@/tenant-admin/pages/settings/Pricing.vue'),
                meta: { requiresAuth: true }
            },
            {
                path: 'payments',
                name: 'payments',
                component: () => import('@/tenant-admin/pages/settings/Payments.vue'),
                meta: { requiresAuth: true }
            },
            {
                path: 'orderlist',
                name: 'orderlist',
                component: () => import('@/tenant-admin/pages/OrderList.vue'),
                meta: { requiresAuth: true }
            },
            {
                path: 'orders/drafts',
                name: 'draftorders',
                component: () => import('@/tenant-admin/pages/DraftOrders.vue'),
                meta: { requiresAuth: true }
            },
            {
                path: 'orders/abandoned',
                name: 'abandonedorders',
                component: () => import('@/tenant-admin/pages/Abandoned.vue'),
                meta: { requiresAuth: true }
            },
            {
                path: 'orders/:id',
                name: 'orders',
                component: () => import('@/tenant-admin/pages/OrderDetail.vue'),
                meta: { requiresAuth: true }
            },
            {
                path: 'invoicetemplateform',
                name: 'invoicetemplateform',
                component: () => import('@/tenant-admin/pages/settings/InvoiceTemplateForm.vue'),
                meta: { requiresAuth: true }
            },
            {
                path: 'settings',
                component: () => import('@/tenant-admin/layouts/Settingslayout.vue'),
                meta: { requiresAuth: true },
                redirect: { name: 'settings-general' },
                children: [
                    {
                        path: 'general',
                        name: 'settings-general',
                        component: () => import('@/tenant-admin/pages/settings/GeneralSettings.vue'),
                        meta: { requiresAuth: true },
                    },
                    {
                        path: 'localization',
                        name: 'settings-localization',
                        component: () => import('@/tenant-admin/pages/settings/LocalizationSettings.vue'),
                        meta: { requiresAuth: true },
                    },
                    {
                        path: 'legal',
                        name: 'settings-legal',
                        component: () => import('@/tenant-admin/pages/settings/LegalInfo.vue'),
                        meta: { requiresAuth: true },
                    },
                    {
                        path: 'email',
                        name: 'settings-email',
                        component: () => import('@/tenant-admin/pages/settings/MailSettings.vue'),
                        meta: { requiresAuth: true },
                    },
                    {
                        path: 'coupons',
                        name: 'settings-coupons',
                        component: () => import('@/tenant-admin/pages/settings/CouponParent.vue'),
                        meta: { requiresAuth: true },
                    },
                    {
                        path: 'invoices',
                        name: 'settings-invoices',
                        component: () => import('@/tenant-admin/pages/settings/InvoiceSettings.vue'),
                        meta: { requiresAuth: true },
                    },
                    {
                        path: 'domains',
                        name: 'settings-domains',
                        component: () => import('@/tenant-admin/pages/settings/Domains.vue'),
                        meta: { requiresAuth: true },
                    },
                ],
            },
        ]
    },

    // {
    //     path: '/:pathMatch(.*)*',
    //     name: 'NotFound',
    //     component: () => import('@/tenant-admin/NotFound.vue')
    // }
]

// ================================
// ROUTER
// ================================
const router = createRouter({
  history: createWebHistory(),
  scrollBehavior () {
    return { top: 0 }
  },
  routes: [


    // ...frontendRoutes,
    // customerRoutes,
    ...systemRoutes,
    {
      path: '/:pathMatch(.*)*',
      name: 'NotFound',
      component: () => import('@/tenant-admin/pages/NotFound.vue'),
    },
  ],
})



// const router = createRouter({
//     history: createWebHistory(),
//     routes
// })
// router.beforeEach((to, from, next) => {
//     const host = window.location.hostname;

//     // ✅ Only allow tenant domain
//     const isTenantDomain = /^tenant-[\w-]+\.localhost$/.test(host);

//     if (!isTenantDomain) {
//       // Block tenant routes on central domain
//       return next({ path: '/' });
//     }

//     // ✅ Auth check
//     const requiresAuth = to.matched.some(record => record.meta.requiresAuth);
//     const token = localStorage.getItem('tenant_token');

//     if (requiresAuth && !token) {
//       return next({ path: '/login', query: { redirect: to.fullPath } });
//     }

//     next();
//   });
// const routesToCheck = ['tenant-home', 'Products', 'Maintenance']

// let cachedSettings = null

// router.beforeEach(async (to, from, next) => {
//   // Do NOT block the maintenance page itself
//   if (to.name === 'Maintenance') {
//     return next()
//   }

//   if (routesToCheck.includes(to.name)) {
//     try {
//       // Fetch settings only once
//       if (!cachedSettings) {
//         const res = await axiosTenant.get('/settings/general')
//         cachedSettings = res.data
//       }

//       const settings = cachedSettings

//       // ------------------------------
//       // 🚧 CASE 1: Maintenance mode ON
//       // ------------------------------
//       if (settings.maintenance_mode === 1) {
//         if (to.name !== 'Maintenance') {
//           return next({
//             name: 'Maintenance',
//             // query: {
//             //   message: settings.maintenance_message,
//             //   logo: settings.logo ? `/storage/${settings.logo}` : null
//             // }
//           })
//         }
//         return next()
//       }

      // ------------------------------
      // ✔ CASE 2: Maintenance mode OFF
      // ------------------------------
//       if (settings.maintenance_mode === 0) {
//         if (to.name !== 'Products') {
//           return next({ name: 'Products' })
//         }
//         return next()
//       }

//     } catch (err) {
//       console.error('Failed to fetch general settings', err)
//     }
//   }

//   next()
// })

// ================================
// AUTH GUARD
// ================================
router.beforeEach((to, from, next) => {
  if (to.matched.some(route => route.meta.requiresAuth)) {
    const token = localStorage.getItem('tenant_token')
    if (!token) {
      return next({ name: 'admin-login' })
    }
  }
  next()
})





export default router
