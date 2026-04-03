<template>
  <div class="min-h-screen bg-[#F0F2F2] dashboard-font text-[#0F1111]">
    <header class="fixed top-0 left-0 right-0 z-50 shadow-sm">
      <div class="flex items-center justify-between px-4 bg-[#131921] h-14">
        <div class="flex items-center space-x-4">
          <button
  @click="toggleSidebar"
  class="md:hidden p-2 text-white rounded-sm transition-all hover:outline hover:outline-1 hover:outline-white"
>
  <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
      d="M4 6h16M4 12h16M4 18h16" />
  </svg>
</button>


          <div class="flex items-center space-x-3">
  <router-link to="/dashboard" class="flex items-center">
    <img
      :src="logoUrl"
      alt="Logo"
      class="h-8 brightness-0 invert object-contain cursor-pointer"
    />
  </router-link>
</div>

        </div>

        <div class="hidden lg:flex items-center flex-1 max-w-xl mx-8">
          <div class="flex w-full group">
            <input type="text" placeholder="Search for features, products or help"
              class="flex-1 bg-white px-4 py-2 text-sm focus:outline-none rounded-l-sm border-2 border-transparent focus:border-orange-500" />
            <button class="bg-[#febd69] hover:bg-[#f3a847] px-1 rounded-r-sm transition-colors">
              <svg class="w-5 h-5 text-[#131921]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
              </svg>
            </button>
          </div>
        </div>

        <div class="flex items-center space-x-2 md:space-x-5">
  <div class="relative header-language-dropdown group">
    <button @click="showHeaderLanguageDropdown = !showHeaderLanguageDropdown"
      class="flex items-center space-x-1 px-2 py-1.5  text-white text-xs font-bold transition-all">
      <span class="uppercase">{{ locale }}</span>
      <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7"></path></svg>
    </button>
    <div v-if="showHeaderLanguageDropdown" class="absolute top-full right-0 mt-1 bg-white border border-gray-200 shadow-xl z-50 min-w-[140px] rounded-sm py-2">
      <button v-for="lang in availableLanguages" :key="lang.code" @click="switchLanguage(lang.code)"
        class="w-full text-left px-4 py-2 text-xs text-gray-700 hover:bg-gray-100 flex items-center justify-between">
        <span class="font-medium">{{ lang.name }}</span>
        <span v-if="locale === lang.code" class="text-orange-500 font-bold">✓</span>
      </button>
    </div>
  </div>

  <button class="hidden sm:block text-white hover:text-orange-400">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
  </button>

  <div class="h-8 w-px bg-gray-700 hidden md:block"></div>

  <div class="relative profile-dropdown">
  <button @click="showProfileDropdown = !showProfileDropdown"
    class="flex items-center space-x-3 px-2 py-1  transition-all">

    <div class="w-8 h-8 rounded-full bg-[#008B9C] flex items-center justify-center text-white font-bold text-sm border border-white/20">
      {{ userInitial }}
    </div>

    <!-- <div class="hidden md:block text-left">
      <p class="text-[10px] text-gray-400 leading-none">Hello, {{ displayName }}</p>
      <p class="text-xs text-white font-bold flex items-center">
        Account & Lists
        <svg class="w-3 h-3 ml-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path d="M19 9l-7 7-7-7"></path>
        </svg>
      </p>
    </div> -->
  </button>

  <div v-if="showProfileDropdown" class="absolute top-full right-0 mt-1 bg-white border border-gray-200 shadow-2xl z-50 min-w-[220px] rounded-sm overflow-hidden animate-in fade-in slide-in-from-top-1">
    <div class="p-4 bg-gray-50 border-b border-gray-100">
      <p class="text-sm font-bold text-gray-900 capitalize">{{ displayName }}</p>
      <p class="text-xs text-gray-500 truncate">{{ user?.email }}</p>
    </div>

    <div class="py-1">
      <router-link to="/profile" class="flex items-center px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-100 no-underline">
        <i class="fas fa-user-circle w-5 mr-3 text-gray-400"></i>
        Your Profile
      </router-link>

      <button @click="handleLogout" class="w-full flex items-center px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 no-underline border-none bg-transparent cursor-pointer">
        <i class="fas fa-sign-out-alt w-5 mr-3 text-red-400"></i>
        Sign Out
      </button>
    </div>
  </div>
</div>
</div>
      </div>


    </header>

    <div class="flex pt-14 min-h-screen">
     <aside
  :class="[
    'fixed left-0 top-14 bottom-0 w-64 bg-white border-r border-gray-200 z-40 flex flex-col justify-between transition-transform duration-300',
    sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'
  ]"
