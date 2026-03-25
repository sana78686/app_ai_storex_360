<template>

    <!-- ================= HEADER ================= -->
    <header class="sticky top-0 z-50 border-b border-slate-200 bg-white/90 backdrop-blur">

      <!-- TOP BAR -->
       <div class="mx-auto flex max-w-7xl items-center gap-3 px-4 py-3 sm:gap-6">
      <!-- Mobile Menu Button -->
      <button
        @click="mobileMenuOpen = !mobileMenuOpen"
        class="flex h-9 w-9 items-center justify-center rounded-full border border-slate-200 text-slate-600 hover:border-brand/60 hover:text-brand md:hidden"
        aria-label="Menu"
>
        <X v-if="mobileMenuOpen" class="h-5 w-5" />
        <Menu v-else class="h-5 w-5" />
      </button>

      <!-- Logo -->
      <router-link to="/" class="flex items-center flex-shrink-0">
        <div class="relative h-10 w-auto sm:h-12">
          <img
            src="/public/assets/logo/Einfachkaufen24.png"
            alt="EinfachShop24 Logo"
            class="h-10 w-auto object-contain sm:h-12"
          />
        </div>
      </router-link>

      <!-- Search - Desktop -->
      <div class="hidden flex-1 md:block">
        <div class="relative">
          <Search class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />

          <input
  type="search"
  :placeholder="$t('header.search_placeholder')"
  class="h-10 w-full rounded-full border border-slate-300 pl-10 pr-4 text-sm"
/>
        </div>
      </div>

      <!-- Mobile Search Button -->
      <button
        @click="mobileSearchOpen = !mobileSearchOpen"
        class="flex h-9 w-9 items-center justify-center rounded-full border border-slate-200 text-slate-600 hover:border-brand/60 hover:text-brand md:hidden"
        aria-label="Search"
      >
        <Search class="h-4 w-4" />
      </button>

      <!-- Icons -->
      <div class="ml-auto flex items-center gap-2">
        <!-- Language Selector -->
        <div class="relative hidden sm:block">
          <button
            @click="langDropdownOpen = !langDropdownOpen"
            class="flex h-9 items-center gap-2 rounded-full border border-slate-200 px-3 text-xs font-medium text-slate-700 hover:border-brand/60 hover:text-brand transition"
          >
            <span class="text-base">{{ selectedLang.flag }}</span>
            <span class="hidden md:inline">{{ selectedLang.code.toUpperCase() }}</span>
            <ChevronDown class="h-3 w-3" />
          </button>
          <div v-if="langDropdownOpen" class="fixed inset-0 z-10" @click="langDropdownOpen = false" />
          <div
            v-if="langDropdownOpen"
            class="absolute right-0 top-full z-20 mt-2 w-48 rounded-lg border border-slate-200 bg-white shadow-lg"
          >
            <button
              v-for="lang in languages"
              :key="lang.code"
              @click="selectLanguage(lang)"
              :class="[
                'w-full flex items-center gap-3 px-4 py-2 text-sm hover:bg-slate-50 transition',
                selectedLang.code === lang.code ? 'bg-slate-50' : ''
              ]"
            >
              <span class="text-lg">{{ lang.flag }}</span>
              <span class="flex-1 text-left">{{ lang.name }}</span>
              <span v-if="selectedLang.code === lang.code" class="text-brand">✓</span>
            </button>
          </div>
        </div>

       <router-link
  :to="{ name: 'frontend-wishlist' }"
          class="flex h-10 w-10 items-center justify-center rounded-full border border-slate-200 text-slate-600 hover:border-brand/60 hover:text-brand touch-manipulation"
          aria-label="Wunschliste"
        >
          <Heart class="h-5 w-5" />
        </router-link>
  <router-link
  :to="isAuthenticated
    ? { name: 'customer-dashboard' }
    : { name: 'frontend-login' }"
  class="hidden h-10 items-center gap-2 rounded-full border border-slate-200 px-3 text-xs font-medium text-slate-700 hover:border-brand/60 hover:text-brand md:flex"
>
  <User class="h-4 w-4" />
  <span>
    {{ isAuthenticated ? $t('header.account') : $t('header.register') }}
  </span>
</router-link>
<router-link
  :to="{ name: 'frontend-cart' }"
  class="relative flex h-10 items-center gap-2 rounded-full bg-brand px-2 sm:px-3 text-xs font-semibold text-white hover:bg-brand-dark touch-manipulation min-w-[44px]"
