<template>
  <div v-if="loading" class="flex min-h-screen items-center justify-center bg-slate-50">
    <div class="text-center">
      <p class="text-slate-600">{{ $t('productDetail.loading') }}</p>
    </div>
  </div>
  <div v-else-if="!product" class="flex min-h-screen items-center justify-center bg-slate-50">
    <div class="text-center">
      <h1 class="text-2xl font-bold text-slate-900 mb-2">{{ $t('productDetail.notFound') }}</h1>
      <p class="text-slate-600 mb-4">{{ $t('productDetail.notFoundDescription') }}</p>
      <router-link to="/" class="inline-block rounded-full bg-brand px-6 py-3 text-sm font-semibold text-white hover:bg-brand-dark">
        {{ $t('productDetail.backToHome') }}
      </router-link>
    </div>
  </div>
  <div v-else>
    <!-- Breadcrumb -->
    <div class="border-b border-slate-200 bg-white">
      <div class="mx-auto max-w-7xl px-4 py-2 sm:py-3">
        <nav class="text-xs sm:text-sm text-slate-600 overflow-x-auto">
          <div class="flex items-center whitespace-nowrap">
            <router-link to="/" class="hover:text-brand transition-colors">
              Startseite
            </router-link>
            <span class="mx-2">/</span>
            <router-link
              v-if="categorySlug"
              :to="`/kategorie/${categorySlug}`"
              class="hover:text-brand transition-colors"
            >
              {{ product.category }}
            </router-link>
            <span v-else>{{ product.category }}</span>
            <span class="mx-2">/</span>
            <span class="text-slate-900 truncate max-w-[150px] sm:max-w-none">{{ product.title }}</span>
          </div>
        </nav>
      </div>
    </div>

    <div class="mx-auto max-w-7xl px-4 py-6">
      <!-- Main Product Display -->
      <div class="grid gap-6 lg:grid-cols-2 mb-8">
        <!-- Left Column - Image Gallery -->
        <div class="space-y-4">
          <div class="relative aspect-square overflow-hidden rounded-lg border border-slate-200 bg-white group">
            <img
              :src="displayImages[selectedImageIndex] || product.imageUrl"
              :alt="product.title"
              class="h-full w-full object-contain p-4"
            />
            <!-- Navigation Arrows -->
            <button
              v-if="displayImages.length > 1"
              @click="handlePreviousImage"
              class="absolute left-2 top-1/2 -translate-y-1/2 bg-white/90 hover:bg-white border border-slate-300 rounded-full p-2 opacity-0 group-hover:opacity-100 transition-opacity shadow-md"
              aria-label="Previous image"
            >
              <ChevronLeft class="h-5 w-5 text-slate-700" />
            </button>
            <button
              v-if="displayImages.length > 1"
              @click="handleNextImage"
              class="absolute right-2 top-1/2 -translate-y-1/2 bg-white/90 hover:bg-white border border-slate-300 rounded-full p-2 opacity-0 group-hover:opacity-100 transition-opacity shadow-md"
              :aria-label="$t('productDetail.nextImage')"
            >
              <ChevronRight class="h-5 w-5 text-slate-700" />
            </button>
            <!-- Action Icons -->
            <div class="absolute top-4 right-4 flex gap-2">
              <button
                @click="handleToggleWishlist"
                :class="[
                  'bg-white/90 hover:bg-white border border-slate-300 rounded-full p-2 shadow-md transition',
                  isInWishlist && 'border-red-300'
                ]"
                :aria-label="$t('productDetail.addToWishlist')"
              >
                <Heart :class="['h-5 w-5', isInWishlist ? 'fill-red-500 text-red-500' : 'text-slate-700']" />
              </button>
              <button class="bg-white/90 hover:bg-white border border-slate-300 rounded-full p-2 shadow-md transition">
                <Share2 class="h-5 w-5 text-slate-700" />
              </button>
            </div>
          </div>
          <!-- Thumbnail Gallery -->
          <div v-if="displayImages.length > 1" class="flex gap-2 overflow-x-auto pb-2">
            <button
              v-for="(image, index) in displayImages"
              :key="index"
              @click="selectedImageIndex = index"
              :class="[
                'flex-shrink-0 overflow-hidden rounded border-2 transition',
                selectedImageIndex === index
                  ? 'border-brand shadow-md'
                  : 'border-slate-200 hover:border-slate-300'
              ]"
            >
              <img
                :src="image"
                :alt="`${product.title} ${index + 1}`"
                class="h-20 w-20 object-cover"
              />
            </button>
          </div>
        </div>

        <!-- Right Column - Product Info & Purchase -->
        <div class="space-y-6">
          <!-- Seller Information -->
          <div class="border-b border-slate-200 pb-4">
            <div class="flex items-center gap-3">
              <img v-if="sellerInfo.logo" :src="sellerInfo.logo" :alt="sellerInfo.name" class="h-10 w-10 rounded" />
              <div class="flex-1">
                <div class="flex items-center gap-2">
                  <router-link :to="`/verkaufer/${sellerInfo.id || 'seller-1'}`" class="font-semibold text-slate-900 hover:text-brand">
                    {{ sellerInfo.name }}
                  </router-link>
                  <span class="text-sm text-slate-600">
                    {{ sellerInfo.rating }}% {{ $t('productDetail.positiveReviews') }}
                  </span>
                </div>
                <div class="flex items-center gap-4 text-xs text-slate-600 mt-1">
                  <router-link :to="`/verkaufer/${sellerInfo.id || 'seller-1'}`" class="hover:text-brand">
                    Weitere Artikel von diesem Verkäufer
                  </router-link>
                  <router-link to="#" class="hover:text-brand">
                    Kontakt
                  </router-link>
                </div>
              </div>
            </div>
          </div>

          <!-- Product Title -->
          <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-900 lg:text-3xl leading-tight">
              {{ product.title }}
            </h1>
            <p class="mt-2 text-xs sm:text-sm text-slate-500">Art.-Nr.: {{ product.articleNumber }}</p>
          </div>

          <!-- Rating -->
          <div v-if="product.rating" class="flex items-center gap-2">
            <div class="flex items-center">
              <Star
                v-for="i in 5"
                :key="i"
                :class="[
                  'h-5 w-5',
                  i < Math.floor(product.rating || 0)
                    ? 'fill-yellow-400 text-yellow-400'
                    : 'text-slate-300'
                ]"
              />
            </div>
            <span class="text-sm font-medium text-slate-700">
              {{ product.rating }}
            </span>
            <span v-if="product.reviewCount" class="text-sm text-slate-500">
              ({{ product.reviewCount }} Bewertungen)
            </span>
          </div>

          <!-- Price -->
          <div class="space-y-2">
            <div class="flex items-baseline gap-2 sm:gap-3 flex-wrap">
              <span class="text-2xl sm:text-3xl font-bold text-slate-900">
                {{ currentPrice.toFixed(2) }} €
              </span>
              <template v-if="product.oldPrice">
                <span class="text-lg text-slate-500 line-through">
                  {{ product.oldPrice.toFixed(2) }} €
                </span>
                <span class="rounded bg-red-100 px-2 py-1 text-sm font-semibold text-red-700">
                  -{{ discount }}%
                </span>
              </template>
            </div>
            <p class="text-sm text-slate-600">
              Preis inkl. MwSt.
            </p>
            <p v-if="product.oldPrice" class="text-sm text-slate-500">
              oder
              <button
                type="button"
                @click="handleBestOffer"
                class="text-brand hover:underline cursor-pointer bg-transparent border-0 p-0 font-inherit text-sm"
              >
                Bestes Angebot
              </button>
            </p>
          </div>

          <!-- Product Variants -->
          <div v-if="product.variants && Object.keys(product.variants).length > 0" class="space-y-4">
            <div v-for="(variant, variantName) in product.variants" :key="variantName" class="space-y-2">
              <div class="flex items-center justify-between">
                <label class="text-sm font-medium text-slate-900">
                  {{ variantName }}:
                </label>
                <span v-if="selectedVariants[variantName]" class="text-xs text-slate-600">
                  {{ getSelectedVariantLabel(variantName) }}
                </span>
              </div>

              <!-- Color Variants -->
              <div v-if="variant.type === 'color'" class="flex flex-wrap gap-2">
                <button
                  v-for="option in variant.options"
                  :key="option.value"
                  @click="selectVariant(variantName, option)"
                  :disabled="!option.inStock"
                  :class="[
                    'relative h-10 w-10 rounded-full border-2 transition-all',
                    selectedVariants[variantName]?.value === option.value
                      ? 'border-brand ring-2 ring-brand ring-offset-2 scale-110'
                      : 'border-slate-300 hover:border-slate-400',
                    !option.inStock && 'opacity-50 cursor-not-allowed'
                  ]"
                  :title="option.name + (option.inStock ? '' : ' - Nicht verfügbar')"
                >
                  <img
                    v-if="option.image"
                    :src="option.image"
                    :alt="option.name"
                    class="h-full w-full rounded-full object-cover"
                  />
                  <div
                    v-else
                    :class="[
                      'h-full w-full rounded-full',
                      option.value === 'rot' && 'bg-red-500',
                      option.value === 'schwarz' && 'bg-slate-900',
                      option.value === 'blau' && 'bg-blue-500',
                      option.value === 'weiss' && 'bg-white border border-slate-300'
                    ]"
                  />
                  <span
                    v-if="!option.inStock"
                    class="absolute inset-0 flex items-center justify-center text-xs text-red-600 font-bold"
                  >
                    ×
                  </span>
                </button>
              </div>

              <!-- Size/Other Variants -->
              <div v-else class="flex flex-wrap gap-2">
                <button
                  v-for="option in variant.options"
                  :key="option.value"
                  @click="selectVariant(variantName, option)"
                  :disabled="!option.inStock"
                  :class="[
                    'px-4 py-2 rounded-md border-2 text-sm font-medium transition-all',
                    selectedVariants[variantName]?.value === option.value
                      ? 'border-brand bg-brand/10 text-brand'
                      : 'border-slate-300 hover:border-slate-400 text-slate-700',
                    !option.inStock && 'opacity-50 cursor-not-allowed line-through'
                  ]"
                >
                  {{ option.name }}
                  <span v-if="option.price && option.price !== currentPrice" class="ml-1 text-xs">
                    ({{ option.price > currentPrice ? '+' : '' }}{{ (option.price - currentPrice).toFixed(2) }} €)
                  </span>
                </button>
              </div>
            </div>
          </div>

          <!-- Item Condition -->
          <div class="flex items-center gap-2">
            <span class="font-medium text-slate-900">Zustand:</span>
            <span class="text-slate-700">Neu</span>
            <Info class="h-4 w-4 text-slate-400" />
          </div>

          <!-- Availability & Urgency -->
          <div v-if="product.available" class="rounded-lg border border-slate-200 bg-amber-50 p-3">
            <p class="text-sm font-medium text-amber-900">
              Eile, bevor es weg ist. 1 Person beobachtet diesen Artikel.
            </p>
          </div>

          <!-- Amazon Price Feature -->
          <div v-if="!product.available && amazonLink" class="rounded-lg border-2 border-orange-200 bg-gradient-to-r from-orange-50 to-amber-50 p-4">
            <div class="flex items-start gap-3">
              <div class="flex-shrink-0">
                <div class="flex h-10 w-10 items-center justify-center rounded bg-white shadow-sm">
                  <span class="text-xl font-bold text-orange-600">a</span>
                </div>
              </div>
              <div class="flex-1">
                <p class="text-sm font-semibold text-slate-900 mb-1">
                  Auch bei Amazon verfügbar
                </p>
                <p class="text-xs text-slate-600 mb-3">
                  Dieses Produkt ist aktuell nicht auf Lager. Finden Sie es auf Amazon.
                </p>
                <a
                  :href="amazonLink"
                  target="_blank"
                  rel="noopener noreferrer"
                  class="inline-flex items-center gap-2 rounded-md bg-orange-600 px-4 py-2 text-sm font-semibold text-white hover:bg-orange-700 transition-colors"
                >
                  <span>Auf Amazon ansehen</span>
                  <ExternalLink class="h-4 w-4" />
                </a>
              </div>
            </div>
          </div>

          <!-- Quantity Selector -->
          <div class="flex items-center gap-4">
            <label class="text-sm font-medium text-slate-700">Menge:</label>
            <div class="flex items-center border border-slate-300 rounded-md">
              <button
                @click="quantity = Math.max(1, quantity - 1)"
                class="px-3 py-2 hover:bg-slate-100 transition"
                type="button"
              >
                -
              </button>
              <input
                type="number"
                v-model.number="quantity"
                @input="quantity = Math.max(1, parseInt($event.target.value) || 1)"
                class="w-16 border-0 text-center focus:ring-0"
                min="1"
              />
              <button
                @click="quantity = quantity + 1"
                class="px-3 py-2 hover:bg-slate-100 transition"
                type="button"
              >
                +
              </button>
            </div>
          </div>

          <!-- Action Buttons -->
          <div class="space-y-3">
            <button
              @click="handleBuyNow"
              :disabled="!product.available || adding"
              class="w-full rounded-full bg-brand px-6 py-3 text-base font-semibold text-white hover:bg-brand-dark disabled:opacity-50 disabled:cursor-not-allowed"
            >
              {{ adding ? 'Wird hinzugefügt...' : 'Jetzt kaufen' }}
            </button>
            <div class="grid grid-cols-2 gap-3">
              <button
                @click="handleAddToCart"
                :disabled="!product.available || adding"
                class="rounded-full border-2 border-slate-300 hover:border-brand hover:text-brand px-4 py-2 disabled:opacity-50 disabled:cursor-not-allowed"
              >
                In den Warenkorb
              </button>
              <button
                @click="handleBestOffer"
                class="rounded-full border-2 border-slate-300 hover:border-brand hover:text-brand px-4 py-2"
              >
                Bestes Angebot senden
              </button>
            </div>
            <button
              @click="handleToggleWishlist"
              :class="[
                'w-full rounded-full border-2 px-4 py-2 flex items-center justify-center gap-2 transition',
                isInWishlist
                  ? 'border-red-300 bg-red-50 text-red-700 hover:bg-red-100'
                  : 'border-slate-300 hover:border-brand hover:text-brand'
              ]"
            >
              <Heart :class="['h-4 w-4', isInWishlist && 'fill-red-500 text-red-500']" />
              {{ isInWishlist ? 'Von Wunschliste entfernen' : 'Zur Wunschliste hinzufügen' }}
            </button>
          </div>

          <!-- Shipping & Returns Summary -->
          <div class="rounded-lg border border-slate-200 bg-white p-4 space-y-3">
            <div class="flex items-center gap-2 text-brand">
              <Check class="h-5 w-5" />
              <p class="font-semibold">Völlig entspannt. Kostenloser Versand & Rückgabe.</p>
            </div>
            <div class="space-y-2 text-sm text-slate-700">
              <div>
                <span class="font-medium">Versand:</span> Kostenlos. <router-link to="#" class="text-brand hover:underline">Weitere Details</router-link>
              </div>
              <div>
                <span class="font-medium">Standort:</span> Herzogenaurach, Deutschland
              </div>
              <div>
                <span class="font-medium">Lieferung:</span> Zwischen Donnerstag, 11. Dezember und Montag, 15. Dezember, wenn Zahlung heute erhalten wird.
              </div>
              <div>
                <span class="font-medium">Rückgaberecht:</span> 14-tägiges Rückgaberecht. Kostenlose Rücksendung. <router-link to="#" class="text-brand hover:underline">Informationen zum Widerrufsrecht</router-link>
              </div>
            </div>
          </div>

          <!-- Payment Methods -->
          <div class="rounded-lg border border-slate-200 bg-white p-4">
            <p class="text-sm font-medium text-slate-900 mb-3">Zahlungsarten:</p>
            <div class="flex flex-wrap gap-2">
              <div
                v-for="method in ['PayPal', 'Visa', 'Mastercard', 'American Express', 'Klarna', 'G Pay']"
                :key="method"
                class="px-3 py-1.5 bg-slate-50 rounded text-xs font-medium text-slate-700 border border-slate-200"
              >
                {{ method }}
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Similar Products / Customers Also Viewed Section -->
      <section v-if="similarProducts.length > 0" class="mb-8 bg-white rounded-lg border border-slate-200 p-6">
        <h2 class="text-xl font-bold text-slate-900 mb-4">{{ $t('productDetail.customersAlsoViewed') }}</h2>
        <div class="grid gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
          <ProductCard
            v-for="similarProduct in similarProducts"
            :key="similarProduct.id"
            :product="similarProduct"
          />
        </div>
      </section>

      <!-- Similar Articles Section -->
      <section v-if="relatedProducts.length > 0" class="mb-8 bg-white rounded-lg border border-slate-200 p-6">
        <h2 class="text-xl font-bold text-slate-900 mb-4">Ähnliche Artikel</h2>
        <div class="flex gap-4 overflow-x-auto pb-4 scrollbar-hide">
          <router-link
            v-for="relatedProduct in relatedProducts.slice(0, 6)"
            :key="relatedProduct.id"
            :to="`/produkt/${relatedProduct.id}`"
            class="flex-shrink-0 w-48 group"
          >
            <div class="bg-white border border-slate-200 rounded-lg overflow-hidden hover:shadow-md transition">
              <div class="aspect-square bg-slate-100 overflow-hidden">
                <img
                  :src="relatedProduct.imageUrl"
                  :alt="relatedProduct.title"
                  class="w-full h-full object-cover group-hover:scale-105 transition-transform"
                />
              </div>
              <div class="p-3">
                <h3 class="text-sm font-medium text-slate-900 line-clamp-2 mb-2 group-hover:text-brand transition">
                  {{ relatedProduct.title }}
                </h3>
                <p class="text-base font-bold text-slate-900">
                  {{ relatedProduct.price.toFixed(2) }} €
                </p>
                <p v-if="relatedProduct.oldPrice" class="text-xs text-slate-500 line-through">
                  {{ relatedProduct.oldPrice.toFixed(2) }} €
                </p>
              </div>
            </div>
          </router-link>
        </div>
      </section>

      <!-- Article Features Section -->
      <section class="mb-8 bg-white rounded-lg border border-slate-200 p-6">
        <h2 class="text-xl font-bold text-slate-900 mb-4">{{ $t('productDetail.articleFeatures') }}</h2>
        <div class="grid md:grid-cols-2 gap-4">
          <div class="space-y-2">
            <div class="flex justify-between py-2 border-b border-slate-100">
              <span class="text-sm font-medium text-slate-600">{{ $t('productDetail.condition') }}:</span>
              <span class="text-sm text-slate-900">{{ $t('productDetail.new') }}</span>
            </div>
            <div class="flex justify-between py-2 border-b border-slate-100">
              <span class="text-sm font-medium text-slate-600">{{ $t('productDetail.productType') }}:</span>
              <span class="text-sm text-slate-900">{{ product.category }}</span>
            </div>
            <div v-if="product.brand" class="flex justify-between py-2 border-b border-slate-100">
              <span class="text-sm font-medium text-slate-600">Marke:</span>
              <span class="text-sm text-slate-900">{{ product.brand }}</span>
            </div>
            <div
              v-for="[key, value] in Object.entries(product.specifications || {}).slice(0, Math.ceil(Object.keys(product.specifications || {}).length / 2))"
              :key="key"
              class="flex justify-between py-2 border-b border-slate-100"
            >
              <span class="text-sm font-medium text-slate-600">{{ key }}:</span>
              <span class="text-sm text-slate-900">{{ value }}</span>
            </div>
          </div>
          <div class="space-y-2">
            <div
              v-for="[key, value] in Object.entries(product.specifications || {}).slice(Math.ceil(Object.keys(product.specifications || {}).length / 2))"
              :key="key"
              class="flex justify-between py-2 border-b border-slate-100"
            >
              <span class="text-sm font-medium text-slate-600">{{ key }}:</span>
              <span class="text-sm text-slate-900">{{ value }}</span>
            </div>
            <div v-if="product.warranty" class="flex justify-between py-2 border-b border-slate-100">
              <span class="text-sm font-medium text-slate-600">{{ $t('productDetail.warranty') }}:</span>
              <span class="text-sm text-slate-900">{{ product.warranty }}</span>
            </div>
          </div>
        </div>
        <div v-if="product.longDescription" class="mt-6 pt-6 border-t border-slate-200">
          <p class="text-sm text-slate-700 leading-relaxed">{{ product.longDescription }}</p>
        </div>
      </section>

      <!-- Legal Information -->
      <section class="mb-8 bg-white rounded-lg border border-slate-200 p-6">
        <h2 class="text-xl font-bold text-slate-900 mb-4">{{ $t('productDetail.legalInfo') }}</h2>
        <div class="space-y-2 text-sm text-slate-700">
          <p>Umsatzsteuer-ID: DE123456789</p>
          <p>Handelsregisternummer: HRB 12345</p>
          <p>EPR-Registernummer: DE12345678901234</p>
          <div class="pt-4 space-y-1">
            <router-link to="#" class="text-brand hover:underline block">{{ $t('productDetail.termsForOffer') }}</router-link>
            <router-link to="#" class="text-brand hover:underline block">{{ $t('productDetail.accessibilityStatement') }}</router-link>
            <router-link to="#" class="text-brand hover:underline block">{{ $t('productDetail.sellerContactInfo') }}</router-link>
          </div>
          <p class="pt-4 text-xs text-slate-600">
            Die Europäische Kommission stellt eine Plattform zur Online-Streitbeilegung (OS) bereit, die Sie unter{" "}
            <router-link to="#" class="text-brand hover:underline">https://ec.europa.eu/consumers/odr/</router-link> finden.
          </p>
        </div>
      </section>

      <!-- Seller Information & Reviews -->
      <section class="mb-8 bg-white rounded-lg border border-slate-200 p-6">
        <div class="mb-6">
          <div class="flex items-start gap-4 mb-4">
            <img v-if="sellerInfo.logo" :src="sellerInfo.logo" :alt="sellerInfo.name" class="h-16 w-16 rounded" />
            <div class="flex-1">
              <div class="flex items-center gap-3 mb-2">
                <h3 class="text-xl font-bold text-slate-900">{{ sellerInfo.name }}</h3>
                <span class="text-sm text-slate-600">
                  {{ sellerInfo.rating }}% {{ $t('productDetail.positiveReviews') }}
                </span>
                <span class="text-sm text-slate-600">
                  {{ sellerInfo.itemsSold }} {{ $t('productDetail.itemsSold') }}
                </span>
              </div>
              <p class="text-sm text-slate-600 mb-2">
                {{ $t('productDetail.sellerSince') }} {{ sellerInfo.joinedDate }} • {{ $t('productDetail.respondsWithin') }} {{ sellerInfo.responseTime }} • {{ $t('productDetail.registeredBusiness') }}
              </p>
              <p class="text-sm text-slate-700 mb-4">
                {{ $t('productDetail.welcomeTo') }} {{ sellerInfo.name }}...{" "}
                <router-link to="#" class="text-brand hover:underline">{{ $t('productDetail.showMore') }}</router-link>
              </p>
              <div class="flex gap-3">
                <router-link
                  :to="`/verkaufer/${sellerInfo.id || 'seller-1'}`"
                  class="rounded-full border border-slate-300 px-4 py-2 text-sm hover:border-brand hover:text-brand inline-block"
                >
                  {{ $t('productDetail.visitShop') }}
                </router-link>
                <button class="rounded-full border border-slate-300 px-4 py-2 text-sm hover:border-brand hover:text-brand flex items-center gap-2">
                  <MessageCircle class="h-4 w-4" />
                  {{ $t('productDetail.contact') }}
                </button>
                <button class="rounded-full border border-slate-300 px-4 py-2 text-sm hover:border-brand hover:text-brand flex items-center gap-2">
                  <Heart class="h-4 w-4" />
                  {{ $t('productDetail.saveSeller') }}
                </button>
              </div>
            </div>
          </div>

          <!-- Detailed Seller Ratings -->
          <div class="border-t border-slate-200 pt-6 mb-6">
            <h4 class="font-semibold text-slate-900 mb-4">{{ $t('productDetail.detailedSellerRatings') }}</h4>
            <div class="grid md:grid-cols-2 gap-4">
              <div
                v-for="(value, key) in ratingBreakdown"
                :key="key"
                class="flex items-center justify-between"
              >
                <span class="text-sm text-slate-700 capitalize">
                  {{ key.replace(/([A-Z])/g, ' $1').trim() }}:
                </span>
                <div class="flex items-center gap-2">
                  <div class="flex items-center">
                    <Star
                      v-for="i in 5"
                      :key="i"
                      :class="[
                        'h-4 w-4',
                        i < Math.floor(value)
                          ? 'fill-yellow-400 text-yellow-400'
                          : 'text-slate-300'
                      ]"
                    />
                  </div>
                  <span class="text-sm font-medium text-slate-900 w-8">{{ value.toFixed(1) }}</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Customer Reviews -->
          <div class="border-t border-slate-200 pt-6">
            <div class="flex items-center justify-between mb-4">
              <h4 class="text-lg font-bold text-slate-900">
                {{ $t('productDetail.customerReviews') }} ({{ product.reviewCount || reviews.length }})
              </h4>
              <router-link to="#" class="text-sm text-brand hover:underline flex items-center gap-1">
                {{ $t('productDetail.viewAllReviews') }}
                <ArrowRight class="h-4 w-4" />
              </router-link>
            </div>
            <div class="space-y-4">
              <div
                v-for="review in reviews"
                :key="review.id"
                class="border-b border-slate-100 pb-4 last:border-0 last:pb-0"
              >
                <div class="flex items-center gap-2 mb-2">
                  <div class="flex items-center">
                    <Star
                      v-for="i in 5"
                      :key="i"
                      :class="[
                        'h-4 w-4',
                        i < review.rating
                          ? 'fill-yellow-400 text-yellow-400'
                          : 'text-slate-300'
                      ]"
                    />
                  </div>
                  <span class="text-sm font-semibold text-slate-900">{{ review.author }}</span>
                  <span class="text-xs text-slate-500">{{ review.date }}</span>
                  <span v-if="review.confirmed" class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded">
                    {{ $t('productDetail.verifiedPurchase') }}
                  </span>
                </div>
                <p class="text-sm text-slate-700">{{ review.text }}</p>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Popular Categories in Shop -->
      <section class="mb-8 bg-white rounded-lg border border-slate-200 p-6">
        <h2 class="text-xl font-bold text-slate-900 mb-4">{{ $t('productDetail.popularCategories') }}</h2>
        <div class="flex flex-wrap gap-2">
          <router-link
            v-for="category in ['Haus & Wohnen', 'Haushaltsgeräte & Unterhaltung', 'Spielzeug', 'DIY & Werkzeuge', 'Baby', 'Sport & Freizeit', 'Tierbedarf', 'Auto & Motorrad', 'Verschiedenes']"
            :key="category"
            :to="`/kategorie/${categorySlugs[category] || category.toLowerCase()}`"
            class="px-4 py-2 bg-slate-50 hover:bg-slate-100 rounded-full text-sm text-slate-700 hover:text-brand transition"
          >
            {{ category }}
          </router-link>
        </div>
      </section>

      <!-- Also Interesting Section -->
      <section v-if="relatedProducts.length > 6" class="mb-8 bg-white rounded-lg border border-slate-200 p-6">
        <h2 class="text-xl font-bold text-slate-900 mb-4">Auch interessant</h2>
        <div class="grid gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
          <ProductCard
            v-for="relatedProduct in relatedProducts.slice(6)"
            :key="relatedProduct.id"
            :product="relatedProduct"
          />
        </div>
      </section>

      <!-- More Products from Category -->
      <section v-if="relatedProducts.length > 0" class="mb-8 bg-white rounded-lg border border-slate-200 p-6">
        <div class="mb-4 flex items-center justify-between">
          <h2 class="text-xl font-bold text-slate-900">{{ $t('productDetail.moreFromCategory') }}</h2>
          <router-link :to="`/kategorie/${categorySlug || ''}`" class="text-sm text-brand hover:underline">
            {{ $t('productDetail.showAll') }}
          </router-link>
        </div>
        <div class="grid gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
          <ProductCard
            v-for="relatedProduct in relatedProducts"
            :key="relatedProduct.id"
            :product="relatedProduct"
          />
        </div>
      </section>
    </div>

    <!-- Best Offer Modal -->
    <BestOfferModal
      :is-open="showBestOfferModal"
      :product="product"
      @close="handleBestOfferClose"
      @submit="handleBestOfferSubmit"
    />
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import axiosTenant from '@theme-prism/axios'
import { getProductDetail } from '@theme-prism/data/productDetails'
import { categorySlugs } from '@theme-prism/data/products'
import { useCartStore } from '@theme-prism/stores/cartStore'
import { useWishlistStore } from '@theme-prism/stores/wishlistStore'
import ProductCard from '@theme-prism/component/ProductCard.vue'
// import BestOfferModal from '@theme-prism/components/BestOfferModal.vue'
import { Heart, Star, Check, ChevronLeft, ChevronRight, Share2, MessageCircle, Info, ArrowRight, ExternalLink } from 'lucide-vue-next'

