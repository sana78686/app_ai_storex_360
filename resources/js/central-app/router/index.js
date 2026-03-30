import { createRouter, createWebHistory } from 'vue-router'
import CentralDashboardLayout from '@/central-app/layouts/DashboardLayout.vue';
// ... existing code ...
import DashboardHome from '@/central-app/pages/DashboardHome.vue'
import Permissions from '@/central-app/pages/Permissions.vue'
import Plans from '@/central-app/pages/Plans.vue'
import Tenants from '@/central-app/pages/Tenants.vue'
import Roles from '@/central-app/pages/Roles.vue'
// import CentralLandingPage from '../views/CentralLandingPage.vue'
// import tenantRoutes from '../../router/tenant'


const routes = [
    // {
    //     path: '',
    //     name: 'landing',
    //     component: CentralLandingPage,
    //     meta: { requiresAuth: false }
    // },
     {
        path: '',
        name: 'register',
        component: () => import('@/central-app/pages/Register.vue'),
        meta: { requiresAuth: false }
    },
    {
        path: '/dashboard',
        name: 'dashboard',
        component: CentralDashboardLayout,
        meta: { requiresAuth: true, title: 'Dashboard' },
        children: [
            {
                path: '',
                name: 'dashboard-home',
                component: DashboardHome,
                meta: { requiresAuth: true, title: 'Dashboard Overview' }
            },
            {
                path: 'permissions',
                name: 'permissions',
                component: Permissions,
                meta: { requiresAuth: true, title: 'Permissions Management' }
            },
            {
                path: 'plans',
                name: 'plans',
                component: Plans,
                meta: { requiresAuth: true, title: 'plans Management' }
            },
            {
                path: 'roles',
                name: 'roles',
                component: Roles,
                meta: { requiresAuth: true, title: 'Roles Management' }
            },
            {
                path: 'tenants',
                name: 'tenants',
                component: Tenants,
                meta: { requiresAuth: true, title: 'Tenants Management' }
            }
        ]
    },
    {
        path: '/login',
        name: 'login',
        component: () => import('@/central-app/pages/Login.vue'),
        meta: { requiresAuth: false }
    },

    {
        path: '/signup',
        name: 'signup',
        component: () => import('@/central-app/pages/Signup.vue'),
        meta: { requiresAuth: false }
    },
    {
        path: '/find-tenant',
        name: 'find-tenant',
        component: () => import('@/central-app/pages/TenantLookup.vue'),
        meta: { requiresAuth: false }
    },
    {
      path: '/tenant/confirm',
      name: 'tenant-confirmation',
      component: () => import('@/central-app/pages/TenantConfirmation.vue'),
      props: (route) => ({ tenant_id: route.query.tenant_id }), // Pass tenant_id from query params
      meta: { requiresAuth: false, title: 'Account Confirmation' }
    },
    {
        path: '/SocialiteApiCallback',
        name: 'SocialiteApiCallback',
        component: () => import('@/central-app/pages/SocialiteApiCallback.vue'),
        meta: { requiresAuth: false }
      },
// ✅ Merge tenant routes here
//   ...tenantRoutes,
    {
      path: '/:pathMatch(.*)*',
      name: 'NotFound',
      component: () => import('@/central-app/pages/NotFound.vue')
    }
    // {
    //     path: '/central/auth/google/callback',
    //     name: 'central-auth-google-api-callback',
    //     component: () => import('@/views/pages/central/SocialiteApiCallback.vue'),
    //     meta: { requiresAuth: false }
    //   }
]

const router = createRouter({
    history: createWebHistory(),
    scrollBehavior () {
        return { top: 0 }
      },
    routes
})

router.beforeEach((to, from, next) => {
//   const host = window.location.hostname

//   if (/^tenant-[\w-]+\.localhost$/.test(host)) {
//     return next(false) // ❌ stop completely — don't try to route
//   }

  // Auth check if needed
  const requiresAuth = to.matched.some(record => record.meta.requiresAuth)
  if (requiresAuth && !localStorage.getItem('central_token')) {
    // return next({ path: '/login', query: { redirect: to.fullPath } })
  }

  next()
})

export default router
