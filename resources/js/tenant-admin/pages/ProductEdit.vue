<template>
  <div class="tenant-product-editor tenant-product-editor--shopify pb-12 text-[13px] leading-snug">
    <header
      class="tenant-product-editor__header sticky top-16 z-[45] border-b border-[#e3e3e3] bg-white shadow-sm"
      role="banner"
    >
      <div class="mx-auto flex max-w-[920px] flex-col gap-2 px-3 py-2.5 sm:flex-row sm:items-center sm:justify-between sm:px-4">
        <div class="flex min-w-0 items-center gap-2 sm:gap-3">
          <button
            type="button"
            class="shrink-0 rounded-lg p-2 text-gray-500 transition hover:bg-gray-100"
            aria-label="Go back to products"
            @click="closeModal"
          >
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
          </button>
          <div class="min-w-0 flex-1">
            <p class="tenant-product-editor__eyebrow mb-0.5">Products</p>
            <h1 class="truncate text-[15px] font-semibold text-[#303030]">{{ form.name.trim() ? form.name : 'Edit product' }}</h1>
          </div>
          <span
            class="shrink-0 rounded-full px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wide"
            :class="statusBadgeClass"
          >
            {{ statusLabel }}
          </span>
        </div>
        <div class="flex w-full gap-2 sm:w-auto sm:shrink-0 sm:justify-end">
          <button type="button" class="tenant-btn-secondary tenant-btn-sm min-h-[40px] flex-1 sm:flex-none sm:min-h-0" @click="closeModal">
            Discard
          </button>
          <button type="button" class="tenant-btn-submit tenant-btn-sm min-h-[40px] flex-1 sm:flex-none sm:min-h-0" @click="handleSubmit">
            Save product
          </button>
        </div>
      </div>
    </header>

    <div class="mx-auto mt-3 max-w-[920px] px-3 sm:px-4">
      <div class="grid grid-cols-1 gap-3 lg:grid-cols-12 lg:gap-4 lg:items-start">
        <div class="space-y-3 lg:col-span-8">
        <section class="tenant-product-editor__section px-3 py-3 sm:px-4 sm:py-3 space-y-4">
          <div class="mb-3 flex flex-wrap items-center gap-1.5">
            <h2 class="tenant-product-editor__section-title">Product details</h2>
            <TenantFieldTip label="Help" text="Main info customers see: title, description, category, photos, price, and stock." />
          </div>
          <div>
            <div class="tenant-label-row">
              <label class="tenant-field-label mb-0 cursor-pointer" for="product-name">Title</label>
              <span class="tenant-required-mark" aria-hidden="true">*</span>
              <TenantFieldTip label="Title" text="The product name shown in your store, search, cart, and orders." />
            </div>
            <input
              id="product-name"
              v-model="form.name"
              type="text"
              placeholder="Short sleeve t-shirt"
              class="tenant-input-shopify w-full px-3 py-2 transition"
              autocomplete="off"
            />
          </div>
          <div>
            <div class="tenant-label-row">
              <span class="tenant-field-label">Description</span>
              <TenantFieldTip label="Description" text="Details for the product page: features, size, care, etc." />
            </div>
            <Editor
              api-key="gxuz0hko2aulkfi1oewoca3b7oz93uo8ib39y3gx0ahwf9va"
              v-model="form.detailed_description"
              :init="editorInit"
            />
          </div>
          <div>
            <div class="tenant-label-row">
              <span class="tenant-field-label">Category</span>
              <TenantFieldTip label="Category" text="Where this product appears in your store. Type to search." />
            </div>
            <div class="space-y-2">
              <TenantSelectSearch
                v-model="selectedL1"
                input-id="product-cat-l1"
                placeholder="Search main category…"
                :options="categoryOptionsL1"
              />
              <TenantSelectSearch
                v-if="categoriesL2.length"
                v-model="selectedL2"
                input-id="product-cat-l2"
                placeholder="Search sub-category…"
                :options="categoryOptionsL2"
              />
              <TenantSelectSearch
                v-if="categoriesL3.length"
                v-model="form.categories_id"
                input-id="product-cat-l3"
                placeholder="Search type…"
                :options="categoryOptionsL3"
              />
            </div>
          </div>
        </section>

        <section class="bg-white border border-gray-200 rounded-xl shadow-sm p-5">
  <div class="flex justify-between items-center mb-4 min-h-[24px]">
    <div v-if="selectedIndices.length === 0" class="flex items-center gap-2">
      <h3 class="text-sm font-semibold">Media</h3>
    </div>

    <div v-else class="flex items-center justify-between w-full">
      <div class="flex items-center gap-3">
        <div class="bg-gray-800 text-white px-2 py-0.5 rounded text-xs flex items-center gap-2">
          <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zM7 9a1 1 0 000 2h6a1 1 0 100-2H7z"/></svg>
          {{ selectedIndices.length }} file{{ selectedIndices.length > 1 ? 's' : '' }} selected
        </div>
      </div>
      <button type="button" @click="removeSelectedFiles" class="text-red-600 text-xs font-medium hover:underline">
        Remove
      </button>
    </div>

    <button v-if="selectedIndices.length === 0" type="button" @click="triggerFileInput" class="text-blue-600 text-xs font-medium hover:underline">
      Add from URL
    </button>
  </div>

  <div
    v-if="form.media_files.length === 0"
    @click="triggerFileInput"
    @drop.prevent="handleFileDrop"
    @dragover.prevent
    class="border-2 border-dashed border-gray-300 rounded-lg p-12 flex flex-col items-center justify-center bg-gray-50/50 hover:bg-gray-50 transition cursor-pointer"
  >
    <div class="flex gap-3 mb-3">
      <button class="bg-white border border-gray-300 px-3 py-1.5 rounded-lg text-sm font-medium shadow-sm">Upload new</button>
      <button class="text-gray-600 px-3 py-1.5 text-sm font-medium">Select existing</button>
    </div>
    <p class="text-xs text-gray-400">Accepts images, videos, or 3D models</p>
    <input ref="fileInput" type="file" multiple accept="image/*" class="hidden" @change="handleFileSelect" />
  </div>

  <draggable
    v-else
    v-model="form.media_files"
    item-key="_clientKey"
    class="flex flex-wrap gap-3"
    ghost-class="opacity-40"
    animation="250"
  >
    <template #item="{ element, index }">
      <div
        class="relative border border-gray-200 rounded-lg overflow-hidden bg-white group cursor-move shadow-sm"
        :class="index === 0 ? 'w-full sm:w-[212px] sm:h-[212px] h-64' : 'w-[100px] h-[100px]'"
      >
        <div class="absolute top-2 left-2 z-20">
          <input
            type="checkbox"
            :value="index"
            v-model="selectedIndices"
            @click.stop
            class="w-4 h-4 rounded border-gray-300 text-black focus:ring-black cursor-pointer"
          />
        </div>

        <img :src="element.preview" class="w-full h-full object-cover" />

        <div v-if="index === 0" class="absolute bottom-2 left-2 bg-white/90 px-2 py-0.5 rounded text-[10px] font-bold text-gray-700 border border-gray-200">
          Main
        </div>
      </div>
    </template>

    <template #footer>
      <div
        @click="triggerFileInput"
        class="w-[100px] h-[100px] border-2 border-dashed border-gray-300 rounded-lg flex items-center justify-center hover:bg-gray-50 transition cursor-pointer"
      >
        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
        <input ref="fileInput" type="file" multiple accept="image/*" class="hidden" @change="handleFileSelect" />
      </div>
    </template>
  </draggable>