>
  <!-- Menu items -->
  <nav class="flex-1 overflow-y-auto p-2">
    <div v-for="item in menu" :key="item.path" class="mb-1">
      <router-link
        :to="item.path"
        class="flex items-center px-3 py-2 text-sm rounded transition-colors no-underline group"
        :class="[
          isActive(item) ? 'bg-gray-100 font-semibold text-black' : 'text-gray-700 hover:bg-gray-100'
        ]"
      >
        <i :class="[item.icon, 'w-5 mr-3', isActive(item) ? 'text-black' : 'text-gray-500 group-hover:text-black']"></i>
        <span class="flex-1">{{ item.label }}</span>
      </router-link>

      <div v-if="item.children && isActive(item)" class="mt-1 ml-8 space-y-1">
        <router-link
          v-for="child in item.children"
          :key="child.path"
          :to="child.path"
          class="block px-3 py-1.5 text-sm rounded no-underline"
          :class="[
            route.path === child.path ? 'bg-white font-medium text-black' : 'text-gray-600 hover:bg-gray-100'
          ]"
        >
          {{ child.label }}
        </router-link>
      </div>
    </div>
  </nav>
<!-- Theme Selector -->
<div class="px-3 py-3 border-t border-gray-200">
  <label class="block text-xs font-semibold text-gray-500 mb-1">
    Store Theme
  </label>

  <select
    v-model="selectedTheme"
    @change="changeTheme"
    class="w-full text-sm border border-gray-300 rounded px-2 py-1 focus:outline-none focus:ring-1 focus:ring-orange-400"
  >
    <option v-for="theme in themes" :key="theme.value" :value="theme.value">
      {{ theme.label }}
    </option>
  </select>
</div>
  <!-- Sticky bottom button -->
  <div class="p-4 border-t border-gray-200 lg:mt-auto">
    <router-link
      to="/dashboard/settings"
      class="flex items-center no-underline px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded transition-colors"
      active-class="bg-gray-100 font-semibold text-black"
    >
      <i class="fas fa-cog w-5 mr-3 text-gray-500 group-hover:text-black"></i>
      <span>Settings</span>
    </router-link>
  </div>
</aside>


      <main class="flex-1 lg:ml-64 p-4 md:p-8 flex flex-col">
        <div class="flex-grow">
          <slot />
        </div>

        <!-- <footer class="mt-12 border-t border-gray-300 pt-8 pb-12">
          <div class="max-w-6xl mx-auto flex flex-col md:flex-row justify-between items-center px-4">
            <div class="flex flex-wrap justify-center md:justify-start gap-6 text-[11px] font-bold text-[#0066c0]">
              <a href="#" class="hover:underline hover:text-orange-600">Help</a>
              <a href="#" class="hover:underline hover:text-orange-600">Policies</a>

              <div class="relative language-dropdown">
                <button @click="showLanguageDropdown = !showLanguageDropdown" class="flex items-center space-x-1 text-gray-500">
                  <span>{{ currentLanguage }}</span>
                  <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <div v-if="showLanguageDropdown" class="absolute bottom-full left-0 mb-2 bg-white border border-gray-300 shadow-lg p-2 min-w-[120px]">
                   <button v-for="lang in availableLanguages" :key="lang.code" @click="switchLanguage(lang.code)"
                    class="block w-full text-left px-2 py-1 hover:bg-gray-100">
                    {{ lang.name }}
                  </button>
                </div>
              </div>
            </div>

            <div class="mt-6 md:mt-0 flex items-center space-x-4">
              <img :src="logoUrl" alt="Logo" class="h-5 opacity-50 grayscale" />
              <p class="text-[11px] text-gray-500 font-medium">© 2026 Einfachkaufen24. All rights reserved.</p>
            </div>
          </div>
        </footer> -->
      </main>
    </div>

    <div v-if="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 bg-black/50 z-30 lg:hidden"></div>
  </div>
</template>

<script setup>
    import { useRoute } from 'vue-router'
    import axiosTenant from '@/api/axiosTenant'
    import Swal from 'sweetalert2'
const route = useRoute()
/* ... (Logic stays exactly as you provided, keeping all refs and functions) ... */
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useI18n } from 'vue-i18n'

const { locale } = useI18n()
const sidebarOpen = ref(false)
const currentSlide = ref(0)
const totalSlides = ref(2)
const showLanguageDropdown = ref(false)
const showHeaderLanguageDropdown = ref(false)

const availableLanguages = [
  { code: 'en', name: 'English' },
  { code: 'de', name: 'Deutsch' }
]

const currentLanguage = computed(() => {
  const currentLang = availableLanguages.find(lang => lang.code === locale.value)
  return currentLang ? currentLang.name : 'English'
})

const switchLanguage = (langCode) => {
  locale.value = langCode
  showLanguageDropdown.value = false
  showHeaderLanguageDropdown.value = false
}

const toggleSidebar = () => { sidebarOpen.value = !sidebarOpen.value }
const nextSlide = () => { currentSlide.value = (currentSlide.value + 1) % totalSlides.value }
const previousSlide = () => { currentSlide.value = currentSlide.value === 0 ? totalSlides.value - 1 : currentSlide.value - 1 }





const user = ref(null)