>
  <ShoppingCart class="h-5 w-5" />
  <span class="hidden sm:inline">{{ $t('header.cart') }}</span>
  <span
    v-if="cartItemsCount > 0"
    class="absolute -right-1 -top-1 flex h-5 w-5 items-center justify-center rounded-full bg-white text-[10px] font-bold text-brand"
  >
    {{ cartItemsCount > 99 ? '99+' : cartItemsCount }}
  </span>
</router-link>
      </div>
    </div>

       <!-- Mobile Search Bar -->
<div v-if="mobileSearchOpen" class="border-t border-slate-200 bg-white px-4 py-3 md:hidden">
      <div class="relative">
        <Search class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />

        <input
  type="search"
  :placeholder="$t('header.search_placeholder')"
  class="h-10 w-full rounded-full border border-slate-300 pl-10 pr-4 text-sm"
  autofocus
/>
      </div>
    </div>
    <!-- Mobile Menu Overlay -->
    <template v-if="mobileMenuOpen">
      <div
        class="fixed inset-0 z-30 bg-black/50 md:hidden"
        @click="mobileMenuOpen = false"
      />
      <div class="fixed left-0 top-0 z-40 h-full w-64 bg-white shadow-xl md:hidden">
        <div class="flex h-14 items-center justify-between border-b border-slate-200 px-4">
          <span class="font-semibold text-slate-900"> {{ $t('header.menu') }}</span>
          <button
            @click="mobileMenuOpen = false"
            class="flex h-9 w-9 items-center justify-center rounded-full hover:bg-slate-100"
          >
            <X class="h-5 w-5" />
          </button>
        </div>
        <nav class="overflow-y-auto p-4">
          <router-link
            to="/"
            class="block rounded-lg px-4 py-3 text-sm font-medium text-slate-700 hover:bg-slate-50"
            @click="mobileMenuOpen = false"
          >
            Startseite
          </router-link>
          <router-link
            to="/produkte"
            class="block rounded-lg px-4 py-3 text-sm font-medium text-slate-700 hover:bg-slate-50"
            @click="mobileMenuOpen = false"
          >
            Alle Produkte
          </router-link>
          <div class="my-2 border-t border-slate-200" />
          <p class="px-4 py-2 text-xs font-semibold uppercase text-slate-500">Kategorien</p>
         <router-link
  v-for="cat in categories"
  :key="cat.id"
  :to="`/kategorie/${cat.slug}`"
  class="block rounded-lg px-4 py-3 text-sm text-slate-700 hover:bg-slate-50"
  @click="mobileMenuOpen = false"
>
  {{ cat.label }}