</section>

       <section class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
  <div class="p-5">
    <h3 class="text-sm font-semibold mb-4">Price</h3>

    <div class="mb-4 max-w-[240px]">
      <div class="relative group">
        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 text-sm">Rs</span>
        <input
          id="product-price"
          v-model.number="form.price"
          type="number"
          class="tenant-input-shopify w-full py-2 pl-9 pr-3"
          placeholder="0.00"
        />
      </div>
    </div>

    <div class="border-t border-gray-100 pt-4">
      <button
        @click="showAdditional = !showAdditional"
        class="flex items-center justify-between w-full text-left group"
      >
        <span class="text-xs font-bold text-gray-700">Additional display prices</span>
        <svg
          class="w-4 h-4 text-gray-500 transition-transform"
          :class="{'rotate-180': showAdditional}"
          fill="none" stroke="currentColor" viewBox="0 0 24 24"
        >
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
      </button>

      <div v-show="showAdditional" class="mt-4 space-y-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block text-xs text-gray-600 mb-1">Compare-at price</label>
            <div class="relative">
              <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 text-sm">Rs</span>
              <input id="product-compare" v-model.number="form.compare_at_price" type="number" class="tenant-input-shopify w-full py-2 pl-9 pr-3" placeholder="0.00" />
              <button class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
              </button>
            </div>
          </div>
          <div class="relative">
  <label class="block text-xs text-gray-600 mb-1">Unit price</label>

  <div
    @click="showUnitPricePopover = !showUnitPricePopover"
    class="w-full border border-gray-300 rounded-lg px-3 py-1.5 text-sm bg-white cursor-pointer flex justify-between items-center outline-none focus:ring-1 focus:ring-black"
    :class="{'ring-1 ring-black': showUnitPricePopover}"
  >
    <span :class="form.unit_price_total ? 'text-black' : 'text-gray-400'">
      {{ form.unit_price_total ? `${form.unit_price_total} ${form.unit_price_type} / ${form.unit_price_base}${form.unit_price_base_type}` : '--' }}
    </span>
    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
    </svg>
  </div>

  <div v-if="showUnitPricePopover" class="absolute z-50 top-full mt-1 right-0 w-[280px] bg-white border border-gray-200 rounded-xl shadow-xl p-4">

    <div class="mb-4">
      <label class="block text-[11px] text-gray-500 mb-1">Total amount</label>
      <div class="flex items-center gap-1">
        <input
          v-model="tempUnitPrice.total"
          type="number"
          class="flex-1 border-2 border-blue-500 rounded-lg px-2 py-1 text-sm outline-none"
        />
        <select v-model="tempUnitPrice.type" class="border border-gray-300 rounded-lg px-1 py-1 text-sm bg-gray-50">
          <option value="g">g</option>
          <option value="kg">kg</option>
          <option value="ml">ml</option>
          <option value="l">l</option>
        </select>
      </div>
    </div>

    <div class="mb-6">
      <label class="block text-[11px] text-gray-500 mb-1">Base measure</label>
      <div class="flex items-center gap-1">
        <input
          v-model="tempUnitPrice.base"
          type="number"
          class="flex-1 border border-gray-300 rounded-lg px-2 py-1 text-sm outline-none"
        />
        <select v-model="tempUnitPrice.baseType" class="border border-gray-300 rounded-lg px-1 py-1 text-sm bg-gray-50">
          <option value="kg">kg</option>
          <option value="g">g</option>
        </select>
      </div>
    </div>

    <div class="flex items-center justify-between pt-3 border-t border-gray-100">
      <button @click="clearUnitPrice" class="text-red-700 text-sm hover:underline">Clear</button>
      <div class="flex gap-2">
        <button @click="showUnitPricePopover = false" class="px-3 py-1.5 border border-gray-300 rounded-lg text-sm font-medium">Cancel</button>
        <button @click="applyUnitPrice" class="px-4 py-1.5 bg-gray-200 text-gray-500 rounded-lg text-sm font-medium">Done</button>
      </div>
    </div>
  </div>