const route = useRoute()
const router = useRouter()
const cartStore = useCartStore()
const wishlistStore = useWishlistStore()

const selectedImageIndex = ref(0)
const quantity = ref(1)
const adding = ref(false)
const selectedVariants = ref({})
const showBestOfferModal = ref(false)
const productData = ref(null)
const loading = ref(true)
const relatedProductsList = ref([])

const PLACEHOLDER_IMAGE = 'https://images.unsplash.com/photo-1558980664-2506fca6bfc2?auto=format&fit=crop&w=600&q=80'

function slugFromName(name) {
  if (!name) return ''
  return String(name).toLowerCase().replace(/\s*&\s*/g, '-').replace(/[^a-z0-9\u00C0-\u024F-]+/g, '-').replace(/-+/g, '-').replace(/^-|-$/g, '')
}

function mapApiProductToPage(apiProduct) {
  const mainMedia = apiProduct.media?.find(m => m.is_main) || apiProduct.media?.[0]
  const imageUrl = mainMedia?.cdn_url || PLACEHOLDER_IMAGE
  const firstCategory = apiProduct.categories?.[0]
  const categoryName = firstCategory?.name || 'Produkt'
  return {
    id: apiProduct.id,
    title: apiProduct.name,
    articleNumber: apiProduct.sku || `ART-${apiProduct.id}`,
    price: parseFloat(apiProduct.price) || 0,
    oldPrice: apiProduct.compare_at_price ? parseFloat(apiProduct.compare_at_price) : null,
    imageUrl,
    images: [imageUrl],
    category: categoryName,
    categoryId: firstCategory?.id ?? null,
    categorySlug: slugFromName(categoryName),
    available: (apiProduct.quantity ?? 0) > 0,
    description: apiProduct.detailed_description || apiProduct.name,
    longDescription: apiProduct.detailed_description || null,
    specifications: {},
    variants: {},
    rating: 4.5,
    reviewCount: 0,
    brand: null,
    warranty: null
  }
}

