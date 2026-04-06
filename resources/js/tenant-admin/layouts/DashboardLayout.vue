<template>
  <div class="gull-admin min-h-screen bg-[#f3f4f6] text-gray-800 antialiased">
    <!-- Top bar — white, full width (Gull-style) -->
    <header
      class="gull-header fixed left-0 right-0 top-0 z-50 flex h-16 items-center gap-3 border-b border-gray-200/90 bg-white px-3 shadow-sm sm:px-4 lg:pl-[4.5rem] lg:pr-6"
      style="padding-top: env(safe-area-inset-top, 0px)"
    >
      <button
        type="button"
        class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl text-gray-600 transition-colors hover:bg-gray-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#275a19]/40 lg:hidden"
        :aria-label="t('adminLayout.menu')"
        @click="toggleSidebar"
      >
        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M4 6h16M4 12h16M4 18h16"
          />
        </svg>
      </button>

      <router-link
        to="/dashboard"
        class="flex shrink-0 items-center gap-2 rounded-xl py-1 no-underline focus:outline-none focus-visible:ring-2 focus-visible:ring-[#275a19]/40 lg:hidden"
      >
        <img
          :src="logoUrl"
          alt=""
          class="h-8 max-w-[120px] object-contain sm:h-9"
        />
      </router-link>

      <router-link
        to="/dashboard"
        class="hidden shrink-0 items-center gap-2 rounded-xl py-1 no-underline focus:outline-none focus-visible:ring-2 focus-visible:ring-[#275a19]/40 lg:flex"
      >
        <img
          :src="logoUrl"
          alt=""
          class="h-8 max-w-[120px] object-contain sm:h-9"
        />
      </router-link>

      <div class="mx-2 hidden min-w-0 flex-1 justify-center md:flex">
        <div
          class="flex w-full max-w-xl items-center gap-2 rounded-full border border-gray-200/80 bg-gray-100/90 px-3 py-1.5 shadow-inner"
        >
          <svg class="h-4 w-4 shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
            />
          </svg>
          <input
            type="search"
            class="min-w-0 flex-1 border-0 bg-transparent text-sm text-gray-800 placeholder:text-gray-500 focus:outline-none focus:ring-0"
            :placeholder="t('adminLayout.searchPlaceholder')"
            autocomplete="off"
          />
          <button
            type="button"
            class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-[#275a19] text-white shadow-sm transition-transform hover:scale-105 active:scale-95 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#275a19] focus-visible:ring-offset-2"
            :aria-label="t('search')"
          >
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
              />
            </svg>
          </button>
        </div>
      </div>

      <div class="ml-auto flex min-w-0 shrink-0 items-center gap-1 sm:gap-2">
        <router-link
          to="/dashboard/order/create"
          class="flex shrink-0 items-center rounded-xl bg-[#275a19] px-3 py-2 text-xs font-bold text-white no-underline shadow-sm transition-colors hover:bg-[#1f4814] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#275a19]/50"
        >
          {{ t('adminLayout.pos') }}
        </router-link>

        <div class="flex min-w-0 items-center gap-1.5">
          <label class="sr-only" for="tenant-header-store-theme">{{ t('adminLayout.storeTheme') }}</label>
          <i class="fas fa-palette hidden shrink-0 text-xs text-gray-400 sm:block" aria-hidden="true" />
          <select
            id="tenant-header-store-theme"
            v-model="selectedTheme"
            class="max-w-[5.75rem] truncate rounded-lg border border-gray-200 bg-gray-50 py-1.5 pl-2 pr-7 text-[11px] font-medium text-gray-800 focus:border-[#275a19] focus:outline-none focus:ring-2 focus:ring-[#275a19]/15 sm:max-w-[10rem] sm:text-xs"
            :title="t('adminLayout.storeTheme')"
            @change="changeTheme"
          >
            <option v-for="th in themes" :key="th.value" :value="th.value">
              {{ th.label }}
            </option>
          </select>
        </div>

        <div class="relative header-language-dropdown">
          <button
            type="button"
            class="flex items-center gap-1 rounded-xl px-2.5 py-2 text-xs font-bold uppercase tracking-wide text-gray-700 transition-colors hover:bg-gray-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#275a19]/40"
            @click.stop="showHeaderLanguageDropdown = !showHeaderLanguageDropdown"
          >
            <span>{{ locale }}</span>
            <svg class="h-3 w-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path d="M19 9l-7 7-7-7" />
            </svg>
          </button>
          <div
            v-if="showHeaderLanguageDropdown"
            class="absolute right-0 top-full z-[60] mt-1 min-w-[160px] rounded-xl border border-gray-200 bg-white py-1 shadow-lg"
          >
            <button
              v-for="lang in availableLanguages"
              :key="lang.code"
              type="button"
              class="flex w-full items-center justify-between px-4 py-2.5 text-left text-sm text-gray-800 hover:bg-gray-50"
              @click="switchLanguage(lang.code)"
            >
              <span class="font-medium">{{ lang.name }}</span>
              <span v-if="locale === lang.code" class="font-bold text-[#275a19]">✓</span>
            </button>
          </div>
        </div>

        <button
          type="button"
          class="relative hidden rounded-xl p-2 text-gray-600 transition-colors hover:bg-gray-100 sm:flex"
          :aria-label="t('adminLayout.notifications')"
        >
          <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"
            />
          </svg>
          <span
            class="absolute right-1 top-1 h-2 w-2 rounded-full bg-[#275a19] ring-2 ring-white"
            aria-hidden="true"
          />
        </button>

        <div class="relative profile-dropdown">
          <button
            type="button"
            class="flex items-center rounded-full p-0.5 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#275a19] focus-visible:ring-offset-2"
            @click.stop="showProfileDropdown = !showProfileDropdown"
          >
            <span
              class="flex h-9 w-9 items-center justify-center rounded-full bg-gradient-to-br from-[#275a19] to-[#1a4012] text-sm font-bold text-white shadow-md ring-2 ring-white"
            >
              {{ userInitial }}
            </span>
          </button>
          <div
            v-if="showProfileDropdown"
            class="absolute right-0 top-full z-[60] mt-2 w-[min(calc(100vw-2rem),260px)] overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-xl"
          >
            <div class="border-b border-gray-100 bg-gray-50 px-4 py-3">
              <p class="truncate text-sm font-bold capitalize text-gray-900">{{ displayName }}</p>
              <p class="truncate text-xs text-gray-500">{{ user?.email }}</p>
            </div>
            <div class="py-1">
              <router-link
                to="/profile"
                class="flex items-center px-4 py-2.5 text-sm text-gray-700 no-underline hover:bg-gray-50"
                @click="showProfileDropdown = false"
              >
                <i class="fas fa-user-circle mr-3 w-5 text-center text-gray-400" />
                {{ t('adminLayout.yourProfile') }}
              </router-link>
              <button
                type="button"
                class="flex w-full cursor-pointer items-center border-0 bg-transparent px-4 py-2.5 text-left text-sm text-red-600 hover:bg-red-50"
                @click="handleLogout"
              >
                <i class="fas fa-sign-out-alt mr-3 w-5 text-center text-red-400" />
                {{ t('adminLayout.signOut') }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </header>

    <div class="flex pt-16">
      <!-- Desktop: icon rail always visible; flyout opens on hover only -->
      <div
        class="gull-dual-rail fixed bottom-0 left-0 top-16 z-40 hidden h-[calc(100vh-4rem)] w-[4.5rem] flex-row border-r border-gray-200/90 bg-white shadow-sm lg:flex"
        style="padding-bottom: env(safe-area-inset-bottom, 0px)"
        @mouseenter="cancelHideHover"
        @mouseleave="scheduleHideHover"
      >
        <nav
          class="no-scrollbar relative z-10 flex w-[4.5rem] shrink-0 flex-col items-stretch gap-0.5 overflow-y-auto border-r border-gray-100 bg-white py-2"
          aria-label="Main modules"
        >
          <button
            v-for="section in navSections"
            :key="section.id"
            type="button"
            class="gull-primary-item relative flex flex-col items-center gap-1 rounded-lg border-0 px-1 py-2.5 text-center transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-[#275a19]/40"
            :class="primaryItemClass(section)"
            @mouseenter="hoveredSectionId = section.id"
            @click="onPrimaryClick(section)"
            @focus="hoveredSectionId = section.id"
          >
            <i
              :class="[
                section.icon,
                'text-[17px] leading-none',
                isPrimaryRouteActive(section) ? 'text-[#275a19]' : 'text-gray-400',
              ]"
              aria-hidden="true"
            />
            <span
              class="max-w-full truncate px-0.5 text-[9px] font-semibold leading-tight"
              :class="isPrimaryRouteActive(section) ? 'text-[#275a19]' : 'text-gray-500'"
            >
              {{ section.label }}
            </span>
            <span
              v-if="isPrimaryRouteActive(section)"
              class="gull-primary-notch pointer-events-none absolute right-0 top-1/2 z-10 -translate-y-1/2"
              aria-hidden="true"
            />
          </button>
        </nav>

        <aside
          v-if="flyoutSection"
          class="no-scrollbar absolute bottom-0 left-full top-0 z-20 flex w-[13rem] flex-col overflow-y-auto border-r border-gray-200/90 bg-white py-3 pl-2 pr-1 shadow-lg"
          aria-label="Section pages"
        >
          <p
            class="mb-2 truncate px-2 text-[11px] font-bold uppercase tracking-wider text-gray-400"
          >
            {{ flyoutSection.label }}
          </p>
          <div class="flex flex-col gap-0.5">
            <router-link
              v-for="child in flyoutSection.children"
              :key="child.path"
              :to="child.path"
              class="flex items-center gap-2 rounded-lg px-2.5 py-2 text-sm font-medium no-underline transition-colors"
              :class="
                isChildActive(child.path)
                  ? 'bg-[#275a19]/10 font-semibold text-[#275a19]'
                  : 'text-gray-600 hover:bg-gray-200 hover:text-gray-900'
              "
            >
              <i
                v-if="child.icon"
                :class="[
                  child.icon,
                  'w-4 shrink-0 text-center text-[13px]',
                  isChildActive(child.path) ? 'text-[#275a19]' : 'text-gray-400',
                ]"
                aria-hidden="true"
              />
              <span class="min-w-0 leading-snug">{{ child.label }}</span>
            </router-link>
          </div>
        </aside>
      </div>

      <!-- Mobile drawer -->
      <aside
        :class="[
          'gull-sidebar-mobile fixed bottom-0 left-0 top-16 z-40 flex w-[min(19rem,calc(100vw-2.5rem))] flex-col border-r border-gray-200/90 bg-white shadow-xl transition-transform duration-300 ease-out lg:hidden',
          sidebarOpen ? 'translate-x-0' : '-translate-x-full',
        ]"
        :style="{ paddingBottom: 'env(safe-area-inset-bottom, 0px)' }"
      >
        <nav class="no-scrollbar flex-1 space-y-1 overflow-y-auto p-2" aria-label="Main">
          <div v-for="section in navSections" :key="section.id" class="rounded-xl border border-gray-100">
            <button
              type="button"
              class="flex w-full items-center gap-2 rounded-t-xl border-0 bg-gray-50/80 px-3 py-2.5 text-left text-sm font-semibold text-gray-800"
              @click="toggleMobileSection(section.id)"
            >
              <i :class="[section.icon, 'w-5 shrink-0 text-center text-[#275a19]']" aria-hidden="true" />
              <span class="min-w-0 flex-1">{{ section.label }}</span>
              <svg
                class="h-4 w-4 shrink-0 text-gray-400 transition-transform"
                :class="expandedMobileSectionId === section.id ? 'rotate-180' : ''"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
              </svg>
            </button>
            <div
              v-show="expandedMobileSectionId === section.id"
              class="space-y-0.5 border-t border-gray-100 p-2"
            >
              <router-link
                v-for="child in section.children"
                :key="child.path"
                :to="child.path"
                class="block rounded-lg px-3 py-2 text-sm font-medium no-underline transition-colors"
                :class="
                  isChildActive(child.path)
                    ? 'bg-[#275a19]/10 font-semibold text-[#275a19]'
                    : 'text-gray-600 hover:bg-gray-200'
                "
                @click="closeSidebarMobile"
              >
                {{ child.label }}
              </router-link>
            </div>
          </div>
        </nav>
      </aside>

      <!-- Main -->
      <main
        class="gull-main min-h-[calc(100vh-4rem)] w-full flex-1 transition-[margin] duration-300 ease-out lg:ml-[4.5rem]"
        :style="{ paddingBottom: 'env(safe-area-inset-bottom, 0px)' }"
      >
        <div class="tenant-dashboard-body mx-auto max-w-[1800px] px-3 py-3 sm:px-4 sm:py-4 lg:px-5">
          <slot />
        </div>
      </main>
    </div>

    <div
      v-if="sidebarOpen"
      class="fixed inset-0 z-30 bg-gray-900/40 backdrop-blur-[2px] lg:hidden"
      aria-hidden="true"
      @click="sidebarOpen = false"
    />
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useI18n } from 'vue-i18n'
import axiosTenant from '@/api/axiosTenant'
import Swal from 'sweetalert2'