</router-link>




        </nav>
      </div>
    </template>

      <!-- ================= MEGA MENU ================= -->
 <nav
    class="hidden border-t border-slate-200 bg-slate-50 md:block"
    @mouseleave="open = false"
  >
    <div class="mx-auto max-w-7xl px-4 py-2">
      <div class="flex flex-col items-center gap-3 md:flex-row md:justify-between">
        <div class="flex flex-wrap items-center justify-center gap-3 text-xs font-medium text-slate-700">
          <button
            v-for="cat in megaMenuData"
            :key="cat.id"
            @mouseenter="setActive(cat)"
            :class="[
              'rounded-md px-3 py-2 transition-colors',

              active?.id === cat.id && open

                ? 'bg-white text-brand shadow-sm'
                : 'hover:bg-white hover:text-brand'
            ]"
          >
            {{ cat.title }}
          </button>
        </div>
        <div class="flex items-center justify-center gap-2 text-center">
          <div class="flex items-center gap-2 rounded-full bg-gradient-to-r from-brand/10 to-brand/5 px-4 py-2 shadow-sm ring-1 ring-brand/20">
            <Star class="h-3.5 w-3.5 fill-brand text-brand" />
            <span class="text-xs font-semibold text-slate-900">Top Qualität</span>
            <span class="text-xs text-slate-600">zu günstigen Preisen</span>
          </div>
          <div class="flex items-center gap-2 rounded-full bg-gradient-to-r from-brand/10 to-brand/5 px-4 py-2 shadow-sm ring-1 ring-brand/20">
            <Heart class="h-3.5 w-3.5 fill-brand text-brand" />
            <span class="text-xs font-semibold text-slate-900">Einzigartiger</span>
            <span class="text-xs text-slate-600">Kundenservice</span>
          </div>
        </div>
      </div>
    </div>

    <div v-if="open" class="border-t border-slate-200 bg-white shadow-lg">
      <div class="mx-auto grid max-w-7xl grid-cols-1 gap-6 px-4 py-5 md:grid-cols-12">
        <div class="md:col-span-8 grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-4 items-start">
          <router-link
            v-for="child in active?.children || []"
            :key="child.id"
            :to="`/kategorie/${child.slug}`"
            class="group block rounded-md border border-slate-100 bg-slate-50/60 p-3 text-sm font-semibold text-slate-800 hover:border-brand/40 hover:bg-white hover:text-brand transition"
          >
            {{ child.name }}
            <span class="block text-xs font-normal text-slate-500 group-hover:text-slate-600">
              Jetzt entdecken
            </span>
          </router-link>
        </div>
        <div class="md:col-span-4 flex items-center gap-4 rounded-md bg-gradient-to-r from-slate-50 to-white border border-slate-100 p-4">
          <div class="h-24 w-24 overflow-hidden rounded-md border border-slate-100 bg-slate-100">
            <img
              :src="active?.image"
              :alt="active?.title"
              class="h-full w-full object-cover"
            />
          </div>
          <div class="flex-1" v-if="active">
            <h3 class="text-base font-semibold text-slate-900">
              {{ active?.title }}
            </h3>
            <p class="text-sm text-slate-600">
              {{ (active?.children || []).slice(0, 3).map(ch => ch.name).join(' • ') }}
            </p>
            <router-link
              :to="`/kategorie/${active.slug}`"
              class="mt-2 inline-flex items-center text-sm font-semibold text-brand hover:underline"
            >
              Produkte ansehen
            </router-link>
          </div>
        </div>
      </div>
    </div>
  </nav>
      <!-- ================= ANNOUNCEMENT BANNER ================= -->
       <div class="relative overflow-hidden border-b border-slate-200 bg-gradient-to-r from-brand via-brand-dark to-brand py-2">
    <div class="flex animate-scroll-banner whitespace-nowrap">
      <div
        v-for="(announcement, index) in duplicatedAnnouncements"
        :key="index"
        class="flex items-center justify-center px-8 text-sm font-semibold text-white"
      >
        <span>{{ announcement }}</span>
        <span class="mx-8 text-white/50">•</span>
      </div>
    </div>
  </div>
    </header>

    <!-- ================= PAGE CONTENT ================= -->

  <router-view />

    <!-- ================= FOOTER ================= -->


  <footer class="mt-10 border-t border-slate-200 bg-gradient-to-b from-slate-50 to-white">
    <div class="mx-auto grid max-w-7xl gap-6 sm:gap-8 px-3 sm:px-4 py-8 sm:py-12 text-xs sm:text-sm text-slate-700 grid-cols-2 sm:grid-cols-2 md:grid-cols-4">
      <!-- Kontakt & Abholung -->
      <div>
        <h3 class="mb-3 sm:mb-4 text-xs sm:text-sm font-bold uppercase tracking-wide text-slate-900">
          Kontakt & Abholung
        </h3>
        <ul class="space-y-2 sm:space-y-3">
          <li class="font-semibold text-slate-900">Support & Abholung</li>
          <li class="flex items-start gap-2">
            <Clock class="h-4 w-4 text-brand mt-0.5 flex-shrink-0" />
            <span>Mo–Sa, 10:00–18:00 Uhr</span>
          </li>
          <li class="flex items-start gap-2">
            <Phone class="h-4 w-4 text-brand mt-0.5 flex-shrink-0" />
            <a
              href="tel:+491709014419"
              class="hover:text-brand transition-colors font-medium"
            >
              +49 170 901 4419
            </a>
          </li>
          <li class="flex items-start gap-2">
            <MapPin class="h-4 w-4 text-brand mt-0.5 flex-shrink-0" />
            <a
              href="https://maps.google.com/?q=Am%20Boscheler%20Berg%208d,%2052134%20Herzogenrath"
              class="hover:text-brand transition-colors leading-relaxed"
              target="_blank"
              rel="noopener noreferrer"
            >
              Am Boscheler Berg 8d<br />
              52134 Herzogenrath
            </a>
          </li>
        </ul>
      </div>

      <!-- Rechtliche Informationen -->
      <div>
        <h3 class="mb-3 sm:mb-4 text-xs sm:text-sm font-bold uppercase tracking-wide text-slate-900">
          Rechtliche Informationen
        </h3>
        <ul class="space-y-1.5 sm:space-y-2">
          <li>
            <router-link to="/impressum" class="hover:text-brand transition-colors block py-1">
              Impressum
            </router-link>
          </li>
          <li>
            <router-link to="/agb" class="hover:text-brand transition-colors block py-1">
              AGB & Kundeninformationen
            </router-link>
          </li>
          <li>
            <router-link to="/datenschutz" class="hover:text-brand transition-colors block py-1">
              Datenschutzerklärung
            </router-link>
          </li>
          <li>
            <router-link to="/widerruf" class="hover:text-brand transition-colors block py-1">
              Widerrufsbelehrung & -formular
            </router-link>
          </li>
          <li>
            <router-link to="/versand-zahlung" class="hover:text-brand transition-colors block py-1">
              Versand & Zahlung
            </router-link>
          </li>
          <li>
            <router-link to="/entsorgung" class="hover:text-brand transition-colors block py-1">
              Entsorgung & Umweltschutz
            </router-link>
          </li>
          <li>
            <router-link to="/elektrogeraete" class="hover:text-brand transition-colors block py-1">
              Informationen zu Elektro- und Elektronikgeräten
            </router-link>
          </li>
        </ul>
      </div>

      <!-- Unternehmen -->
      <div>
        <h3 class="mb-4 text-sm font-bold uppercase tracking-wide text-slate-900">
          Unternehmen
        </h3>
        <ul class="space-y-2">
          <li>
            <router-link to="/ueber-uns" class="hover:text-brand transition-colors block py-1">
              Über uns
            </router-link>
          </li>
          <li>
            <router-link to="/presse" class="hover:text-brand transition-colors block py-1">
              Presse
            </router-link>
          </li>
          <li>
            <router-link to="/faq" class="hover:text-brand transition-colors block py-1">
              FAQ
            </router-link>
          </li>
          <li>
            <router-link to="/kontakt" class="hover:text-brand transition-colors block py-1">
              Kontaktformular
            </router-link>
          </li>
        </ul>
      </div>

      <!-- Zahlung & Versand -->
      <div>
        <h3 class="mb-4 text-sm font-bold uppercase tracking-wide text-slate-900">
          Zahlung & Versand
        </h3>
        <div class="space-y-4">
          <div>
            <p class="mb-3 text-xs font-medium text-slate-600">
              Sichere Bezahlung mit:
            </p>
            <div class="flex flex-wrap gap-2.5">
              <div
                v-for="method in paymentMethods"
                :key="method.name"
                class="group relative inline-flex items-center justify-center rounded-lg bg-white p-2.5 shadow-sm ring-1 ring-slate-200 hover:ring-brand/60 hover:shadow-md transition-all duration-200 h-12 min-w-[60px]"
                :title="method.name"
              >
                <img
                  v-if="method.image"
                  :src="method.image"
                  :alt="method.alt"
                  class="h-7 w-auto max-w-[70px] object-contain transition-transform duration-200 group-hover:scale-105"
                  @error="handleImageError($event, method)"
                  loading="lazy"
                />
                <span
                  v-else
                  class="text-xs font-medium text-slate-700 px-2"
                >
                  {{ method.name }}
                </span>
              </div>
            </div>
          </div>
          <div>
            <p class="mb-3 text-xs font-medium text-slate-600">
              Versandpartner:
            </p>
            <div class="flex flex-wrap gap-2.5">
              <div
                v-for="carrier in shippingCarriers"
                :key="carrier.name"
                class="group relative inline-flex items-center justify-center rounded-lg bg-white p-2.5 shadow-sm ring-1 ring-slate-200 hover:ring-brand/60 hover:shadow-md transition-all duration-200 h-12 min-w-[60px]"
                :title="carrier.name"
              >
                <img
                  v-if="carrier.image"
                  :src="carrier.image"
                  :alt="carrier.alt"
                  class="h-7 w-auto max-w-[70px] object-contain transition-transform duration-200 group-hover:scale-105"
                  @error="handleImageError($event, carrier)"
                  loading="lazy"
                />
                <span
                  v-else
                  class="text-xs font-medium text-slate-700 px-2"
                >
                  {{ carrier.name }}
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Bottom Bar -->
    <div class="border-t border-slate-200 bg-white">
      <div class="mx-auto flex max-w-7xl flex-col items-center justify-between gap-3 px-4 py-6 text-xs text-slate-500 sm:flex-row">
        <p class="text-center sm:text-left">
          © {{ new Date().getFullYear() }} EinfachShop24. Alle Rechte vorbehalten.
        </p>
        <p class="text-center sm:text-right">
          Alle Preise inkl. MwSt. zzgl. Versandkosten.
        </p>
      </div>
    </div>
  </footer>