function mapApiProductToCard(apiProduct) {
  const mainMedia = apiProduct.media?.find(m => m.is_main) || apiProduct.media?.[0]
  const imageUrl = mainMedia?.cdn_url || PLACEHOLDER_IMAGE
  const qty = apiProduct.stock ?? apiProduct.quantity ?? apiProduct.variants?.reduce((sum, v) => sum + (v.quantity || v.stock || 0), 0) ?? 0
  return {
    id: apiProduct.id,
    title: apiProduct.name,
    imageUrl,
    articleNumber: apiProduct.sku || `ART-${apiProduct.id}`,
    price: parseFloat(apiProduct.price) || 0,
    oldPrice: apiProduct.compare_at_price ? parseFloat(apiProduct.compare_at_price) : null,
    available: qty > 0
  }
}

async function loadProduct() {
  const id = route.params.id
  if (!id) {
    productData.value = null
    loading.value = false
    return
  }
  loading.value = true
  productData.value = null
  relatedProductsList.value = []
  try {
    const res = await axiosTenant.get(`/products/${id}`)
    const apiProduct = res.data?.data ?? res.data?.product ?? res.data
    if (apiProduct && (apiProduct.id || apiProduct.name)) {
      productData.value = mapApiProductToPage(apiProduct)
      const categoryId = productData.value.categoryId
      const currentId = productData.value.id
      if (categoryId) {
        try {
          const listRes = await axiosTenant.get('/products', { params: { category_id: categoryId } })
          const items = listRes.data?.products ?? listRes.data?.data ?? []
          const list = items.filter(p => p.id !== currentId).map(mapApiProductToCard)
          relatedProductsList.value = list
        } catch (_) {
          relatedProductsList.value = []
        }
      }
    } else {
      productData.value = getProductDetail(id)
    }
  } catch (err) {
    if (process.env.NODE_ENV !== 'production') {
      console.warn('Product load failed:', err.response?.status, err.response?.data ?? err.message)
    }
    if (err.response?.status === 404) {
      productData.value = getProductDetail(id)
    } else {
      productData.value = getProductDetail(id) || null
    }
  } finally {
    loading.value = false
  }
}