const SITE_LANG_KEY = 'site-lang'

const route = useRoute()
const router = useRouter()
const { locale, t } = useI18n()

const sidebarOpen = ref(false)
const showHeaderLanguageDropdown = ref(false)
const showProfileDropdown = ref(false)
const user = ref(null)
const hoveredSectionId = ref(null)
const expandedMobileSectionId = ref(null)
let hideHoverTimer = null

const availableLanguages = [
  { code: 'en', name: 'English' },
  { code: 'de', name: 'Deutsch' },
]

const logoUrl = '/assets/logo/Einfachkaufen24.png'

const navSections = computed(() => [
  {
    id: 'home',
    icon: 'fas fa-th-large',
    label: t('adminLayout.nav.home'),
    defaultPath: '/dashboard',
    children: [{ path: '/dashboard', label: t('adminLayout.nav.overview'), icon: 'fas fa-home' }],
  },
  {
    id: 'orders',
    icon: 'fas fa-shopping-bag',
    label: t('adminLayout.nav.orders'),
    defaultPath: '/dashboard/orderlist',
    extraMatch: (p) => {
      if (!p.startsWith('/dashboard/orders/')) return false
      if (p.startsWith('/dashboard/orders/drafts')) return false
      if (p.startsWith('/dashboard/orders/abandoned')) return false
      return true
    },
    children: [
      { path: '/dashboard/order/create', label: t('adminLayout.nav.posCreateOrder'), icon: 'fas fa-cash-register' },
      { path: '/dashboard/orderlist', label: t('adminLayout.nav.orderList'), icon: 'fas fa-list' },
      { path: '/dashboard/orders/drafts', label: t('adminLayout.nav.drafts'), icon: 'fas fa-file-alt' },
      {
        path: '/dashboard/orders/abandoned',
        label: t('adminLayout.nav.abandonedCheckouts'),
        icon: 'fas fa-shopping-cart',
      },
    ],
  },
  {
    id: 'catalog',
    icon: 'fas fa-tags',
    label: t('adminLayout.nav.products'),
    defaultPath: '/dashboard/products',
    children: [
      { path: '/dashboard/products', label: t('adminLayout.nav.products'), icon: 'fas fa-box-open' },
      {
        path: '/dashboard/products/create',
        label: t('adminLayout.nav.addProduct'),
        icon: 'fas fa-plus-circle',
      },
    ],
  },
  {
    id: 'customers',
    icon: 'fas fa-users',
    label: t('adminLayout.nav.customers'),
    defaultPath: '/customers',
    children: [{ path: '/customers', label: t('adminLayout.nav.customers'), icon: 'fas fa-user-friends' }],
  },
  {
    id: 'settings',
    icon: 'fas fa-sliders-h',
    label: t('adminLayout.settings'),
    defaultPath: '/dashboard/settings/general',
    extraMatch: (p) => p === '/dashboard/settings' || p.startsWith('/dashboard/settings/'),
    children: [
      { path: '/dashboard/settings/general', label: t('adminLayout.settingsNav.general'), icon: 'fas fa-cog' },
      {
        path: '/dashboard/settings/localization',
        label: t('adminLayout.settingsNav.localization'),
        icon: 'fas fa-globe',
      },
      { path: '/dashboard/settings/legal', label: t('adminLayout.settingsNav.legalInfo'), icon: 'fas fa-file-alt' },
      {
        path: '/dashboard/settings/email',
        label: t('adminLayout.settingsNav.emailNotifications'),
        icon: 'fas fa-envelope',
      },
      { path: '/dashboard/settings/coupons', label: t('adminLayout.settingsNav.coupons'), icon: 'fas fa-ticket-alt' },
      {
        path: '/dashboard/settings/invoices',
        label: t('adminLayout.settingsNav.invoices'),
        icon: 'fas fa-file-invoice-dollar',
      },
      { path: '/dashboard/settings/domains', label: t('adminLayout.settingsNav.domains'), icon: 'fas fa-globe-americas' },
    ],
  },
])