</div>
        </div>

        <div class="flex items-center gap-2 py-2">
          <input v-model="form.charge_tax" type="checkbox" id="tax" class="w-4 h-4 rounded border-gray-400 text-black focus:ring-black" />
          <label for="tax" class="text-sm text-gray-700 cursor-pointer">Charge tax on this product</label>
        </div>
      </div>
    </div>
  </div>

  <div class="bg-gray-50/50 p-4 border-t border-gray-100 flex flex-wrap gap-2">
    <div class="flex items-center bg-white border border-gray-200 rounded-lg px-3 py-1 text-sm shadow-sm group">
      <span class="text-gray-500 mr-2">Cost</span>
      <div class="flex items-center">
        <span class="text-gray-400 text-xs mr-1">Rs</span>
        <input id="product-cost" v-model.number="form.cost_per_item" type="number" class="w-16 outline-none bg-transparent" placeholder="--" />
      </div>
    </div>

    <div class="flex items-center bg-white border border-gray-200 rounded-lg px-3 py-1 text-sm shadow-sm">
      <span class="text-gray-500 mr-2">Profit</span>
      <span class="text-gray-800 font-medium">{{ form.price ? 'Rs ' + calculatedProfit.toFixed(2) : '--' }}</span>
    </div>

    <div class="flex items-center bg-white border border-gray-200 rounded-lg px-3 py-1 text-sm shadow-sm">
      <span class="text-gray-500 mr-2">Margin</span>
      <span class="text-gray-800 font-medium">{{ form.price ? calculatedMargin + '%' : '--' }}</span>
    </div>
  </div>
</section>

        <section class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
  <div class="p-5 pb-4 flex justify-between items-center">
    <h3 class="text-sm font-semibold text-gray-900">Inventory</h3>
    <div class="flex items-center gap-2">
      <span class="text-xs text-gray-500">Inventory tracked</span>
      <label class="relative inline-flex items-center cursor-pointer">
        <input type="checkbox" v-model="form.inventory_tracked" class="sr-only peer">
        <div class="w-8 h-4 bg-gray-300 rounded-full peer peer-checked:bg-black after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-3 after:w-3 after:transition-all peer-checked:after:translate-x-4"></div>
      </label>
    </div>
  </div>

  <div class="px-5 pb-5">
    <div class="border border-gray-200 rounded-xl overflow-hidden mb-4">
      <div class="bg-gray-50/80 border-b border-gray-100 px-4 py-2 flex justify-between items-center">
        <span class="text-[11px] font-bold text-blue-700 tracking-tight">Quantity</span>
        <span class="text-[11px] font-bold text-blue-700 tracking-tight">Quantity</span>
      </div>
      <div class="px-4 py-3 flex justify-between items-center bg-white">
        <span class="text-sm text-gray-700">Shop location</span>
        <input
          id="product-qty"
          v-model.number="form.quantity"
          type="number"
          class="tenant-input-shopify w-32 px-3 py-2 text-right"
        />
      </div>
    </div>

    <div class="pt-4 border-t border-gray-100">
      <div v-if="!showAdvancedInventory" class="flex items-center justify-between">
        <div class="flex gap-2">
          <span class="px-3 py-1 bg-gray-100 border border-gray-200 rounded-lg text-xs font-medium text-gray-600">SKU</span>
          <span class="px-3 py-1 bg-gray-100 border border-gray-200 rounded-lg text-xs font-medium text-gray-600">Barcode</span>
          <div class="px-3 py-1 bg-gray-100 border border-gray-200 rounded-lg text-xs font-medium text-gray-600 flex items-center gap-1">
            <span>Sell when out of stock</span>
            <span class="text-gray-400">Off</span>
          </div>
        </div>
        <button @click="showAdvancedInventory = true" class="text-gray-400 hover:text-gray-600">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
        </button>
      </div>

      <div v-else>
        <div class="flex justify-between items-center mb-4">
          <span class="text-sm font-bold text-gray-900">More details</span>
          <button @click="showAdvancedInventory = false" class="text-gray-400 hover:text-gray-600">
            <svg class="w-4 h-4 rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
          </button>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
          <div>
            <label class="block text-xs font-medium text-gray-600 mb-1.5">SKU (Stock Keeping Unit)</label>
            <input id="product-sku" v-model="form.sku" type="text" class="tenant-input-shopify w-full px-3 py-2" />
          </div>
          <div>
            <label class="block text-xs font-medium text-gray-600 mb-1.5">Barcode (ISBN, UPC, GTIN, etc.)</label>
            <input id="product-barcode" v-model="form.barcode" type="text" class="tenant-input-shopify w-full px-3 py-2" />
          </div>
        </div>

        <div class="flex items-center gap-2">
          <input type="checkbox" id="oversell" v-model="form.continue_selling" class="w-4 h-4 rounded border-gray-300 accent-black">
          <label for="oversell" class="text-sm text-gray-700">Continue selling when out of stock</label>
        </div>
      </div>
    </div>
  </div>

