<template>
  <div class="tenant-category-page tenant-dashboard-page mx-auto max-w-5xl px-3 py-4 pb-20 text-[13px] sm:px-4">
    <header
      class="sticky top-16 z-[45] -mx-1 mb-4 flex flex-wrap items-center justify-between gap-2 rounded-xl border border-gray-200/90 bg-white/95 px-3 py-2.5 shadow-sm backdrop-blur-sm sm:-mx-0 sm:px-4"
    >
      <div class="min-w-0">
        <p class="tenant-dashboard-page__breadcrumb mb-0.5">Catalog / Categories</p>
        <h1 class="tenant-dashboard-page__title text-lg sm:text-xl">Add category</h1>
      </div>
      <div class="flex flex-wrap gap-2">
        <button type="button" class="tenant-btn-secondary tenant-btn-sm" @click="goBack">Cancel</button>
        <button type="button" class="tenant-btn-submit tenant-btn-sm" :disabled="saving" @click="saveCategory">
          {{ saving ? 'Saving…' : 'Save category' }}
        </button>
      </div>
    </header>

    <div class="tenant-settings-form-grid">
      <div class="space-y-4">
        <section class="rounded-xl border border-[#e3e3e3] bg-white p-4 shadow-sm sm:p-5">
          <h2 class="mb-3 text-xs font-bold uppercase tracking-wide text-[#616161]">General</h2>
          <div class="space-y-4">
            <div>
              <div class="tenant-label-row">
                <label class="tenant-field-label mb-0" for="cat-name">Category name</label>
                <span class="tenant-required-mark" aria-hidden="true">*</span>
                <TenantFieldTip label="Name" text="The name shoppers see in your store navigation." />
              </div>
              <input
                id="cat-name"
                v-model="form.name"
                type="text"
                class="tenant-input-shopify tenant-form-input-global w-full px-3 py-2"
                placeholder="e.g. Electronics"
                autocomplete="off"
              />
            </div>
            <div>
              <div class="tenant-label-row">
                <label class="tenant-field-label mb-0" for="cat-desc">Description</label>
                <TenantFieldTip label="Description" text="Optional text for category pages or SEO." />
              </div>
              <textarea
                id="cat-desc"
                v-model="form.description"
                rows="4"
                class="tenant-input-shopify tenant-form-input-global w-full px-3 py-2"
                placeholder="Describe this category…"
              />
            </div>
          </div>
        </section>
      </div>

      <div class="space-y-4">
        <section class="rounded-xl border border-[#e3e3e3] bg-white p-4 shadow-sm sm:p-5">
          <h2 class="mb-3 text-xs font-bold uppercase tracking-wide text-[#616161]">Placement</h2>
          <div class="tenant-label-row">
            <span class="tenant-field-label">Parent category</span>
            <TenantFieldTip label="Parent" text="Leave empty for a top-level category, or nest under another." />
          </div>
          <TenantSelectSearch
            v-model="form.parent_id"
            input-id="cat-parent"
            placeholder="Search parent…"
            :options="parentOptions"
          />
        </section>

        <section
          v-if="previewImage"
          class="rounded-xl border border-[#e3e3e3] bg-white p-4 shadow-sm sm:p-5"
        >
          <h2 class="mb-2 text-xs font-bold uppercase tracking-wide text-[#616161]">Image preview</h2>
          <img :src="previewImage" alt="" class="max-h-40 rounded-lg object-contain" />
        </section>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, nextTick } from 'vue'
import { useRouter } from 'vue-router'
import axiosTenant from '@/api/axiosTenant'
import Swal from 'sweetalert2'
import TenantFieldTip from '@tenant/components/TenantFieldTip.vue'
import TenantSelectSearch from '@tenant/components/TenantSelectSearch.vue'
import { focusFirstValidationField } from '@tenant/helpers/formFocus'

const router = useRouter()
const saving = ref(false)
const previewImage = ref(null)
const categoryTree = ref([])

const form = ref({
  name: '',
  description: '',
  parent_id: '',
})

const CAT_FIELD_IDS = {
  name: 'cat-name',
  description: 'cat-desc',
  parent_id: 'cat-parent',
}

function translationName(cat) {
  const t = cat.translations?.find((x) => x.locale === 'en') || cat.translations?.[0]
  return t?.name || '—'
}

function flattenTree(nodes, prefix = '') {
  const out = []
  if (!nodes) return out
  for (const c of nodes) {
    out.push({ value: c.id, label: `${prefix}${translationName(c)}` })
    if (c.children?.length) {
      out.push(...flattenTree(c.children, `${prefix}— `))
    }
  }
  return out
}

const parentOptions = computed(() => [
  { value: '', label: 'None (root)' },
  ...flattenTree(categoryTree.value),
])

async function loadCategories() {
  try {
    const res = await axiosTenant.get('/categories')
    categoryTree.value = res.data.categories || []
  } catch (e) {
    console.error(e)
  }
}

function goBack() {
  router.push({ name: 'dashboard-home' })
}

async function saveCategory() {
  const name = (form.value.name || '').trim()
  if (!name) {
    await nextTick()
    const el = document.getElementById('cat-name')
    el?.scrollIntoView({ block: 'center', behavior: 'smooth' })
    el?.focus({ preventScroll: true })
    await Swal.fire({ icon: 'warning', title: 'Name required', text: 'Please enter a category name.' })
    return
  }

  saving.value = true
  try {
    const payload = {
      name,
      description: (form.value.description || '').trim() || null,
      parent_id: form.value.parent_id === '' || form.value.parent_id == null ? null : form.value.parent_id,
    }
    await axiosTenant.post('/categories', payload)
    await Swal.fire({ icon: 'success', title: 'Saved', text: 'Category created.' })
    form.value = { name: '', description: '', parent_id: '' }
    previewImage.value = null
    await loadCategories()
  } catch (e) {
    focusFirstValidationField(e, CAT_FIELD_IDS)
    await Swal.fire({
      icon: 'error',
      title: 'Save failed',
      text: e.response?.data?.message || 'Could not save category.',
    })
  } finally {
    saving.value = false
  }
}

onMounted(async () => {
  await loadCategories()
  await nextTick()
  document.getElementById('cat-name')?.focus({ preventScroll: true })
})
</script>
