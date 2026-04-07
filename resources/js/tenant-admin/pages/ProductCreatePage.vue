<template>
  <div class="tenant-product-editor tenant-product-editor--shopify pb-12 text-[13px] leading-snug">
    <!-- Section 1: stays visible while you scroll -->
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
            <h1 class="truncate text-[15px] font-semibold text-[#303030]">{{ form.name.trim() ? form.name : 'New product' }}</h1>
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
        <!-- Main column (Shopify-style) -->
        <div class="space-y-3 lg:col-span-8">
          <section class="tenant-product-editor__section px-3 py-3 sm:px-4 sm:py-3">
            <div class="mb-3 flex flex-wrap items-center gap-1.5">
              <h2 class="tenant-product-editor__section-title">Product details</h2>
              <TenantFieldTip
                label="Help"
                text="Main info customers see: title, description, category, photos, price, and stock."
              />
            </div>
            <div class="space-y-3">
              <div>
                <div class="tenant-label-row">
                  <label class="tenant-field-label mb-0 cursor-pointer" for="product-name">Title</label>
                  <span class="tenant-required-mark" aria-hidden="true">*</span>
                  <TenantFieldTip
                    label="Title"
                    text="The product name shown in your store, search, cart, and orders."
                  />
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
                  <TenantFieldTip
                    label="Description"
                    text="Details for the product page: features, size, care, etc."
                  />
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
                  <TenantFieldTip
                    label="Category"
                    text="Where this product appears in your store navigation. Type to search."
                  />
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
            </div>
          </section>

          <section class="tenant-product-editor__section px-3 py-3 sm:px-4 sm:py-3">
            <div class="mb-2 flex min-h-[22px] items-center justify-between">
              <div v-if="selectedIndices.length === 0" class="flex flex-wrap items-center gap-1.5">
                <h3 class="tenant-product-editor__section-title">Media</h3>
                <TenantFieldTip
                  label="Media"
                  text="First image is the main photo. Drag to reorder."
                />
              </div>
              <div v-else class="flex w-full items-center justify-between">
                <div class="flex items-center gap-2">
                  <span class="rounded bg-[#303030] px-2 py-0.5 text-[11px] font-medium text-white">
                    {{ selectedIndices.length }} selected
                  </span>
                </div>
                <button type="button" class="text-[12px] font-medium text-[#d72c0d] hover:underline" @click="removeSelectedFiles">
                  Remove
                </button>
              </div>
              <button
                v-if="selectedIndices.length === 0"
                type="button"
                class="text-[12px] font-medium text-[#2c6ecb] hover:underline"
                @click="triggerFileInput"
              >
                Add files
              </button>
            </div>

            <div
              v-if="form.media_files.length === 0"
              class="flex cursor-pointer flex-col items-center justify-center rounded-md border border-dashed border-[#c9cccf] bg-[#fafafa] px-4 py-6 transition hover:bg-[#f3f3f3]"
              @click="triggerFileInput"
              @drop.prevent="handleFileDrop"
              @dragover.prevent
            >
              <p class="mb-2 text-center text-[12px] text-[#616161]">Drag images here or click to upload</p>
              <span class="tenant-input-shopify inline-block px-3 py-1.5 text-[12px] font-medium text-[#303030]">Upload</span>
              <input ref="fileInput" type="file" multiple accept="image/*" class="hidden" @change="handleFileSelect" />
            </div>

            <draggable
              v-else
              v-model="form.media_files"
              item-key="_clientKey"
              class="flex flex-wrap gap-2"
              ghost-class="opacity-40"
              animation="200"
            >
              <template #item="{ element, index }">
                <div
                  class="group relative cursor-move overflow-hidden rounded-md border border-[#e3e3e3] bg-white"
                  :class="index === 0 ? 'h-40 w-full max-w-[200px] sm:h-[168px] sm:w-[168px]' : 'h-[72px] w-[72px]'"
                >
                  <div class="absolute left-1 top-1 z-20">
                    <input
                      type="checkbox"
                      :value="index"
                      v-model="selectedIndices"
                      class="h-3.5 w-3.5 cursor-pointer rounded border-[#c9cccf]"
                      @click.stop
                    />
                  </div>
                  <img :src="element.preview" class="h-full w-full object-cover" />
                  <div
                    v-if="index === 0"
                    class="absolute bottom-1 left-1 rounded border border-[#e3e3e3] bg-white/95 px-1.5 py-0.5 text-[10px] font-semibold text-[#303030]"
                  >
                    Main
                  </div>
                </div>
              </template>
              <template #footer>
                <div
                  class="flex h-[72px] w-[72px] cursor-pointer items-center justify-center rounded-md border border-dashed border-[#c9cccf] hover:bg-[#fafafa]"
                  @click="triggerFileInput"
                >
                  <svg class="h-5 w-5 text-[#8c9196]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                  </svg>
                  <input ref="fileInput" type="file" multiple accept="image/*" class="hidden" @change="handleFileSelect" />
                </div>
              </template>
            </draggable>
          </section>

          <section class="tenant-product-editor__section px-3 py-3 sm:px-4 sm:py-3">
            <div class="mb-2 flex flex-wrap items-center gap-1.5">
              <h3 class="tenant-product-editor__section-title">Pricing & inventory</h3>
              <TenantFieldTip
                label="Pricing"
                text="Price and quantity. Turn off track quantity if you do not count stock for this product."
              />
            </div>
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 sm:gap-4">
              <div>
                <div class="tenant-label-row">
                  <label class="tenant-field-label mb-0 cursor-pointer" for="product-price">Price</label>
                  <span class="tenant-required-mark" aria-hidden="true">*</span>
                  <TenantFieldTip label="Price" text="The amount customers pay for one item." />
                </div>
                <div class="relative">
                  <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-[12px] text-[#616161]">Rs</span>
                  <input
                    id="product-price"
                    v-model.number="form.price"
                    type="number"
                    min="0"
                    step="0.01"
                    placeholder="0.00"
                    class="tenant-input-shopify w-full py-2 pl-8 pr-3"
                  />
                </div>
              </div>
              <div class="space-y-2 rounded-md border border-[#e3e3e3] bg-[#fafafa] px-3 py-2.5">
                <div class="flex items-center justify-between gap-2">
                  <span class="tenant-field-label text-[12px]">Track quantity</span>
                  <label class="inline-flex cursor-pointer items-center gap-0">
                    <input v-model="form.inventory_tracked" type="checkbox" class="peer sr-only" />
                    <span
                      class="relative h-6 w-11 shrink-0 rounded-full bg-[#c9cccf] transition-colors after:absolute after:left-0.5 after:top-0.5 after:h-5 after:w-5 after:rounded-full after:bg-white after:shadow after:transition-transform peer-checked:bg-[#303030] peer-checked:after:translate-x-[1.125rem]"
                    />
                  </label>
                </div>
                <div>
                  <label class="mb-0.5 block text-[11px] text-[#616161]" for="product-qty">Quantity</label>
                  <input
                    id="product-qty"
                    v-model.number="form.quantity"
                    type="number"
                    min="0"
                    class="tenant-input-shopify w-full px-3 py-2 text-right"
                  />
                </div>
              </div>
            </div>
          </section>
        </div>

        <!-- Sidebar (Shopify-style) -->
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

          <section class="tenant-product-editor__section px-3 py-3 sm:px-4 sm:py-3">
            <div class="mb-2 flex flex-wrap items-center gap-1.5">
              <h3 class="tenant-product-editor__section-title">Brand</h3>
              <TenantFieldTip label="Brand" text="Product brand, if you use brands in your store." />
            </div>
            <select id="product-brand" v-model="form.brand_id" class="tenant-input-shopify mb-3 w-full px-3 py-2 outline-none">
              <option value="">—</option>
              <option v-for="b in brands" :key="b.id" :value="b.id">{{ b.name }}</option>
            </select>
            <label class="flex cursor-pointer items-start gap-2 border-t border-[#e3e3e3] pt-2.5">
              <input v-model="form.is_featured" type="checkbox" class="mt-0.5 h-4 w-4 rounded border-[#c9cccf] text-[#303030]" />
              <span class="text-[12px] leading-tight text-[#303030]">Featured product</span>
            </label>
          </section>

          <section class="tenant-product-editor__section px-3 py-3 sm:px-4 sm:py-3">
            <div class="mb-2 flex flex-wrap items-center gap-1.5">
              <h3 class="tenant-product-editor__section-title">SKU & barcode</h3>
              <TenantFieldTip label="Codes" text="Your internal SKU and package barcode (optional)." />
            </div>
            <div class="space-y-2">
              <div>
                <label class="mb-0.5 block text-[11px] text-[#616161]" for="product-sku">SKU</label>
                <input id="product-sku" v-model="form.sku" type="text" placeholder="—" class="tenant-input-shopify w-full px-3 py-2" />
              </div>
              <div>
                <label class="mb-0.5 block text-[11px] text-[#616161]" for="product-barcode">Barcode</label>
                <input id="product-barcode" v-model="form.barcode" type="text" placeholder="—" class="tenant-input-shopify w-full px-3 py-2" />
              </div>
            </div>
          </section>

          <section class="tenant-product-editor__section px-3 py-3 sm:px-4 sm:py-3">
            <div class="mb-2 flex flex-wrap items-center gap-1.5">
              <h3 class="tenant-product-editor__section-title">More pricing</h3>
              <TenantFieldTip label="Pricing" text="Compare-at price, your cost, and tax." />
            </div>
            <div class="space-y-2">
              <div>
                <label class="mb-0.5 block text-[11px] text-[#616161]">Compare-at</label>
                <div class="relative">
                  <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-[12px] text-[#616161]">Rs</span>
                  <input
                    id="product-compare"
                    v-model.number="form.compare_at_price"
                    type="number"
                    min="0"
                    step="0.01"
                    class="tenant-input-shopify w-full py-2 pl-8 pr-3"
                  />
                </div>
              </div>
              <div>
                <label class="mb-0.5 block text-[11px] text-[#616161]">Cost per item</label>
                <div class="relative">
                  <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-[12px] text-[#616161]">Rs</span>
                  <input
                    id="product-cost"
                    v-model.number="form.cost_per_item"
                    type="number"
                    min="0"
                    step="0.01"
                    class="tenant-input-shopify w-full py-2 pl-8 pr-3"
                  />
                </div>
              </div>
              <label class="flex cursor-pointer items-center gap-2 pt-0.5">
                <input id="tax" v-model="form.charge_tax" type="checkbox" class="h-4 w-4 rounded border-[#c9cccf] text-[#303030]" />
                <span class="text-[12px] text-[#303030]">Charge tax</span>
              </label>
              <div class="grid grid-cols-2 gap-2 border-t border-[#e3e3e3] pt-2">
                <div class="rounded border border-[#e3e3e3] bg-[#fafafa] px-2 py-1.5">
                  <span class="text-[10px] text-[#616161]">Profit</span>
                  <p class="text-[12px] font-semibold text-[#303030]">{{ form.price ? 'Rs ' + calculatedProfit.toFixed(2) : '—' }}</p>
                </div>
                <div class="rounded border border-[#e3e3e3] bg-[#fafafa] px-2 py-1.5">
                  <span class="text-[10px] text-[#616161]">Margin</span>
                  <p class="text-[12px] font-semibold text-[#303030]">{{ form.price ? calculatedMargin + '%' : '—' }}</p>
                </div>
              </div>
            </div>
          </section>

          <section class="tenant-product-editor__section px-3 py-3 sm:px-4 sm:py-3">
            <div class="mb-2 flex flex-wrap items-center gap-1.5">
              <h3 class="tenant-product-editor__section-title">Inventory policy</h3>
              <TenantFieldTip label="Backorder" text="Allow customers to buy when quantity is zero." />
            </div>
            <label class="flex cursor-pointer items-start gap-2">
              <input id="oversell" v-model="form.continue_selling" type="checkbox" class="mt-0.5 h-4 w-4 rounded border-[#c9cccf] text-[#303030]" />
              <span class="text-[12px] leading-tight text-[#303030]">Continue selling when out of stock</span>
            </label>
          </section>
        </div>

        <section class="tenant-product-editor__section overflow-hidden lg:col-span-12">
          <div class="flex items-center gap-2 border-b border-[#e3e3e3] px-3 py-2.5 sm:px-4">
            <h3 class="tenant-product-editor__section-title">Variants</h3>
            <TenantFieldTip
              label="Variants"
              text="Different options (size, color, etc.). Each combination can have its own price, stock, SKU, and images."
            />
          </div>

          <div v-if="options.length > 0" class="space-y-2 bg-[#fafafa] p-2 sm:p-3">
            <div
              v-for="(option, index) in options"
              :key="index"
              class="space-y-2 rounded-md border border-[#e3e3e3] bg-white p-3"
            >
      <div class="flex justify-between items-center">
        <label class="text-xs font-bold uppercase text-gray-500">Option name</label>
        <button @click="removeOption(index)" class="text-gray-400 hover:text-red-500">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2"/></svg>
        </button>
      </div>

              <select v-model="option.name" class="tenant-input-shopify w-full px-3 py-2 text-[13px] outline-none">
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
                type="text"
                placeholder="Add value, Enter"
                class="tenant-input-shopify w-full px-3 py-2 text-[13px]"
                @input="processVariants"
                @keydown.enter.prevent="addTag(index)"
              />
            </div>
            </div>
          </div>
          <div class="px-3 py-2 sm:px-4">
            <button
              v-if="options.length < 3"
              type="button"
              class="rounded-md border border-[#e3e3e3] bg-white px-3 py-1.5 text-[12px] font-medium text-[#303030] hover:bg-[#fafafa]"
              @click="addOption"
            >
              + Add option (e.g. Size)
            </button>
          </div>
          <div v-if="variants.length > 0">
            <div
              v-if="options.length > 1"
              class="flex items-center justify-between border-b border-[#e3e3e3] bg-[#fafafa] px-3 py-2 text-[12px] text-[#616161]"
            >
    <span class="text-xs font-medium text-gray-600">Previewing combinations</span>
    <button class="text-xs font-bold text-blue-600 flex items-center gap-1 group">
      Group by {{ options[0].name }}
      <svg class="w-3 h-3 transition-transform group-hover:translate-y-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path d="M19 9l-7 7-7-7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
    </button>
  </div>

            <table class="w-full border-collapse text-left text-[13px]">
              <thead class="border-b border-[#e3e3e3] bg-[#fafafa]">
                <tr>
                  <th class="w-8 p-2">
                    <input type="checkbox" class="h-3.5 w-3.5 rounded border-[#c9cccf]" />
                  </th>
                  <th class="p-2 text-[11px] font-semibold uppercase tracking-wide text-[#616161]">Variant</th>
                  <th class="p-2 text-[11px] font-semibold uppercase tracking-wide text-[#616161]">Price</th>
                  <th class="p-2 text-[11px] font-semibold uppercase tracking-wide text-[#616161]">Stock</th>
                  <th class="p-2 text-[11px] font-semibold uppercase tracking-wide text-[#616161]">SKU</th>
                  <th class="w-8 p-2"></th>
                </tr>
              </thead>
    <tbody>
      <template v-for="(variant, vIdx) in variants" :key="vIdx">
        <tr
          class="cursor-pointer border-b border-[#e3e3e3] transition-colors hover:bg-[#fafafa]"
          @click="variant.expanded = !variant.expanded"
        >
          <td class="p-2" @click.stop>
            <input type="checkbox" class="h-3.5 w-3.5 rounded border-[#c9cccf]" />
          </td>
          <td class="p-2">
            <div class="flex items-center gap-2">
              <div class="relative flex h-9 w-9 items-center justify-center overflow-hidden rounded border border-[#e3e3e3] bg-[#fafafa]">
                <img v-if="variant.media.length" :src="variant.media[0].preview" class="w-full h-full object-cover" />
                <svg v-else class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <div v-if="variant.media.length > 1" class="absolute bottom-0 right-0 bg-black/70 text-[8px] text-white px-1 leading-tight rounded-tl">
                  +{{ variant.media.length - 1 }}
                </div>
              </div>
              <span class="text-[13px] font-medium text-[#303030]">{{ variant.title }}</span>
            </div>
          </td>
          <td class="p-2">
            <div class="relative w-[5.5rem]">
              <span class="pointer-events-none absolute left-2 top-1/2 -translate-y-1/2 text-[11px] text-[#8c9196]">Rs</span>
              <input
                v-model.number="variant.price"
                type="number"
                class="tenant-input-shopify w-full py-1 pl-6 pr-1.5 text-[13px]"
                @click.stop
              />
            </div>
          </td>
          <td class="p-2">
            <input
              v-model.number="variant.qty"
              type="number"
              class="tenant-input-shopify w-14 px-2 py-1 text-right text-[13px]"
              @click.stop
            />
          </td>
          <td class="p-2 font-mono text-[12px] text-[#616161]">
            {{ variant.sku || '—' }}
          </td>
          <td class="p-2">
            <svg
              class="h-4 w-4 text-[#8c9196] transition-transform duration-200"
              :class="{'rotate-180': variant.expanded}"
              fill="none" stroke="currentColor" viewBox="0 0 24 24"
            >
              <path d="M19 9l-7 7-7-7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </td>
        </tr>

        <tr v-if="variant.expanded" class="bg-[#fafafa]">
          <td colspan="6" class="border-b border-[#e3e3e3] p-3 sm:p-4">
            <div class="space-y-4">
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

              <div class="max-w-md">
                <label class="mb-1 block text-[12px] font-medium text-[#303030]">Variant SKU</label>
                <input v-model="variant.sku" type="text" class="tenant-input-shopify w-full px-3 py-2 text-[13px]" />
                <p class="mt-1 text-[11px] text-[#616161]">Optional code for this variant (saved on the variant row).</p>
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
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted, nextTick } from 'vue'
import { useRouter } from 'vue-router'
import draggable from 'vuedraggable'
import Editor from '@tinymce/tinymce-vue'
import axiosTenant from '@/api/axiosTenant'
import Swal from 'sweetalert2'
import { formatApiErrorHtml } from '@tenant/helpers/apiErrorMessage'
import { focusFirstValidationField } from '@tenant/helpers/formFocus'
import TenantFieldTip from '@tenant/components/TenantFieldTip.vue'
import TenantSelectSearch from '@tenant/components/TenantSelectSearch.vue'

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

const router = useRouter()

// --- State ---
// --- Category State ---
const categoriesL1 = ref([])
const categoriesL2 = ref([])
const categoriesL3 = ref([])

const selectedL1 = ref('')
const selectedL2 = ref('')
const selectedIndices = ref([])
const fileInput = ref(null)
const brands = ref([])
const options = ref([])
const variants = ref([])
const allowedNames = ['Color', 'Size', 'Material']

const form = ref({
    name: '',
    detailed_description: '',
    media_files: [],
    categories_id: '',
    status: 'draft',
    price: 0,
    compare_at_price: null,
    cost_per_item: null,
    charge_tax: true,
    inventory_tracked: true,
    quantity: 0,
    sku: '',
    barcode: '',
    brand_id: '',
    is_featured: false,
    vendor: '',
    collection: '',
    tags: '',
    continue_selling: false,
})

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

watch(selectedL1, () => {
  handleL1Change()
})

watch(selectedL2, () => {
  handleL2Change()
})
const calculatedProfit = computed(() => {
  const cost = form.value.cost_per_item
  const c = cost === '' || cost === null ? 0 : Number(cost)
  return (form.value.price || 0) - (Number.isFinite(c) ? c : 0)
})

const calculatedMargin = computed(() => {
  if (!form.value.price || form.value.price === 0) return '0.00'
  return ((calculatedProfit.value / form.value.price) * 100).toFixed(2)
})

// --- Media Methods ---
const triggerFileInput = () => fileInput.value.click()

function ensureMediaClientKey(file) {
  if (!file._clientKey) {
    file._clientKey = typeof crypto !== 'undefined' && crypto.randomUUID ? crypto.randomUUID() : `m-${Date.now()}-${Math.random().toString(36).slice(2, 9)}`
  }
  return file
}

const handleFileSelect = (event) => {
    const files = Array.from(event.target.files).map(file => {
        file.preview = URL.createObjectURL(file)
        return ensureMediaClientKey(file)
    })
    form.value.media_files.push(...files)
}

const handleFileDrop = (event) => {
    const files = Array.from(event.dataTransfer.files).map(file => {
        file.preview = URL.createObjectURL(file)
        return ensureMediaClientKey(file)
    })
    form.value.media_files.push(...files)
}

// Bulk removal method
const removeSelectedFiles = () => {
    // Sort indices descending so splicing doesn't mess up the order
    const sortedIndices = [...selectedIndices.value].sort((a, b) => b - a)

    sortedIndices.forEach(index => {
        const file = form.value.media_files[index]
        if (file.preview) URL.revokeObjectURL(file.preview)
        form.value.media_files.splice(index, 1)
    })

    // Clear selection
    selectedIndices.value = []
}
// const removeFile = (index) => {
//     const file = form.value.media_files[index]
//     if (file.preview) URL.revokeObjectURL(file.preview)
//     form.value.media_files.splice(index, 1)
// }

const getFilePreview = (file) => file.preview
// Optional: Modify your existing removeFile to also clear the checkbox if clicked
const removeFile = (index) => {
    const file = form.value.media_files[index]
    if (file.preview) URL.revokeObjectURL(file.preview)
    form.value.media_files.splice(index, 1)
    selectedIndices.value = selectedIndices.value.filter(i => i !== index)
}
//    variant //

// 1. Dropdown logic: Filter names that are already picked
const availableOptionNames = (currentName) => {
  const pickedNames = options.value.map(o => o.name).filter(n => n !== currentName)
  return allowedNames.filter(name => !pickedNames.includes(name))
}

const addOption = () => {
  if (options.value.length < 3) {
    options.value.push({ name: '', values: [], currentInput: '' })
  }
}

// 2. Tag logic
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

// 3. Variant Generation with Auto-SKU



// 4. Multiple Media Logic


const removeVariantMedia = (vIdx, iIdx) => {
  const img = variants.value[vIdx].media[iIdx]
  URL.revokeObjectURL(img.preview)
  variants.value[vIdx].media.splice(iIdx, 1)
}
const handleVariantMedia = (event, vIdx) => {
  const files = Array.from(event.target.files)
  files.forEach(file => {
    variants.value[vIdx].media.push({
      file: file,
      preview: URL.createObjectURL(file),
      id: Math.random().toString(36).substr(2, 9) // Added unique ID for draggable
    })
  })
  event.target.value = '' // Reset input so same file can be uploaded again
}

// const addOption = () => {
//     options.value.push({ name: '', rawValue: '', values: [] })
// }

const removeOption = (index) => {
    options.value.splice(index, 1)
    processVariants()
}



// --- TinyMCE Init ---
const editorInit = {
    height: 260,
    menubar: false,
    plugins: 'lists link code table wordcount',
    toolbar: 'formatselect | bold italic underline strikethrough | alignleft aligncenter alignright | bullist numlist | link table code removeformat',
    content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; font-size: 14px }',
    skin: 'oxide',
    statusbar: false,
}


const processVariants = () => {
  const activeOptions = options.value.filter(o => o.name && o.values.length > 0)
  if (activeOptions.length === 0) return (variants.value = [])

  const optionValues = activeOptions.map(o => o.values)
  const combinations = optionValues.reduce(
    (a, b) => a.flatMap(d => b.map(e => [d, e].flat())),
    [[]]
  )

  variants.value = combinations.map(combo => {
    const title = combo.join(' / ')
    const existing = variants.value.find(v => v.title === title)

    // Create array of option objects for backend
    const optionArray = activeOptions.map((opt, idx) => ({
      name: opt.name,
      value: combo[idx]
    }))

    // Auto SKU logic
    const subSku = combo.map(v => v.toString().substring(0, 3).toUpperCase()).join('-')
    const finalSku = form.value.sku ? `${form.value.sku}-${subSku}` : subSku

    return existing || {
      title,
      price: form.value.price || 0,
      qty: 0,
      sku: finalSku,
      options: optionArray,  // <-- Array of {name, value}
      media: [],
      expanded: false
    }
  })
}

// --- Submit ---
const handleSubmit = async () => {
  const f = form.value
  const name = (f.name || '').trim()
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

  const formData = new FormData()
  const productType = variants.value.length ? 'variant' : 'simple'

  formData.append('name', name)
  formData.append('detailed_description', f.detailed_description || '')
  formData.append('sku', f.sku || '')
  formData.append('barcode', f.barcode || '')
  formData.append('price', String(f.price ?? 0))
  if (f.compare_at_price != null && f.compare_at_price !== '') {
    formData.append('compare_at_price', String(f.compare_at_price))
  }
  formData.append('status', f.status || 'draft')
  formData.append('type', productType)
  formData.append('track_quantity', f.inventory_tracked ? '1' : '0')
  formData.append('stock', String(f.quantity ?? 0))
  formData.append('is_active', f.status === 'active' ? '1' : '0')
  formData.append('is_featured', f.is_featured ? '1' : '0')
  formData.append('allow_backorder', f.continue_selling ? '1' : '0')
  formData.append('charge_tax', f.charge_tax ? '1' : '0')
  if (f.cost_per_item !== '' && f.cost_per_item != null) {
    formData.append('cost_per_item', String(f.cost_per_item))
  }
  if (f.brand_id !== '' && f.brand_id != null) {
    formData.append('brand_id', String(f.brand_id))
  }
  if (f.categories_id !== '' && f.categories_id != null) {
    formData.append('categories_id', String(f.categories_id))
  }

  variants.value.forEach((v, i) => {
    formData.append(`variants[${i}][title]`, v.title || '')
    formData.append(`variants[${i}][price]`, String(v.price ?? 0))
    formData.append(`variants[${i}][qty]`, String(v.qty ?? 0))
    formData.append(`variants[${i}][sku]`, v.sku || '')
    ;(v.options || []).forEach((opt) => {
      formData.append(`variants[${i}][options][${opt.name}]`, opt.value)
    })
    ;(v.media || []).forEach((img) => {
      if (img.file instanceof File) {
        formData.append(`variants[${i}][media][]`, img.file)
      }
    })
  })

  f.media_files.forEach((item) => {
    const file = item instanceof File ? item : item?.file
    if (file instanceof File) {
      formData.append('images[]', file)
    }
  })

  try {
    await axiosTenant.post('/products', formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    })
    await Swal.fire({
      icon: 'success',
      title: 'Saved',
      text: 'Your product was saved. You can find it in the product list.',
    })
    await router.push({ name: 'product-list' })
  } catch (error) {
    console.error('Error saving product:', error.response?.data || error)
    focusFirstValidationField(error, PRODUCT_VALIDATION_FIELD_IDS)
    await Swal.fire({
      icon: 'error',
      title: 'Save failed',
      html: formatApiErrorHtml(error),
      width: 520,
    })
  }
}



const closeModal = async () => {
  const r = await Swal.fire({
    icon: 'warning',
    title: 'Leave this page?',
    text: 'If you leave now, you may lose what you typed.',
    showCancelButton: true,
    confirmButtonText: 'Leave',
    cancelButtonText: 'Keep editing',
  })
  if (r.isConfirmed) {
    await router.push({ name: 'product-list' })
  }
}




// --- Category State ---


// --- Fetching Logic ---
const fetchCategories = async (parentId = 'null') => {
    try {
        const response = await axiosTenant.get(`/categories?parent_id=${parentId}`)
        return response.data.categories // Assuming your controller returns the array directly
    } catch (error) {
        console.error("Error fetching categories:", error)
        return []
    }
}

const fetchBrands = async () => {
  try {
    const res = await axiosTenant.get('/brands')
    brands.value = res.data?.brands || []
  } catch (e) {
    console.error('Error fetching brands:', e)
    brands.value = []
  }
}

onMounted(async () => {
  categoriesL1.value = await fetchCategories('null')
  await fetchBrands()
  await nextTick()
  document.getElementById('product-name')?.focus({ preventScroll: true })
})

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