</section>
<section class="bg-white border  border-gray-200 rounded-xl shadow-sm overflow-hidden">
  <div class="p-5 pb-4 border-b border-gray-100  ">
    <h3 class="  text-sm font-semibold text-gray-900">Variants</h3>
    <!-- <button
      v-if="options.length < 1"
      @click="addOption"
      class="text-gray-600 text-xs font-semibold hover:underline"
    >
      + Add options like size or color
    </button> -->
  </div>

  <div v-if="options.length > 0" class="p-2 space-y-4 bg-gray-50/30">
    <div v-for="(option, index) in options" :key="index" class="p-4 bg-white border border-gray-200 rounded-xl space-y-3">
      <div class="flex justify-between items-center">
        <label class="text-xs font-bold uppercase text-gray-500">Option name</label>
        <button @click="removeOption(index)" class="text-gray-400 hover:text-red-500">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2"/></svg>
        </button>
      </div>

      <select
        v-model="option.name"
        class="w-full border border-gray-300 rounded-lg px-3 py-1.5 text-sm outline-none focus:ring-1 focus:ring-black"
      >
        <option value="" disabled>Select option</option>
        <option
          v-for="opt in availableOptionNames(option.name)"
          :key="opt" :value="opt"
        >{{ opt }}</option>
      </select>

      <label class="block text-xs font-bold uppercase text-gray-500">Option values</label>

      <div class="space-y-2">
        <div v-for="(val, vIdx) in option.values" :key="vIdx" class="flex gap-2">
          <div class="flex-1 bg-gray-100 border border-gray-200 rounded-lg px-3 py-1.5 text-sm flex justify-between items-center">
            {{ val }}
            <button @click="removeTag(index, vIdx)" class="text-gray-400 hover:text-black">&times;</button>
          </div>
        </div>
        <input
          v-model="option.currentInput"
          @input="processVariants"
          @keydown.enter.prevent="addTag(index)"
          type="text"
          placeholder="Add another value"
          class="w-full border border-gray-300 rounded-lg px-3 py-1.5 text-sm outline-none focus:ring-1 focus:ring-black"
        />
      </div>
    </div>
  </div>
  <div class="px-5 py-3">
  <button class="px-3 py-1 bg-gray-100 border border-gray-200 rounded-lg text-xs font-medium text-gray-600"
      v-if="options.length < 3"
      @click="addOption"

    >
      + Add options like size or color
    </button>
    </div>
  <div v-if="variants.length > 0">
  <div v-if="options.length > 1" class="px-5 py-3 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
    <span class="text-xs font-medium text-gray-600">Previewing combinations</span>
    <button class="text-xs font-bold text-blue-600 flex items-center gap-1 group">
      Group by {{ options[0].name }}
      <svg class="w-3 h-3 transition-transform group-hover:translate-y-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path d="M19 9l-7 7-7-7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
    </button>
  </div>

  <table class="w-full text-left border-collapse">
    <thead class="bg-gray-50/50 border-b border-gray-100">
      <tr>
        <th class="p-3 w-10">
          <input type="checkbox" class="w-4 h-4 rounded border-gray-300 accent-black">
        </th>
        <th class="p-3 text-[11px] font-bold text-gray-500 uppercase tracking-tight">Variant</th>
        <th class="p-3 text-[11px] font-bold text-gray-500 uppercase tracking-tight">Price</th>
        <th class="p-3 text-[11px] font-bold text-gray-500 uppercase tracking-tight">Available</th>
        <th class="p-3 text-[11px] font-bold text-gray-500 uppercase tracking-tight">SKU</th>
        <th class="p-3 w-10"></th>
      </tr>
    </thead>
    <tbody>
      <template v-for="(variant, vIdx) in variants" :key="vIdx">
        <tr
          class="border-b border-gray-100 hover:bg-gray-50 transition-colors cursor-pointer"
          @click="variant.expanded = !variant.expanded"
        >
          <td class="p-3" @click.stop>
            <input type="checkbox" class="w-4 h-4 rounded border-gray-300 accent-black">
          </td>
          <td class="p-3">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 bg-gray-50 rounded border border-gray-200 flex items-center justify-center overflow-hidden relative">
                <img v-if="variant.media.length" :src="variant.media[0].preview" class="w-full h-full object-cover" />
                <svg v-else class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <div v-if="variant.media.length > 1" class="absolute bottom-0 right-0 bg-black/70 text-[8px] text-white px-1 leading-tight rounded-tl">
                  +{{ variant.media.length - 1 }}
                </div>
              </div>
              <span class="text-sm font-medium text-gray-800">{{ variant.title }}</span>
            </div>
          </td>
          <td class="p-3">
            <div class="relative w-24">
              <span class="absolute left-2 top-1/2 -translate-y-1/2 text-gray-400 text-xs">Rs</span>
              <input
                v-model.number="variant.price"
                type="number"
                class="w-full pl-7 pr-2 py-1 border border-gray-300 rounded text-sm outline-none focus:ring-1 focus:ring-black"
                @click.stop
              />
            </div>
          </td>
          <td class="p-3">
            <input
              v-model.number="variant.qty"
              type="number"
              class="w-16 px-2 py-1 border border-gray-300 rounded text-sm text-right outline-none focus:ring-1 focus:ring-black"
              @click.stop
            />
          </td>
          <td class="p-3 text-sm text-gray-500 font-mono">
            {{ variant.sku || '--' }}
          </td>
          <td class="p-3">
            <svg
              class="w-4 h-4 text-gray-400 transition-transform duration-200"
              :class="{'rotate-180': variant.expanded}"
              fill="none" stroke="currentColor" viewBox="0 0 24 24"
            >
              <path d="M19 9l-7 7-7-7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </td>
        </tr>

        <tr v-if="variant.expanded" class="bg-gray-50/30">
  <td colspan="6" class="p-6 border-b border-gray-100">
    <div class="space-y-6">
      <div>
        <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-3">
          Variant Media ({{ variant.media.length }}) — <span class="text-orange-600">Drag to reorder (First is Main)</span>
        </label>

        <draggable
          v-model="variants[vIdx].media"
          item-key="preview"
          class="flex flex-wrap gap-3"
          handle=".drag-handle"
          ghost-class="opacity-50"
        >
          <template #item="{ element: img, index: iIdx }">
            <div class="relative w-28 h-28 border border-gray-200 rounded-lg overflow-hidden bg-white shadow-sm group">
              <img :src="img.preview" class="w-full h-full object-cover" />

              <div v-if="iIdx === 0" class="absolute top-0 left-0 bg-orange-500 text-white text-[9px] font-bold px-1.5 py-0.5 rounded-br-md uppercase">
                Main
              </div>

              <div class="drag-handle absolute inset-0 bg-black/20 opacity-0 group-hover:opacity-100 flex items-center justify-center cursor-move transition-opacity">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16" />
                </svg>
              </div>

              <button
                @click.stop="removeVariantMedia(vIdx, iIdx)"
                class="absolute top-1 right-1 bg-white/90 hover:bg-red-600 hover:text-white text-red-600 p-1 rounded-full shadow-md z-10 transition-all scale-75"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path d="M6 18L18 6M6 6l12 12" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </button>
            </div>
          </template>

          <template #footer>
            <div
              @click="$refs['variantInput' + vIdx][0].click()"
              class="w-28 h-28 border-2 border-dashed border-gray-300 rounded-lg flex flex-col items-center justify-center bg-white hover:bg-gray-50 hover:border-orange-400 group cursor-pointer transition-all"
            >
              <svg class="w-6 h-6 text-gray-400 group-hover:text-orange-500 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path d="M12 4v16m8-8H4" stroke-width="2" stroke-linecap="round"/>
              </svg>
              <span class="text-[10px] text-gray-500 font-bold uppercase">Add Media</span>
              <input :ref="'variantInput' + vIdx" type="file" multiple class="hidden" @change="handleVariantMedia($event, vIdx)" />
            </div>
          </template>
        </draggable>
      </div>

      <div class="grid grid-cols-2 gap-4 max-w-xl">
        <div>
          <label class="block text-xs font-semibold text-gray-600 mb-1">Variant SKU</label>
          <input v-model="variant.sku" type="text" class="w-full border border-gray-300 rounded-lg px-3 py-1.5 text-sm outline-none focus:ring-1 focus:ring-orange-500 bg-white" />
        </div>
        <div>
          <label class="block text-xs font-semibold text-gray-600 mb-1">Barcode</label>
          <input v-model="variant.barcode" type="text" class="w-full border border-gray-300 rounded-lg px-3 py-1.5 text-sm outline-none focus:ring-1 focus:ring-orange-500 bg-white" />
        </div>
      </div>
    </div>
  </td>