// Parse user data from localStorage on component mount
onMounted(() => {
  const storedUser = localStorage.getItem('tenant_user')
  if (storedUser) {
    try {
      user.value = JSON.parse(storedUser)
    } catch (e) {
      console.error("Failed to parse user data", e)
    }
  }
})

// Dynamic display name: Use name if available, otherwise use part of email before @
const displayName = computed(() => {
  if (user.value?.name) return user.value.name
  if (user.value?.email) return user.value.email.split('@')[0]
  return 'User'
})

// Dynamic Initial for the Avatar
const userInitial = computed(() => {
  return displayName.value.charAt(0).toUpperCase()
})

// Logout Functionality
const handleLogout = async () => {
  try {
    // 1. Call the logout API
    // This typically invalidates the token on the server side
    await axiosTenant.post('/logout')
  } catch (error) {
    console.error('Logout API error:', error.response?.data?.message || error.message)
    // We continue with local logout even if the server call fails
  } finally {
    // 2. Clear local storage
    // Matches your login keys: 'tenant_token' and 'tenant_user'
    localStorage.removeItem('tenant_token')
    localStorage.removeItem('tenant_user')

    // Optional: Clear any other leftover auth keys
    localStorage.removeItem('token')

    // 3. Close the UI dropdown
    showProfileDropdown.value = false

    // 4. Redirect to login page
    router.push('/login')
  }
}

const handleClickOutside = (event) => {

  if (!event.target.closest('.profile-dropdown')) showProfileDropdown.value = false
  if (!event.target.closest('.language-dropdown')) showLanguageDropdown.value = false
  if (!event.target.closest('.header-language-dropdown')) showHeaderLanguageDropdown.value = false
}

onMounted(() => { document.addEventListener('click', handleClickOutside) })
onUnmounted(() => { document.removeEventListener('click', handleClickOutside) })

const logoUrl = '/assets/logo/Einfachkaufen24.png'

//     const menu = [
//   { path: '/dashboard', icon: 'fas fa-tachometer-alt', label: 'Home' },
//    { path: '/dashboard/orderlist', icon: 'fas fa-shopping-cart', label: 'Orders' },
//   { path: '/dashboard/products', icon: 'fas fa-box', label: 'Products' },
// //   { path: '/dashboard/products/create', icon: 'fas fa-plus', label: 'Add Product' },
//   { path: '/customers', icon: 'fas fa-users', label: 'Customers' },
//   { path: '/dashboard/users', icon: 'fas fa-user', label: 'Users' },
// //   { path: '/dashboard/category', icon: 'fas fa-tags', label: 'Category' },
//   { path: '/dashboard/permissions', icon: 'fas fa-key', label: 'Permissions' },
//   { path: '/dashboard/roles', icon: 'fas fa-user-shield', label: 'Roles' },
// //   { path: '/dashboard/attributes', icon: 'fas fa-list', label: 'Attributes' },
//   { path: '/dashboard/settings', icon: 'fas fa-cog', label: 'Settings' },
//   { path: '/dashboard/payments', icon: 'fas fa-cog', label: 'payments' },
// //   { path: '/dashboard/orderlist', icon: 'fas fa-cog', label: 'orderlist' },
//   { path: '/dashboard/invoicetemplateform', icon: 'fas fa-cog', label: 'invoicetemplateform' },
// ]
const menu = [
  { path: '/dashboard', icon: 'fas fa-home', label: 'Home' },
  {
    path: '/dashboard/orderlist',
    icon: 'fas fa-shopping-bag',
    label: 'Orders',
    children: [
    //   { path: '/dashboard/orderlist', label: 'Orders' }, // Main index
      { path: '/dashboard/orders/drafts', label: 'Drafts' },
      { path: '/dashboard/orders/abandoned', label: 'Abandoned checkouts' }
    ]
  },
  { path: '/dashboard/products', icon: 'fas fa-tag', label: 'Products' },
  { path: '/customers', icon: 'fas fa-users', label: 'Customers' },
//   { path: '/dashboard/settings', icon: 'fas fa-cog', label: 'Settings' },
  // ... rest of your items
]

// Generic active check
const isActive = (item) => {
  if (!item.children) return route.path === item.path

  // Parent is active if current path equals parent path or starts with any child's path
  return (
    route.path === item.path ||
    (item.children && item.children.some(child => route.path.startsWith(child.path)))
  )
}

// ... existing imports
const showProfileDropdown = ref(false) // Add this line

// Add click outside logic for the new dropdown

const themes = ref([])

// Load current theme from backend (recommended)
const selectedTheme = ref('prism')

onMounted(async () => {
  try {
    const res = await axiosTenant.get('/settings/general')
    selectedTheme.value = res.data.theme ?? 'prism'
  } catch (e) {
    console.error('Failed to load theme')
  }
})

onMounted(async () => {
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
})

const changeTheme = async () => {
  try {
    await axiosTenant.post('/settings/theme', {
      theme: selectedTheme.value,
    })

    // 🔥 Hard reload so Blade re-evaluates @vite
    // window.location.reload()
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
</style>
