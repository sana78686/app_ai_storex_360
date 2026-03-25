<template>
  <div>

    <!-- <TenantEinfacshopFrontendLayout/> -->
    <!-- <TenantPrismFrontendLayout/>   -->

 <section class="relative overflow-hidden bg-gradient-to-br from-slate-800 via-slate-700 to-slate-900">
    <div class="mx-auto grid max-w-7xl gap-8 px-4 py-16 md:grid-cols-2 md:py-20">
      <div class="flex flex-col justify-center space-y-6 text-white">
        <h1 class="text-4xl font-bold leading-tight md:text-5xl lg:text-6xl">
          {{$t(currentSlideData.title)}}
        </h1>
        <p class="max-w-xl text-lg text-slate-200">
          {{ $t(currentSlideData.description) }}
        </p>
        <router-link
          :to="currentSlideData.link"
          class="inline-flex w-fit items-center justify-center rounded-full bg-brand px-8 py-3 text-sm font-semibold text-white transition hover:bg-brand-dark"
        >
          {{ $t('common.buyNow')}}
        </router-link>
      </div>
      <div class="relative hidden md:block">
        <div class="relative aspect-square overflow-hidden rounded-lg">
          <img
            :key="currentSlide"
            :src="currentSlideData.image"
            :alt="$t(currentSlideData.category)"
            class="h-full w-full object-cover transition-opacity duration-500"
          />
        </div>
      </div>
    </div>
    <!-- Navigation arrows -->
    <button
      @click="prevSlide"
      class="absolute left-4 top-1/2 -translate-y-1/2 rounded-full bg-white/20 p-2 text-white backdrop-blur-sm transition hover:bg-white/30 z-10"
      aria-label="Vorheriger Slide">
      <ChevronLeft class="h-6 w-6" />
    </button>
    <button
      @click="nextSlide"
      class="absolute right-4 top-1/2 -translate-y-1/2 rounded-full bg-white/20 p-2 text-white backdrop-blur-sm transition hover:bg-white/30 z-10"
      aria-label="Nächster Slide"
    >
      <ChevronRight class="h-6 w-6" />
    </button>
    <!-- Slide indicators -->
    <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex gap-2 z-10">
      <button
        v-for="(_, index) in heroSlides"
        :key="index"
        @click="currentSlide = index"
        :class="[
          'h-2 rounded-full transition',
          index === currentSlide ? 'w-8 bg-white' : 'w-2 bg-white/50'
        ]"
        :aria-label="`Slide ${index + 1}`"
      />
    </div>
  </section>
    <!-- Circular Category Icons -->
    <section class="border-b border-slate-200 bg-white py-8">
      <div class="mx-auto max-w-7xl px-4">
        <div class="grid grid-cols-3 gap-6 md:grid-cols-6">
          <router-link
            v-for="(category, index) in categoryIcons"
            :key="index"
            :to="`/kategorie/${category.slug}`"
            class="group flex flex-col items-center space-y-2"
          >
            <div class="relative h-20 w-20 overflow-hidden rounded-full border-2 border-slate-200 transition group-hover:border-brand md:h-24 md:w-24">
              <img
                :src="category.image"
                :alt="$t(category.nameKey)"
                class="h-full w-full object-cover transition-transform group-hover:scale-110"
              />
            </div>
            <span class="text-center text-xs font-medium text-slate-700 group-hover:text-brand">
             {{ $t(category.nameKey) }}
            </span>
          </router-link>
        </div>
      </div>
    </section>

    <!-- Large Category Blocks -->
    <section class="border-b border-slate-200 bg-slate-50 py-12">
      <div class="mx-auto max-w-7xl px-4">
        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
          <router-link
            v-for="(block, index) in categoryBlocks"
            :key="index"
            :to="`/kategorie/${block.slug}`"
            class="group relative overflow-hidden rounded-lg bg-white shadow-sm transition hover:shadow-md"
          >
            <div class="relative aspect-[4/3] overflow-hidden">
              <img
                :src="block.image"
                :alt="block.title"
                class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105"
              />
              <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/20 to-transparent" />
              <div class="absolute bottom-0 left-0 right-0 p-6 text-white">
                <h3 class="text-xl font-bold">{{ block.title }}</h3>
                <p class="mt-1 text-sm text-slate-200">{{ block.subtitle }}</p>
              </div>
            </div>
          </router-link>
        </div>
      </div>
    </section>

    <!-- Beliebteste Produkte -->
    <ProductCarousel
      :title="$t('sections.popularProducts')"
      :products="popularProducts"
      show-all-link="/produkte?filter=beliebt"
    />
    <!-- Naturewater Banner -->
    <section class="border-b border-slate-200 bg-gradient-to-r from-blue-50 to-cyan-50 py-16">
      <div class="mx-auto grid max-w-7xl gap-8 px-4 md:grid-cols-2">
        <div class="flex flex-col justify-center space-y-4">
          <h2 class="text-3xl font-bold text-slate-900">    {{ $t('banners.naturewaterTitle') }}</h2>
          <p class="text-slate-700">
                {{ $t('banners.naturewaterText') }}
          </p>
        </div>
        <div class="relative aspect-square overflow-hidden rounded-lg">
          <img
            src="https://images.unsplash.com/photo-1514996937319-344454492b37?auto=format&fit=crop&w=800&q=80"
            alt="Naturewater"
            class="h-full w-full object-cover"
          />
        </div>
      </div>
    </section>

    <!-- Aktuelle Empfehlungen -->
    <section class="border-b border-slate-200 bg-white py-12">
      <div class="mx-auto max-w-7xl px-4">
        <h2 class="mb-8 text-2xl font-bold text-slate-900">{{ $t('sections.currentRecommendations') }}</h2>
        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-4">
          <router-link
            v-for="(category, index) in recommendationCategories"
            :key="index"
            :to="`/kategorie/${category.slug}`"
            class="group overflow-hidden rounded-lg bg-white shadow-sm transition hover:shadow-md"
          >
            <div class="relative aspect-[4/3] overflow-hidden">
              <img
                :src="category.image"
                :alt="category.title"
                class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105"
              />
            </div>
            <div class="p-4">
              <h3 class="font-bold text-slate-900">{{ category.title }}</h3>
              <ul class="mt-3 space-y-1 text-sm text-slate-600">
                <li v-for="(item, itemIndex) in category.items" :key="itemIndex" class="hover:text-brand">
                  {{ item }}
                </li>
              </ul>
            </div>
          </router-link>
        </div>
      </div>
    </section>

    <!-- Alles für Ihren Teich Banner -->
    <section class="border-b border-slate-200 bg-gradient-to-r from-green-50 to-emerald-50 py-16">
      <div class="mx-auto grid max-w-7xl gap-8 px-4 md:grid-cols-2">
        <div class="relative aspect-square overflow-hidden rounded-lg order-2 md:order-1">
          <img
            src="https://images.unsplash.com/photo-1576610616656-d3aa5d1f4534?auto=format&fit=crop&w=800&q=80"
            alt="$t('pond.imageAlt')"
            class="h-full w-full object-cover"
          />
        </div>
        <div class="flex flex-col justify-center space-y-4 order-1 md:order-2">
          <h2 class="text-3xl font-bold text-slate-900">{{ $t('pond.title') }}</h2>
          <p class="text-slate-700">
            {{ $t('pond.description') }}
          </p>
           <router-link
            :to="`/kategorie/${categorySlugs['Pool & Teich']}`"
            class="inline-flex w-fit items-center justify-center rounded-full bg-brand px-8 py-3 text-sm font-semibold text-white transition hover:bg-brand-dark"
          >
            {{ $t('common.buyNow')}}
          </router-link>

        </div>
      </div>
    </section>

    <!-- Unsere Empfehlungen -->
    <ProductCarousel
      :title="$t('sections.recommendations')"
      :products="recommendedProducts"
      show-all-link="/produkte?filter=empfehlungen"
    />

    <!-- Customer Reviews -->
    <section class="border-b border-slate-200 bg-slate-50 py-12">
      <div class="mx-auto max-w-7xl px-4">
        <h2 class="mb-8 text-2xl font-bold text-slate-900">UNSERE KUNDENSTIMMEN</h2>
        <div class="grid gap-6 md:grid-cols-3">
          <div
            v-for="i in 3"
            :key="i"
            class="rounded-lg bg-white p-6 shadow-sm"
          >
            <div class="mb-4 flex items-center space-x-1">
              <svg
                v-for="j in 5"
                :key="j"
                class="h-5 w-5 text-yellow-400"
                fill="currentColor"
                viewBox="0 0 20 20"
              >
                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
              </svg>
            </div>
            <p class="mb-4 text-sm text-slate-600">
              "Schnelle Lieferung, sehr gute Qualität. Gerne wieder!"
            </p>
            <p class="text-xs font-semibold text-slate-900">Max M.</p>
          </div>
        </div>
        <div class="mt-6 flex justify-center space-x-2">
          <button
            v-for="i in 3"
            :key="i"
            :class="[
              'h-2 rounded-full',
              i === 1 ? 'bg-brand' : 'bg-slate-300'
            ]"
          />
        </div>
      </div>
    </section>

    <!-- Socials Section -->
    <section class="border-b border-slate-200 bg-slate-50 py-12">
      <div class="mx-auto max-w-7xl px-4">
        <h2 class="mb-8 text-2xl font-bold text-slate-900">SOCIALS</h2>
        <div class="grid gap-4 md:grid-cols-4">
          <div class="relative aspect-square overflow-hidden rounded-lg md:col-span-2 md:row-span-2">
            <img
              src="https://images.unsplash.com/photo-1486262715619-67b85e0b08d3?auto=format&fit=crop&w=800&q=80"
              alt="#EINFACHSHOP24"
              class="h-full w-full object-cover"
            />
            <div class="absolute bottom-4 left-4 text-white">
              <span class="text-sm font-semibold">#EINFACHSHOP24</span>
            </div>
          </div>
          <div
            v-for="(social, index) in socials"
            :key="index"
            class="relative aspect-square overflow-hidden rounded-lg"
          >
            <img
              :src="social.image"
              :alt="social.hashtag"
              class="h-full w-full object-cover"
            />
            <div class="absolute bottom-4 left-4 text-white">
              <span class="text-sm font-semibold">{{ social.hashtag }}</span>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>




