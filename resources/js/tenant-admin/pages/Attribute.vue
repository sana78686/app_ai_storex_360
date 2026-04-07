<template>
  <div class="attributes-page tenant-dashboard-page max-w-6xl px-3 py-4 sm:px-4">
    <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
      <h2 class="tenant-dashboard-page__title text-xl">Attributes</h2>
      <button type="button" class="tenant-btn-submit tenant-btn-sm" @click="openAddModal">Add attribute</button>
    </div>
    <div v-if="loading" class="loading">Loading attributes...</div>
    <div v-else>
      <table class="attributes-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Values</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="attr in attributes" :key="attr.id">
            <td>{{ attr.id }}</td>
            <td>{{ attr.name }}</td>
            <td>
              <span v-for="val in attr.values" :key="val.id" class="value-badge">{{ val.value }}</span>
            </td>
            <td>
              <button @click="openEditModal(attr)" class="edit-btn">Edit</button>
              <button @click="openDeleteModal(attr)" class="delete-btn">Delete</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div v-if="showModal" class="tenant-crud-modal-overlay" @click.self="closeModal">
      <div class="tenant-crud-modal" role="dialog" aria-modal="true">
        <h3 class="tenant-crud-modal__title">{{ editMode ? 'Edit attribute' : 'Add attribute' }}</h3>
        <form class="space-y-3" @submit.prevent="editMode ? updateAttribute() : addAttribute()">
          <div>
            <div class="tenant-label-row">
              <label class="tenant-field-label mb-0" for="attr-form-name">Name</label>
              <span class="tenant-required-mark" aria-hidden="true">*</span>
              <TenantFieldTip label="Name" text="Shown when you pick options like Color or Size for a product." />
            </div>
            <input
              id="attr-form-name"
              v-model="form.name"
              type="text"
              class="tenant-input-shopify tenant-form-input-global w-full px-3 py-2 text-[13px]"
              autocomplete="off"
            />
          </div>
          <div>
            <div class="tenant-label-row">
              <label class="tenant-field-label mb-0" for="attr-form-values">Values</label>
              <TenantFieldTip label="Values" text="List choices separated by commas, for example: Red, Blue, Green." />
            </div>
            <input
              id="attr-form-values"
              v-model="form.valuesInput"
              type="text"
              placeholder="e.g. Red, Blue, Green"
              class="tenant-input-shopify tenant-form-input-global w-full px-3 py-2 text-[13px]"
              autocomplete="off"
            />
          </div>
          <div class="flex flex-wrap justify-end gap-2 pt-2">
            <button type="button" class="tenant-btn-secondary tenant-btn-sm" @click="closeModal">Cancel</button>
            <button type="submit" class="tenant-btn-submit tenant-btn-sm">{{ editMode ? 'Update' : 'Add' }}</button>
          </div>
          <div v-if="error" class="rounded-lg border border-red-100 bg-red-50 px-3 py-2 text-sm text-red-700">{{ error }}</div>
        </form>
      </div>
    </div>

    <div v-if="showDeleteModal" class="tenant-crud-modal-overlay" @click.self="closeDeleteModal">
      <div class="tenant-crud-modal" role="dialog" aria-modal="true">
        <h3 class="tenant-crud-modal__title">Delete attribute</h3>
        <p class="text-sm text-gray-600">
          Are you sure you want to delete <b>{{ selectedAttribute && selectedAttribute.name }}</b>?
        </p>
        <div class="mt-4 flex flex-wrap justify-end gap-2">
          <button type="button" class="tenant-btn-secondary tenant-btn-sm" @click="closeDeleteModal">Cancel</button>
          <button type="button" class="tenant-btn-submit tenant-btn-sm bg-red-700 hover:bg-red-800" @click="deleteAttribute">Delete</button>
        </div>
        <div v-if="error" class="mt-2 rounded-lg border border-red-100 bg-red-50 px-3 py-2 text-sm text-red-700">{{ error }}</div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch, nextTick } from 'vue'
import axiosTenant from '@/api/axiosTenant'
import TenantFieldTip from '@tenant/components/TenantFieldTip.vue'
import { focusFirstValidationField } from '@tenant/helpers/formFocus'

const ATTR_FIELD_IDS = { name: 'attr-form-name', values: 'attr-form-values', valuesInput: 'attr-form-values' }

const attributes = ref([])
const loading = ref(true)
const error = ref('')
const showModal = ref(false)
const showDeleteModal = ref(false)
const editMode = ref(false)
const selectedAttribute = ref(null)
const form = ref({ name: '', valuesInput: '' })