const pinnedSectionId = computed(() => {
  const path = route.path
  let bestId = 'home'
  let bestLen = -1
  for (const s of navSections.value) {
    if (s.extraMatch?.(path)) {
      return s.id
    }
    for (const c of s.children) {
      if (path === c.path || path.startsWith(`${c.path}/`)) {
        if (c.path.length > bestLen) {
          bestLen = c.path.length
          bestId = s.id
        }
      }
    }
  }
  return bestId
})

/** Flyout panel: only while hovering the icon rail (or focused primary item). */
const flyoutSection = computed(() => {
  const id = hoveredSectionId.value
  if (!id) return null
  return navSections.value.find((s) => s.id === id) ?? null
})

function isPrimaryRouteActive(section) {
  return pinnedSectionId.value === section.id
}

function primaryItemClass(section) {
  if (isPrimaryRouteActive(section)) {
    return 'bg-[#275a19]/8 text-[#275a19]'
  }
  if (hoveredSectionId.value === section.id) {
    return 'bg-gray-200/90 text-gray-800'
  }
  return 'bg-transparent text-gray-600 hover:bg-gray-100'
}

function isChildActive(childPath) {
  const path = route.path
  if (path === childPath) return true
  if (childPath === '/dashboard' && (path === '/dashboard' || path === '/dashboard/')) return true
  if (childPath !== '/dashboard' && path.startsWith(`${childPath}/`)) return true
  return false
}