</tr>
      </template>
    </tbody>
  </table>
</div>
</section>
      </div>

      <div class="space-y-3 lg:col-span-4">
        <section class="tenant-product-editor__section px-3 py-3 sm:px-4 sm:py-3">
          <div class="mb-2 flex flex-wrap items-center gap-1.5">
            <h3 class="tenant-product-editor__section-title">Status</h3>
            <TenantFieldTip
              label="Status"
              text="Draft: not visible. Active: visible in store. Archived: hidden from customers."
            />
          </div>
          <select v-model="form.status" class="tenant-input-shopify w-full px-3 py-2 outline-none">
            <option value="draft">Draft</option>
            <option value="active">Active</option>
            <option value="archived">Archived</option>
          </select>
        </section>

        <!-- <section class="bg-white border border-gray-200 rounded-xl shadow-sm p-5">
          <h3 class="text-xs font-bold uppercase text-gray-500 mb-4 tracking-wider">Product organization</h3>
          <div class="space-y-4">

            <div>
              <label class="block text-xs font-semibold text-gray-700 mb-1.5">Vendor</label>
              <input v-model="form.vendor" type="text" placeholder="e.g. Adidas" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm outline-none focus:ring-1 focus:ring-black" />
            </div>
            <div>
              <label class="block text-xs font-semibold text-gray-700 mb-1.5">Collections</label>
              <input v-model="form.collection" type="text" placeholder="Summer 2026" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm outline-none focus:ring-1 focus:ring-black" />
            </div>
            <div>
              <label class="block text-xs font-semibold text-gray-700 mb-1.5">Tags</label>
              <input v-model="form.tags" type="text" placeholder="Vintage, Cotton" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm outline-none focus:ring-1 focus:ring-black" />
            </div>
          </div>
        </section> -->
      </div>

    </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted, nextTick } from 'vue'