watch(() => route.params.id, loadProduct, { immediate: true })

const product = computed(() => {
  const data = productData.value
  if (data && data.variants && Object.keys(data.variants).length > 0) {
    Object.keys(data.variants).forEach(variantName => {
      if (!selectedVariants.value[variantName]) {
        const firstAvailable = data.variants[variantName].options?.find(opt => opt.inStock)
        if (firstAvailable) {
          selectedVariants.value[variantName] = firstAvailable
        }
      }
    })
  }
  return data
})

watch(product, (p) => {
  if (p?.title) document.title = `${p.title} | Einfach Shop`
}, { immediate: true })

// Current price based on selected variants
const currentPrice = computed(() => {
  if (!product.value) return 0

  let basePrice = product.value.price

  // Add variant price differences
  Object.values(selectedVariants.value).forEach(variant => {
    if (variant.price && variant.price !== product.value.price) {
      basePrice = variant.price
    }
  })

  return basePrice
})

// Display images based on selected color variant
const displayImages = computed(() => {
  if (!product.value) return []

  // If color variant is selected and has images, use those
  const colorVariant = Object.values(selectedVariants.value).find(v => v.image)
  if (colorVariant && colorVariant.image) {
    return [colorVariant.image, ...product.value.images.slice(1)]
  }

  return product.value.images || [product.value.imageUrl]
})