</template>


<script setup>
import { ref, computed, onMounted } from 'vue'
import { useCartStore } from '@theme-prism/stores/cartStore'
import { useAuthStore } from '@theme-prism/stores/authStore'
import axiosTenant from '@theme-prism/axios'
import {Heart, ShoppingCart, User, Search, Menu, X,ChevronDown,Star } from 'lucide-vue-next'
import { MapPin, Phone, Clock } from 'lucide-vue-next'
import { useI18n } from 'vue-i18n'
import { paymentMethods, shippingCarriers } from '@theme-prism/config/paymentShipping'


const { locale, tm } = useI18n()



// Payment & Shipping — i18n integrated
// const paymentMethods = computed(() =>
//   tm('footer.paymentShipping.methods')
// )
// const shippingCarriers = computed(() =>
//   tm('footer.paymentShipping.carriers')
// )
/* ===== STORES ===== */
const cartStore = useCartStore()
const authStore = useAuthStore()

/* ===== LANGUAGE ===== */

const languages = [
  { code: 'de', name: 'Deutsch', flag: '🇩🇪' },
  { code: 'en', name: 'English', flag: '🇬🇧' }
]


const selectedLang = ref(languages.find(l => l.code === locale.value) || languages[0])

const selectLanguage = (lang) => {
  selectedLang.value = lang
  locale.value = lang.code          //  THIS LINE SWITCHES WHOLE SITE
  localStorage.setItem('site-lang', lang.code)
  langDropdownOpen.value = false
}