<script setup>
import { ref,computed } from 'vue'
import { ChevronLeft, ChevronRight } from 'lucide-vue-next'
import { useRouter } from 'vue-router'
// import TenantEinfacshopFrontendLayout from '@theme-prism/layouts/TenantEinfacshopFrontendLayout.vue'
import ProductCarousel from '@theme-prism/component/ProductCarousel.vue'
 import { categorySlugs } from '@theme-prism/data/products'

const popularProducts = ref([
  {
    id: '1',
    title: 'Motorrad Montageständer Heber vorne/hinten, rot, 300 kg',
    imageUrl: 'https://images.unsplash.com/photo-1558980664-2506fca6bfc2?auto=format&fit=crop&w=600&q=80',
    articleNumber: '60210',
    price: 89.99,
    oldPrice: 119.99,
    available: true
  },
  {
    id: '2',
    title: 'Naturewater Umkehrosmoseanlage 190 L/Tag, 5-stufiges Trinkwasser-Filtrationssystem',
    imageUrl: 'https://images.unsplash.com/photo-1514996937319-344454492b37?auto=format&fit=crop&w=600&q=80',
    articleNumber: '50808',
    price: 299.99,
    oldPrice: 399.99,
    available: true
  },
  {
    id: '3',
    title: 'Werkstattregal 5 Ebenen, schwarz, 200x50x180cm, max. 500kg',
    imageUrl: 'https://images.unsplash.com/photo-1519710164239-da123dc03ef4?auto=format&fit=crop&w=600&q=80',
    articleNumber: '50790',
    price: 199.99,
    oldPrice: 249.99,
    available: true
  },
  {
    id: '4',
    title: 'Hühnerstall mit Auslauf, 4-6 Hühner, wetterfest, mit Sitzstangen',
    imageUrl: 'https://images.unsplash.com/photo-1601758228041-f3b2795255f1?auto=format&fit=crop&w=600&q=80',
    articleNumber: '61916',
    price: 249.99,
    oldPrice: 299.99,
    available: true
  }
])