// Get selected variant label
const getSelectedVariantLabel = (variantName) => {
  const selected = selectedVariants.value[variantName]
  return selected ? selected.name : ''
}

// Select variant
const selectVariant = (variantName, option) => {
  if (!option.inStock) return

  selectedVariants.value[variantName] = option

  // If it's a color variant, update the main image
  if (option.image) {
    selectedImageIndex.value = 0
  }
}

const relatedProducts = computed(() => relatedProductsList.value)
const similarProducts = computed(() => relatedProductsList.value)

const discount = computed(() => {
  if (!product.value?.oldPrice) return 0
  return Math.round(((product.value.oldPrice - product.value.price) / product.value.oldPrice) * 100)
})

const categorySlug = computed(() => {
  if (!product.value) return ''
  return product.value.categorySlug ?? categorySlugs[product.value.category] ?? ''
})

const amazonLink = computed(() => {
  if (!product.value) return ''
  return product.value.amazonLink || `https://www.amazon.de/s?k=${encodeURIComponent(product.value.title || '')}&tag=einfachshop24-21`
})

// Seller information (in production, this would come from product data or API)
const sellerInfo = {
  id: 'seller-1',
  name: 'SimplyShop',
  rating: 98.5,
  itemsSold: '12.3K',
  joinedDate: 'Mai 2013',
  responseTime: '3 Stunden',
  isCommercial: true,
  logo: 'https://images.unsplash.com/photo-1563013544-824ae1b704d3?auto=format&fit=crop&w=100&q=80'
}