// Auto load saved language
const savedLang = localStorage.getItem('site-lang')
if (savedLang) {
  locale.value = savedLang
  selectedLang.value = languages.find(l => l.code === savedLang) || languages[0]
}
/* ===== CATEGORIES FROM API ===== */
function slugFromName(name) {
  if (!name) return ''
  return String(name)
    .toLowerCase()
    .replace(/\s*&\s*/g, '-')
    .replace(/[^a-z0-9\u00C0-\u024F-]+/g, '-')
    .replace(/-+/g, '-')
    .replace(/^-|-$/g, '')
}

const categories = ref([])
const megaMenuData = ref([])
const categoriesLoading = ref(true)

const PLACEHOLDER_IMAGE = 'https://images.unsplash.com/photo-1501004318641-b39e6451bec6?auto=format&fit=crop&w=500&q=70'

onMounted(async () => {
  try {
    const res = await axiosTenant.get('/categories')
    const list = res.data?.categories ?? []
    const topLevel = list.filter(c => c.is_active !== false)
    categories.value = topLevel.map(c => ({
      id: c.id,
      label: c.name,
      slug: slugFromName(c.name)
    }))
    megaMenuData.value = topLevel.map(c => ({
      id: c.id,
      title: c.name,
      slug: slugFromName(c.name),
      children: (c.children || []).filter(ch => ch.is_active !== false).map(ch => ({
        id: ch.id,
        name: ch.name,
        slug: slugFromName(ch.name)
      })),
      image: PLACEHOLDER_IMAGE
    }))
    if (megaMenuData.value.length && !active.value) {
      active.value = megaMenuData.value[0]
    }
  } catch (err) {
    console.error('Failed to load categories:', err)
  } finally {
    categoriesLoading.value = false
  }
})

const langDropdownOpen = ref(false)
const mobileSearchOpen = ref(false)
const mobileMenuOpen = ref(false)


const cartItemsCount = computed(() => cartStore.totalItems)
const isAuthenticated = computed(() => authStore.isAuthenticated)
/* ===== MEGA MENU ===== */
const active = ref(null)

const open = ref(false)

const setActive = (cat) => {
  active.value = cat
  open.value = true
}
/* ===== ANNOUNCEMENTS ===== */
const announcements = [
  '🎉 NEUJAHR SALE - Bis zu 50% Rabatt auf ausgewählte Artikel!',
  '🚚 Kostenloser Versand ab 50€ Bestellwert',
  '⭐ Top Qualität zu günstigen Preisen',
  '💳 Sichere Zahlung mit PayPal, Kreditkarte & auf Rechnung',
  '🔄 30 Tage Rückgaberecht - Jederzeit kündbar'
]


const duplicatedAnnouncements = computed(() => [...announcements, ...announcements])




const handleImageError = (event, item) => {
  // Hide image and show text fallback
  const img = event.target
  const parent = img.parentElement

  if (img && parent && !parent.querySelector('span.fallback-text')) {
    img.style.display = 'none'
    const span = document.createElement('span')
    span.className = 'fallback-text text-xs font-medium text-slate-700 px-2'
    span.textContent = item.name
    parent.appendChild(span)
  }
}
</script>