const recommendedProducts = ref([
  {
    id: 'rec-1',
    title: 'Green Screen Hintergrund, 2x3m, faltbar, für Foto & Video',
    imageUrl: 'https://images.unsplash.com/photo-1606983340126-99ab4feaa64a?auto=format&fit=crop&w=600&q=80',
    articleNumber: 'FOTO-005',
    price: 49.99,
    available: true
  },
  {
    id: 'rec-2',
    title: 'Gartenpavillon 3x3m, schwarz, wasserabweisend, mit Seitenwänden',
    imageUrl: 'https://images.unsplash.com/photo-1506439773649-6e0eb8cfb237?auto=format&fit=crop&w=600&q=80',
    articleNumber: 'GART-007',
    price: 199.99,
    oldPrice: 249.99,
    available: true
  },
  {
    id: 'rec-3',
    title: 'Autorampen 2er Set, 2 Tonnen, für PKW, robust',
    imageUrl: 'https://images.unsplash.com/photo-1558442074-3d1982a56c24?auto=format&fit=crop&w=600&q=80',
    articleNumber: 'AUTO-007',
    price: 89.99,
    available: true
  },
  {
    id: 'rec-4',
    title: 'Motorrad Montageständer Heber vorne/hinten, rot, 300 kg',
    imageUrl: 'https://images.unsplash.com/photo-1558980664-2506fca6bfc2?auto=format&fit=crop&w=600&q=80',
    articleNumber: '60210',
    price: 89.99,
    oldPrice: 119.99,
    available: true
  }
])
const categoryIcons = ref([
  {
    nameKey: 'categories.garden',
    slug: 'garten',
    image: 'https://images.unsplash.com/photo-1416879595882-3373a0480b5b?auto=format&fit=crop&w=400&q=80'
  },
  {
    nameKey: 'categories.poolPond',
    slug: 'pool-teich',
    image: 'https://images.unsplash.com/photo-1576610616656-d3aa5d1f4534?auto=format&fit=crop&w=400&q=80'
  },
  {
    nameKey: 'categories.homeLiving',
    slug: 'haus-wohnen',
    image: 'https://images.unsplash.com/photo-1586105251261-72a756497a11?auto=format&fit=crop&w=400&q=80'
  },
  {
    nameKey: 'categories.petSupplies',
    slug: 'tierbedarf',
    image: 'https://images.unsplash.com/photo-1601758228041-f3b2795255f1?auto=format&fit=crop&w=400&q=80'
  },
  {
    nameKey: 'categories.sportLeisure',
    slug: 'sport-freizeit',
    image: 'https://images.unsplash.com/photo-1544367567-0f2fcb009e0b?auto=format&fit=crop&w=400&q=80'
  },
  {
nameKey: 'categories.homeLiving',
slug: 'haus-wohnen',
image: 'https://images.unsplash.com/photo-1586105251261-72a756497a11?auto=format&fit=crop&w=400&q=80'
},
])