const fetchAttributes = async () => {
  loading.value = true
  try {
    const res = await axiosTenant.get('/attributes')
    attributes.value = res.data.data || res.data || []
  } catch (e) {
    error.value = 'Failed to load attributes.'
  } finally {
    loading.value = false
  }
}

const openAddModal = () => {
  editMode.value = false
  form.value = { name: '', valuesInput: '' }
  error.value = ''
  showModal.value = true
}

const openEditModal = (attr) => {
  editMode.value = true
  form.value = {
    name: attr.name,
    valuesInput: attr.values ? attr.values.map(v => v.value).join(', ') : ''
  }
  selectedAttribute.value = attr
  error.value = ''
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  selectedAttribute.value = null
  error.value = ''
}

const addAttribute = async () => {
  error.value = ''
  try {
    const payload = { name: form.value.name }
    const res = await axiosTenant.post('/attributes', payload)
    const attrId = res.data.id || res.data.data?.id || res.data.data?.id
    // Add values if provided
    if (form.value.valuesInput.trim()) {
      const values = form.value.valuesInput.split(',').map(v => v.trim()).filter(Boolean)
      for (const val of values) {
        await axiosTenant.post(`/attributes/${attrId}/values`, { value: val })
      }
    }
    await fetchAttributes()
    closeModal()
  } catch (e) {
    focusFirstValidationField(e, ATTR_FIELD_IDS)
    error.value = e.response?.data?.message || 'Failed to add attribute.'
  }
}

const updateAttribute = async () => {
  error.value = ''
  if (!selectedAttribute.value) return;
  try {
    const payload = { name: form.value.name }
    await axiosTenant.put(`/attributes/${selectedAttribute.value.id}`, payload)
    // Update values: for simplicity, remove all and re-add
    if (form.value.valuesInput.trim()) {
      await axiosTenant.delete(`/attributes/${selectedAttribute.value.id}/values`)
      const values = form.value.valuesInput.split(',').map(v => v.trim()).filter(Boolean)
      for (const val of values) {
        await axiosTenant.post(`/attributes/${selectedAttribute.value.id}/values`, { value: val })
      }
    }
    await fetchAttributes()
    closeModal()
  } catch (e) {
    focusFirstValidationField(e, ATTR_FIELD_IDS)
    error.value = e.response?.data?.message || 'Failed to update attribute.'
  }
}

const openDeleteModal = (attr) => {
  selectedAttribute.value = attr
  error.value = ''
  showDeleteModal.value = true
}

const closeDeleteModal = () => {
  showDeleteModal.value = false
  selectedAttribute.value = null
  error.value = ''
}

const deleteAttribute = async () => {
  error.value = ''
  if (!selectedAttribute.value) return;
  try {
    await axiosTenant.delete(`/attributes/${selectedAttribute.value.id}`)
    await fetchAttributes()
    closeDeleteModal()
  } catch (e) {
    error.value = e.response?.data?.message || 'Failed to delete attribute.'
  }
}

watch(showModal, async (open) => {
  if (open) {
    await nextTick()
    document.getElementById('attr-form-name')?.focus({ preventScroll: true })
  }
})

onMounted(fetchAttributes)
</script>

<style scoped>
.attributes-table {
  width: 100%;
  border-collapse: collapse;
  margin-bottom: 2rem;
}
.attributes-table th, .attributes-table td {
  border: 1px solid #ddd;
  padding: 8px;
}
.attributes-table th {
  background: #f4f4f4;
}
.value-badge {
  display: inline-block;
  background: #e0f7fa;
  color: #00796b;
  border-radius: 3px;
  padding: 2px 8px;
  margin: 0 2px 2px 0;
  font-size: 0.85em;
}
.loading {
  color: #888;
}
.edit-btn {
  background: #00B894;
  color: #fff;
  border: none;
  border-radius: 4px;
  padding: 0.4em 1.1em;
  font-weight: 500;
  margin-right: 0.6em;
  cursor: pointer;
  transition: background 0.2s;
}
.edit-btn:hover {
  background: #009e7a;
}
.delete-btn {
  background: #222;
  color: #fff;
  border: none;
  border-radius: 4px;
  padding: 0.4em 1.1em;
  font-weight: 500;
  cursor: pointer;
  transition: background 0.2s;
}
.delete-btn:hover {
  background: #444;
}
</style> 