import draggable from 'vuedraggable'
import Editor from '@tinymce/tinymce-vue'
import axiosTenant from '@/api/axiosTenant'
import Swal from 'sweetalert2'
import { formatApiErrorHtml } from '@tenant/helpers/apiErrorMessage'
import { focusFirstValidationField } from '@tenant/helpers/formFocus'
import TenantFieldTip from '@tenant/components/TenantFieldTip.vue'
import TenantSelectSearch from '@tenant/components/TenantSelectSearch.vue'
import { useRoute, useRouter } from 'vue-router'

const currentLocale = ref('en')
const route = useRoute()
const router = useRouter()

const PRODUCT_VALIDATION_FIELD_IDS = {
  name: 'product-name',
  sku: 'product-sku',
  barcode: 'product-barcode',
  price: 'product-price',
  stock: 'product-qty',
  quantity: 'product-qty',
  categories_id: 'product-cat-l3',
  brand_id: 'product-brand',
  compare_at_price: 'product-compare',
  cost_per_item: 'product-cost',
  type: 'product-name',
}
// --- State ---
const categoriesL1 = ref([])
const categoriesL2 = ref([])
const categoriesL3 = ref([])

const selectedL1 = ref('')
const selectedL2 = ref('')
const showAdvancedInventory = ref(false)
const selectedIndices = ref([])
const showAdditional = ref(false)
const showUnitPricePopover = ref(false)
const fileInput = ref(null)

const options = ref([])
const variants = ref([])
const allowedNames = ['Color', 'Size', 'Material']

const tempUnitPrice = ref({
  total: 0,
  type: 'g',
  base: 1,
  baseType: 'kg'
})

const form = ref({
  id: null,
  name: '',
  detailed_description: '',
  media_files: [],
  categories_id: '',
  status: 'draft',
  price: 0,
  compare_at_price: 0,
  cost_per_item: 0,
  charge_tax: true,
  inventory_tracked: true,
  quantity: 0,
  sku: '',
  barcode: '',
  vendor: '',
  collection: '',
  tags: '',
  unit_price_total: null,
  unit_price_type: 'g',
  unit_price_base: 1,
  unit_price_base_type: 'kg'
})

// --- Computed ---
const statusLabel = computed(() => {
  const labels = { draft: 'Draft', active: 'Active', archived: 'Archived' }
  return labels[form.value.status] || 'Draft'
})

const statusBadgeClass = computed(() => {
  const s = form.value.status
  if (s === 'active') return 'bg-emerald-100 text-emerald-800'
  if (s === 'archived') return 'bg-gray-200 text-gray-700'
  return 'bg-amber-100 text-amber-900'
})

const categoryOptionsL1 = computed(() => [
  { value: '', label: 'None' },
  ...categoriesL1.value.map((c) => ({
    value: c.id,
    label: c.translation?.name || '—',
  })),
])

const categoryOptionsL2 = computed(() => [
  { value: '', label: 'None' },
  ...categoriesL2.value.map((c) => ({
    value: c.id,
    label: c.translation?.name || '—',
  })),
])

const categoryOptionsL3 = computed(() => [
  { value: '', label: 'None' },
  ...categoriesL3.value.map((c) => ({
    value: c.id,
    label: c.translation?.name || '—',
  })),
])

const calculatedProfit = computed(() => {
  const cost = form.value.cost_per_item
  const c = cost === '' || cost === null ? 0 : Number(cost)
  return (form.value.price || 0) - (Number.isFinite(c) ? c : 0)
})

const calculatedMargin = computed(() => {
  if (!form.value.price || form.value.price === 0) return '0.00'
  return ((calculatedProfit.value / form.value.price) * 100).toFixed(2)
})

watch(selectedL1, () => {
  handleL1Change()
})

watch(selectedL2, () => {
  handleL2Change()
})

// --- Unit Price Methods ---
const applyUnitPrice = () => {
  form.value.unit_price_total = tempUnitPrice.value.total
  form.value.unit_price_type = tempUnitPrice.value.type
  form.value.unit_price_base = tempUnitPrice.value.base
  form.value.unit_price_base_type = tempUnitPrice.value.baseType
  showUnitPricePopover.value = false
}

const clearUnitPrice = () => {
  form.value.unit_price_total = null
  tempUnitPrice.value.total = 0
  showUnitPricePopover.value = false
}

// --- Media Methods ---
const triggerFileInput = () => fileInput.value.click()

function ensureMediaClientKey(file) {
  if (!file._clientKey) {
    file._clientKey =
      typeof crypto !== 'undefined' && crypto.randomUUID
        ? crypto.randomUUID()
        : `m-${Date.now()}-${Math.random().toString(36).slice(2, 9)}`
  }
  return file
}

const handleFileSelect = (event) => {
  const files = Array.from(event.target.files).map((file) => {
    file.preview = URL.createObjectURL(file)
    return ensureMediaClientKey(file)
  })
  form.value.media_files.push(...files)
}