function cancelHideHover() {
  if (hideHoverTimer) {
    clearTimeout(hideHoverTimer)
    hideHoverTimer = null
  }
}

function scheduleHideHover() {
  cancelHideHover()
  hideHoverTimer = setTimeout(() => {
    hoveredSectionId.value = null
    hideHoverTimer = null
  }, 160)
}

function onPrimaryClick(section) {
  const target = section.defaultPath ?? section.children[0]?.path
  if (target) router.push(target)
}

function toggleMobileSection(id) {
  expandedMobileSectionId.value = expandedMobileSectionId.value === id ? null : id
}

const switchLanguage = (langCode) => {
  locale.value = langCode
  try {
    localStorage.setItem(SITE_LANG_KEY, langCode)
  } catch (_) {
    /* ignore */
  }
  showHeaderLanguageDropdown.value = false
}

const toggleSidebar = () => {
  sidebarOpen.value = !sidebarOpen.value
}

const closeSidebarMobile = () => {
  if (typeof window !== 'undefined' && window.innerWidth < 1024) {
    sidebarOpen.value = false
  }
}

watch(
  () => route.fullPath,
  () => {
    closeSidebarMobile()
  }
)

watch(sidebarOpen, (open) => {
  if (typeof document === 'undefined' || typeof window === 'undefined') return
  document.body.classList.toggle('overflow-hidden', open && window.innerWidth < 1024)
  if (open && window.innerWidth < 1024) {
    expandedMobileSectionId.value = pinnedSectionId.value
  }
})