const categoryBlocks = ref([
  {
    title: 'DIY & HEIMWERKEN',
    subtitle: 'Baustoffe, Brandschutz & Co.',
    image: 'https://images.unsplash.com/photo-1504148455328-c376907d081c?auto=format&fit=crop&w=600&q=80',
    slug: 'werkstatt-diy'
  },
  {
    title: 'MOTORRADZUBEHÖR',
    subtitle: 'Montageständer, Werkzeuge & mehr',
    image: 'https://images.unsplash.com/photo-1558980664-2506fca6bfc2?auto=format&fit=crop&w=600&q=80',
    slug: 'auto-motorrad'
  },
  {
    title: 'KÜCHE & HAUSHALT',
    subtitle: 'Haushaltsgeräte & Küchenzubehör',
    image: 'https://images.unsplash.com/photo-1602143407151-7111542de6e8?auto=format&fit=crop&w=600&q=80',
    slug: 'haus-wohnen'
  },
  {
    title: 'CAMPING & OUTDOOR',
    subtitle: 'Zelte, Ausrüstung & mehr',
    image: 'https://images.unsplash.com/photo-1478131143081-80f7f84ca84d?auto=format&fit=crop&w=600&q=80',
    slug: 'sport-freizeit'
  },
  {
    title: 'GRILLEN',
    subtitle: 'Grills, Zubehör & Kohle',
    image: 'https://images.unsplash.com/photo-1529692236671-f1f6cf9683ba?auto=format&fit=crop&w=600&q=80',
    slug: 'garten'
  }
])