const handleFileDrop = (event) => {
  const files = Array.from(event.dataTransfer.files).map((file) => {
    file.preview = URL.createObjectURL(file)
    return ensureMediaClientKey(file)
  })
  form.value.media_files.push(...files)
}

const removeSelectedFiles = () => {
  const sortedIndices = [...selectedIndices.value].sort((a, b) => b - a)
  sortedIndices.forEach(index => {
    const file = form.value.media_files[index]
    if (file.preview) URL.revokeObjectURL(file.preview)
    form.value.media_files.splice(index, 1)
  })
  selectedIndices.value = []
}

// Variant Media
const removeVariantMedia = (vIdx, iIdx) => {
  const img = variants.value[vIdx].media[iIdx]
  URL.revokeObjectURL(img.preview)
  variants.value[vIdx].media.splice(iIdx, 1)
}

const handleVariantMedia = (event, vIdx) => {
  const files = Array.from(event.target.files)
  files.forEach(file => {
    variants.value[vIdx].media.push({
      file,
      preview: URL.createObjectURL(file),
      id: Math.random().toString(36).substr(2, 9)
    })
  })
  event.target.value = ''
}

// --- Options & Variants ---
const availableOptionNames = (currentName) => {
  const pickedNames = options.value.map(o => o.name).filter(n => n !== currentName)
  return allowedNames.filter(name => !pickedNames.includes(name))
}

const addOption = () => {
  if (options.value.length < 3) options.value.push({ name: '', values: [], currentInput: '' })
}

const removeOption = (index) => {
  options.value.splice(index, 1)
  processVariants()
}

const addTag = (index) => {
  const val = options.value[index].currentInput.trim()
  if (val && !options.value[index].values.includes(val)) {
    options.value[index].values.push(val)
    options.value[index].currentInput = ''
    processVariants()
  }
}

const removeTag = (optIdx, tagIdx) => {
  options.value[optIdx].values.splice(tagIdx, 1)
  processVariants()
}

const processVariants = () => {
  const activeOptions = options.value.filter(o => o.name && o.values.length > 0)
  if (!activeOptions.length) return (variants.value = [])

  const optionValues = activeOptions.map(o => o.values)
  const combinations = optionValues.reduce(
    (a, b) => a.flatMap(d => b.map(e => [d, e].flat())),
    [[]]
  )

  variants.value = combinations.map(combo => {
    const title = combo.join(' / ')
    const existing = variants.value.find(v => v.title === title)
    const optionArray = activeOptions.map((opt, idx) => ({ name: opt.name, value: combo[idx] }))
    const subSku = combo.map(v => v.toString().substring(0, 3).toUpperCase()).join('-')
    const finalSku = form.value.sku ? `${form.value.sku}-${subSku}` : subSku
    return existing || { title, price: form.value.price, qty: 0, sku: finalSku, options: optionArray, media: [], expanded: false }
  })
}

// --- TinyMCE ---
const editorInit = {
  height: 300,
  menubar: false,
  plugins: 'lists link code table wordcount',
  toolbar: 'formatselect | bold italic underline strikethrough | alignleft aligncenter alignright | bullist numlist | link table code removeformat',
  content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; font-size: 14px }',
  skin: 'oxide',
  statusbar: false,
}

// --- Load existing product ---
// const form = ref({
//   name: '',
//   detailed_description: '',
//   price: 0,
//   compare_at_price: 0,
//   charge_tax: true,
//   inventory_tracked: true,
//   quantity: 0,
//   sku: '',
//   barcode: '',
//   cost_per_item: null,
//   media_files: [], // We will map 'media' from API to this
//   categories_id: null,
//   unit_price_total: null,
//   unit_price_type: 'g',
//   unit_price_base: 1,
//   unit_price_base_type: 'kg'
// });

const loadProduct = async (id) => {
  try {
    // Replace with your actual API call
    const response = await axiosTenant.get(`/products/${id}`);
    const product = response.data.data;

    // 1. Basic Field Mapping

    // 🔥 Translation Handling
    const translation = product.translations?.find(
      t => t.locale === currentLocale.value
    )

    // Fallback to first translation if locale not found
    const fallback = product.translations?.[0]

    form.value.name = translation?.name || fallback?.name || ''
    form.value.detailed_description = translation?.description || fallback?.description || ''

    form.value.price = parseFloat(product.price);
    form.value.compare_at_price = parseFloat(product.compare_at_price);
    form.value.charge_tax = !!product.charge_tax;
    form.value.inventory_tracked = !!product.inventory_tracked;
    form.value.quantity = product.quantity;
    form.value.sku = product.sku;
    form.value.barcode = product.barcode;
    form.value.cost_per_item = product.cost_per_item;

    // 2. Media Mapping (Critical for your draggable UI)
    if (product.media && product.media.length > 0) {
      form.value.media_files = product.media.map((item) => ({
        id: item.id,
        _clientKey: item.id != null ? `media-${item.id}` : `media-${item.cdn_url}`,
        preview: item.cdn_url,
        file: null,
        is_main: item.is_main,
      }))
    }

    form.value.categories_id = product.category_id || product.categories_id || ''

    const archived =
      product.status === 'archived' ||
      product.is_archived === 1 ||
      product.is_archived === true
    const active =
      product.is_active == 1 ||
      product.is_active === true ||
      product.is_active === '1' ||
      product.status === 'active'
    form.value.status = archived ? 'archived' : active ? 'active' : 'draft'

  } catch (error) {
    console.error("Failed to load product:", error);
  }
};