const reviews = [
  {
    id: '1',
    rating: 5,
    author: 'Kunde 1',
    date: 'vor 2 Monaten',
    text: 'D% zutreffend. Sehr zufrieden!',
    confirmed: true
  },
  {
    id: '2',
    rating: 5,
    author: 'Kunde 2',
    date: 'vor 1 Monat',
    text: 'Ausgezeichnete Qualität, schnelle Lieferung. Kann ich nur weiterempfehlen!',
    confirmed: true
  },
  {
    id: '3',
    rating: 4,
    author: 'Kunde 3',
    date: 'vor 3 Wochen',
    text: 'Gutes Produkt, funktioniert wie erwartet. Lieferung war etwas länger als angegeben, aber ok.',
    confirmed: true
  }
]

const ratingBreakdown = {
  detailedDescription: 4.8,
  shippingCosts: 4.7,
  deliveryTime: 4.6,
  communication: 4.9
}

const handlePreviousImage = () => {
  if (!product.value) return
  selectedImageIndex.value = selectedImageIndex.value === 0
    ? displayImages.value.length - 1
    : selectedImageIndex.value - 1
}

const handleNextImage = () => {
  if (!product.value) return
  selectedImageIndex.value = selectedImageIndex.value === displayImages.value.length - 1
    ? 0
    : selectedImageIndex.value + 1
}

const handleBestOffer = () => {
  if (!product.value) return
  showBestOfferModal.value = true
}

const handleBestOfferClose = () => {
  showBestOfferModal.value = false
}

const handleBestOfferSubmit = (offerData) => {
  console.log('Best offer submitted:', offerData)
  // Here you could also show a toast notification or update UI
}

const handleAddToCart = () => {
  if (!product.value || !product.value.available) return

  adding.value = true
  cartStore.addItem(product.value, quantity.value)
  setTimeout(() => {
    adding.value = false
  }, 500)
}

const handleBuyNow = () => {
  if (!product.value || !product.value.available) return

  adding.value = true
  cartStore.addItem(product.value, quantity.value)
  setTimeout(() => {
    adding.value = false
    router.push('/warenkorb')
  }, 500)
}

const isInWishlist = computed(() => {
  if (!product.value) return false
  return wishlistStore.isInWishlist(product.value.id)
})

const handleToggleWishlist = () => {
  if (!product.value) return
  wishlistStore.toggleItem(product.value)
}

onMounted(() => {
  if (loading.value) return
  if (!product.value && route.params.id) {
    console.error('Product not found for ID:', route.params.id)
  }
})
</script>

