<template>
  <div class="permissions-page tenant-dashboard-page max-w-6xl px-3 py-4 sm:px-4">
    <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
      <h2 class="tenant-dashboard-page__title text-xl">Permissions</h2>
      <button type="button" class="tenant-btn-submit tenant-btn-sm" @click="openAddModal">Add permission</button>
    </div>
    <div v-if="loading" class="loading">Loading permissions...</div>
    <div v-else>
      <table class="permissions-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="permission in permissions" :key="permission.id">
            <td>{{ permission.id }}</td>
            <td>{{ permission.name }}</td>
            <td>
              <button @click="openEditModal(permission)" class="edit-btn">Edit</button>
              <button @click="openDeleteModal(permission)" class="delete-btn">Delete</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div v-if="showModal" class="tenant-crud-modal-overlay" @click.self="closeModal">
      <div class="tenant-crud-modal" role="dialog" aria-modal="true">
        <h3 class="tenant-crud-modal__title">{{ editMode ? 'Edit permission' : 'Add permission' }}</h3>
        <form class="space-y-3" @submit.prevent="editMode ? updatePermission() : addPermission()">
          <div>
            <div class="tenant-label-row">
              <label class="tenant-field-label mb-0" for="perm-form-name">Name</label>
              <span class="tenant-required-mark" aria-hidden="true">*</span>
              <TenantFieldTip label="Permission name" text="A short code for this permission, used in your app logic." />
            </div>
            <input
              id="perm-form-name"
              v-model="form.name"
              type="text"
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
        <h3 class="tenant-crud-modal__title">Delete permission</h3>
        <p class="text-sm text-gray-600">
          Are you sure you want to delete <b>{{ selectedPermission && selectedPermission.name }}</b>?
        </p>
        <div class="mt-4 flex flex-wrap justify-end gap-2">
          <button type="button" class="tenant-btn-secondary tenant-btn-sm" @click="closeDeleteModal">Cancel</button>
          <button type="button" class="tenant-btn-submit tenant-btn-sm bg-red-700 hover:bg-red-800" @click="deletePermission">Delete</button>
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

const PERM_FIELD_IDS = { name: 'perm-form-name' }

const permissions = ref([])
const loading = ref(true)
const error = ref('')
const showModal = ref(false)
const showDeleteModal = ref(false)
const editMode = ref(false)
const selectedPermission = ref(null)
const form = ref({ name: '' })

const fetchPermissions = async () => {
  loading.value = true
  try {
    const res = await axiosTenant.get('/permissions')
    permissions.value = res.data.data?.data || []
  } catch (e) {
    error.value = 'Failed to load permissions.'
  } finally {
    loading.value = false
  }
}

const openAddModal = () => {
  editMode.value = false
  form.value = { name: '' }
  error.value = ''
  showModal.value = true
}

const openEditModal = (permission) => {
  editMode.value = true
  form.value = { name: permission.name }
  selectedPermission.value = permission
  error.value = ''
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  selectedPermission.value = null
  error.value = ''
}

const addPermission = async () => {
  error.value = ''
  try {
    const payload = { ...form.value }
    await axiosTenant.post('/permissions', payload)
    await fetchPermissions()
    closeModal()
  } catch (e) {
    focusFirstValidationField(e, PERM_FIELD_IDS)
    error.value = e.response?.data?.message || 'Failed to add permission.'
  }
}

const updatePermission = async () => {
  error.value = ''
  if (!selectedPermission.value) return;
  try {
    const payload = { ...form.value }
    await axiosTenant.put(`/permissions/${selectedPermission.value?.id}`, payload)
    await fetchPermissions()
    closeModal()
  } catch (e) {
    focusFirstValidationField(e, PERM_FIELD_IDS)
    error.value = e.response?.data?.message || 'Failed to update permission.'
  }
}

const openDeleteModal = (permission) => {
  selectedPermission.value = permission
  error.value = ''
  showDeleteModal.value = true
}

const closeDeleteModal = () => {
  showDeleteModal.value = false
  selectedPermission.value = null
  error.value = ''
}

const deletePermission = async () => {
  error.value = ''
  if (!selectedPermission.value) return;
  try {
    await axiosTenant.delete(`/permissions/${selectedPermission.value?.id}`)
    await fetchPermissions()
    closeDeleteModal()
  } catch (e) {
    error.value = e.response?.data?.message || 'Failed to delete permission.'
  }
}

watch(showModal, async (open) => {
  if (open) {
    await nextTick()
    document.getElementById('perm-form-name')?.focus({ preventScroll: true })
  }
})

onMounted(fetchPermissions)
</script>

<style scoped>
.permissions-table {
  width: 100%;
  border-collapse: collapse;
  margin-bottom: 2rem;
}
.permissions-table th, .permissions-table td {
  border: 1px solid #ddd;
  padding: 8px;
}
.permissions-table th {
  background: #f4f4f4;
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