const closeModal = async () => {
  const r = await Swal.fire({
    icon: 'question',
    title: 'Discard changes?',
    showCancelButton: true,
    confirmButtonText: 'Discard',
    cancelButtonText: 'Keep editing',
  })
  if (r.isConfirmed) {
    await router.push({ name: 'product-list' })
  }
}

// --- Submit ---
const handleSubmit = async () => {
  const productId = route.params.id
  if (!productId) {
    await Swal.fire({
      icon: 'error',
      title: 'Cannot update',
      text: 'Product ID is missing. Cannot update.',
    })
    return
  }

  const name = (form.value.name || '').trim()
  if (!name) {
    await nextTick()
    const el = document.getElementById('product-name')
    el?.scrollIntoView({ block: 'center', behavior: 'smooth' })
    el?.focus({ preventScroll: true })
    await Swal.fire({
      icon: 'warning',
      title: 'Title required',
      text: 'Please enter a product title before saving.',
    })
    return
  }

  const formData = new FormData();

  // 2. Method Spoofing
  // We use POST + _method PUT because many servers (PHP/Laravel)
  // struggle to parse multipart/form-data on a native PUT request.
  formData.append('_method', 'PUT');

  // 3. Append main product fields (API expects track_quantity, stock, type — not inventory_tracked / quantity)
  const skipKeys = new Set([
    'media_files',
    'inventory_tracked',
    'quantity',
    'id',
  ])
  Object.keys(form.value).forEach((key) => {
    if (skipKeys.has(key)) return
    const value = form.value[key]

    if (key === 'categories_id') {
      formData.append(key, value || '')
      return
    }
    if (value !== null && value !== undefined && value !== '') {
      formData.append(key, value)
    }
  })

  formData.append('type', variants.value.length ? 'variant' : 'simple')
  formData.append('track_quantity', form.value.inventory_tracked ? '1' : '0')
  formData.append('stock', String(form.value.quantity ?? 0))
  formData.append('is_active', form.value.status === 'active' ? '1' : '0')
  formData.append('is_featured', '0')
  formData.append('allow_backorder', '0')

  form.value.media_files.forEach((file) => {
    if (file instanceof File) {
      formData.append('images[]', file)
    } else if (file.file instanceof File) {
      formData.append('images[]', file.file)
    }
  })

  // 4. Append Variants
  variants.value.forEach((v, i) => {
    if (v.id) formData.append(`variants[${i}][id]`, v.id);
    formData.append(`variants[${i}][title]`, v.title);
    formData.append(`variants[${i}][price]`, v.price || 0);
    formData.append(`variants[${i}][qty]`, v.qty || 0);
    formData.append(`variants[${i}][sku]`, v.sku || '');
    formData.append(`variants[${i}][barcode]`, v.barcode || '');

    ;(v.options || []).forEach((opt) => {
      formData.append(`variants[${i}][options][${opt.name}]`, opt.value)
    })

    if (v.media) {
      v.media.forEach(m => {
        if (m.file instanceof File) {
          formData.append(`variants[${i}][media][]`, m.file);
        }
      });
    }
  });

  try {
    // FIX: Note the use of backticks `` for the URL
    const response = await axiosTenant.post(`/products/${productId}`, formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    });

    console.log('Update Success:', response.data)
    await Swal.fire({
      icon: 'success',
      title: 'Saved',
      text: 'Product updated successfully!',
    })
  } catch (err) {
    console.error('Update Error:', err.response?.data)
    focusFirstValidationField(err, PRODUCT_VALIDATION_FIELD_IDS)
    await Swal.fire({
      icon: 'error',
      title: 'Update failed',
      html: formatApiErrorHtml(err),
      width: 520,
    })
  }
}


const fetchCategories = async (parentId = 'null') => {
  const pid = parentId === 'null' || parentId === null || parentId === '' ? 'null' : parentId
  try {
    const response = await axiosTenant.get(`/categories?parent_id=${pid}`)
    return response.data.categories || []
  } catch (error) {
    console.error('Error fetching categories:', error)
    return []
  }
}

// When Level 1 changes, reset others and fetch Level 2
const handleL1Change = async () => {
    selectedL2.value = ''
    form.value.categories_id = ''
    categoriesL3.value = []

    if (selectedL1.value) {
        categoriesL2.value = await fetchCategories(selectedL1.value)
    } else {
        categoriesL2.value = []
    }
}

// When Level 2 changes, fetch Level 3 (Final Leaf)
const handleL2Change = async () => {
    form.value.categories_id = ''

    if (selectedL2.value) {
        categoriesL3.value = await fetchCategories(selectedL2.value)
    } else {
        categoriesL3.value = []
    }
}

onMounted(async () => {
  categoriesL1.value = await fetchCategories('null')
  const productId = route.params.id
  if (productId) {
    await loadProduct(productId)
    await nextTick()
    document.getElementById('product-name')?.focus({ preventScroll: true })
  }
})
</script>

<style>
.tenant-product-editor--shopify .tox-tinymce {
  border: 1px solid #c9cccf !important;
  border-radius: 0.375rem !important;
}
.tenant-product-editor--shopify .tox-tinymce--focused {
  border-color: #303030 !important;
  box-shadow: 0 0 0 1px #303030 !important;
}
</style>