watch(pinnedSectionId, (id) => {
  if (typeof window !== 'undefined' && window.innerWidth < 1024 && sidebarOpen.value) {
    expandedMobileSectionId.value = id
  }
})

const displayName = computed(() => {
  if (user.value?.name) return user.value.name
  if (user.value?.email) return user.value.email.split('@')[0]
  return 'User'
})

const userInitial = computed(() => displayName.value.charAt(0).toUpperCase())

const handleLogout = async () => {
  try {
    await axiosTenant.post('/logout')
  } catch (error) {
    console.error('Logout API error:', error.response?.data?.message || error.message)
  } finally {
    localStorage.removeItem('tenant_token')
    localStorage.removeItem('tenant_user')
    localStorage.removeItem('token')
    showProfileDropdown.value = false
    router.push('/dashboard/login')
  }
}

const handleClickOutside = (event) => {
  if (!event.target.closest('.profile-dropdown')) showProfileDropdown.value = false
  if (!event.target.closest('.header-language-dropdown')) showHeaderLanguageDropdown.value = false
}

const themes = ref([])
const selectedTheme = ref('prism')

onMounted(() => {
  document.addEventListener('click', handleClickOutside)

  const storedUser = localStorage.getItem('tenant_user')
  if (storedUser) {
    try {
      user.value = JSON.parse(storedUser)
    } catch (e) {
      console.error('Failed to parse user data', e)
    }
  }

  ;(async () => {
    try {
      const res = await axiosTenant.get('/settings/general')
      selectedTheme.value = res.data.theme ?? 'prism'
    } catch (e) {
      console.error('Failed to load theme')
    }
    try {
      const res = await axiosTenant.get('/settings/themes')
      themes.value = res.data?.themes ?? []
      if (!themes.value.length) {
        themes.value = [{ label: 'Prism', value: 'prism' }]
      }
    } catch (e) {
      themes.value = [{ label: 'Prism', value: 'prism' }]
      console.error('Failed to load theme options')
    }
  })()
})

onUnmounted(() => {
  cancelHideHover()
  document.removeEventListener('click', handleClickOutside)
  document.body.classList.remove('overflow-hidden')
})

const changeTheme = async () => {
  try {
    await axiosTenant.post('/settings/theme', {
      theme: selectedTheme.value,
    })
  } catch (e) {
    await Swal.fire({
      icon: 'error',
      title: 'Theme',
      text: 'Failed to change theme.',
    })
  }
}
</script>

<style scoped>
.no-scrollbar::-webkit-scrollbar {
  display: none;
}
.no-scrollbar {
  -ms-overflow-style: none;
  scrollbar-width: none;
}

.gull-primary-notch {
  width: 0;
  height: 0;
  border-top: 7px solid transparent;
  border-bottom: 7px solid transparent;
  border-left: 7px solid #275a19;
  margin-right: -1px;
}
</style>