const recommendationCategories = ref([
  {
    title: 'GRILLEN',
    image: 'https://images.unsplash.com/photo-1529692236671-f1f6cf9683ba?auto=format&fit=crop&w=600&q=80',
    items: ['Gasgrills', 'Holzkohlegrills', 'Elektrogrills', 'Grillzubehör', 'Räucheröfen'],
    slug: 'garten'
  },
  {
    title: 'Motorradzubehör',
    image: 'https://images.unsplash.com/photo-1558980664-2506fca6bfc2?auto=format&fit=crop&w=600&q=80',
    items: ['Hebebühnen', 'Montageständer', 'Rampen', 'Transport', 'Zubehör', 'Werkzeuge'],
    slug: 'auto-motorrad'
  },
  {
    title: 'Werkstatt & Werkzeug',
    image: 'https://images.unsplash.com/photo-1504148455328-c376907d081c?auto=format&fit=crop&w=600&q=80',
    items: ['Werkbänke', 'Werkzeugwagen', 'Regale & Schränke', 'Hebebühnen', 'Werkstattwagen'],
    slug: 'werkstatt-diy'
  },
  {
    title: 'Aquarium & Zubehör',
    image: 'https://images.unsplash.com/photo-1514888286974-6c03e2ca1dba?auto=format&fit=crop&w=600&q=80',
    items: ['Aquarien', 'Filter', 'Pumpen', 'Heizungen', 'Beleuchtung', 'Dekoration'],
    slug: 'tierbedarf'
  }
])

const socials = ref([
  { hashtag: '#SOLARLAMPE', image: 'https://images.unsplash.com/photo-1601758228041-f3b2795255f1?auto=format&fit=crop&w=600&q=80' },
  { hashtag: '#TIERBEDARF', image: 'https://images.unsplash.com/photo-1601758228041-f3b2795255f1?auto=format&fit=crop&w=600&q=80' },
  { hashtag: '#KINDERSPIELZEUG', image: 'https://images.unsplash.com/photo-1504148455328-c376907d081c?auto=format&fit=crop&w=600&q=80' },
  { hashtag: '#WERKSTATT', image: 'https://images.unsplash.com/photo-1504148455328-c376907d081c?auto=format&fit=crop&w=600&q=80' }
])

const router = useRouter()

const heroSlides = [
  {
    title: 'hero.slide1.title',
    description: 'hero.slide1.description',
    image: 'https://images.unsplash.com/photo-1606983340126-99ab4feaa64a?auto=format&fit=crop&w=800&q=80',
    link: `/kategorie/${categorySlugs['Foto & Studio']}`,
    category: 'hero.slide1.category'
  },
  {
    title: 'hero.slide2.title',
    description: 'hero.slide2.description',
    image: 'https://images.unsplash.com/photo-1416879595882-3373a0480b5b?auto=format&fit=crop&w=800&q=80',
    link: `/kategorie/${categorySlugs['Garten']}`,
    category: 'hero.slide2.category'
  },
  {
    title: 'hero.slide3.title',
    description: 'hero.slide3.description',
    image: 'https://images.unsplash.com/photo-1504148455328-c376907d081c?auto=format&fit=crop&w=800&q=80',
    link: `/kategorie/${categorySlugs['Werkstatt & DIY']}`,
    category: 'hero.slide3.category'
  }
]

const currentSlide = ref(0)

const currentSlideData = computed(() => heroSlides[currentSlide.value])

const nextSlide = () => {
  currentSlide.value = (currentSlide.value + 1) % heroSlides.length
}

const prevSlide = () => {
  currentSlide.value = (currentSlide.value - 1 + heroSlides.length) % heroSlides.length
}
</script>
