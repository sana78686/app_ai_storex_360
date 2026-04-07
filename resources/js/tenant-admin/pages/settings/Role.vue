<template>
  <div class="roles-page tenant-dashboard-page max-w-6xl px-3 py-4 sm:px-4">
    <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
      <h2 class="tenant-dashboard-page__title text-xl">Roles</h2>
      <button type="button" class="tenant-btn-submit tenant-btn-sm" @click="openAddModal">Add role</button>
    </div>
    <div v-if="loading" class="loading">Loading roles...</div>
    <div v-else>
      <table class="roles-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Permissions</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="role in roles" :key="role.id">
            <td>{{ role.id }}</td>
            <td>{{ role.name }}</td>
            <td>
              <span v-for="perm in role.permissions" :key="perm.id" class="perm-badge">{{ perm.name }}</span>
            </td>
            <td>
              <button @click="openEditModal(role)" class="edit-btn">Edit</button>
              <button @click="openDeleteModal(role)" class="delete-btn">Delete</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div v-if="showModal" class="tenant-crud-modal-overlay" @click.self="closeModal">
      <div class="tenant-crud-modal max-w-lg" role="dialog" aria-modal="true">
        <h3 class="tenant-crud-modal__title">{{ editMode ? 'Edit role' : 'Add role' }}</h3>
        <form class="space-y-3" @submit.prevent="editMode ? updateRole() : addRole()">
          <div>
            <div class="tenant-label-row">
              <label class="tenant-field-label mb-0" for="role-form-name">Name</label>
              <span class="tenant-required-mark" aria-hidden="true">*</span>
              <TenantFieldTip label="Role name" text="A label for this role, for example Manager or Staff." />
            </div>
            <input
              id="role-form-name"
              v-model="form.name"
              type="text"
              class="tenant-input-shopify tenant-form-input-global w-full px-3 py-2 text-[13px]"
              autocomplete="off"
            />
          </div>
          <div>
            <div class="tenant-label-row">
              <span class="tenant-field-label">Permissions</span>
              <TenantFieldTip label="Permissions" text="Tick what this role is allowed to do in the admin." />
            </div>
            <div class="perm-list max-h-48 overflow-y-auto rounded-lg border border-[#e3e3e3] bg-[#fafafa] p-2">
              <label v-for="perm in permissions" :key="perm.id" class="perm-checkbox flex cursor-pointer items-center gap-2 py-1 text-[13px] text-[#303030]">
                <input type="checkbox" :value="perm.name" v-model="form.permissions" class="rounded border-[#c9cccf] text-[#275a19]" />
                {{ perm.name }}
              </label>
            </div>
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
        <h3 class="tenant-crud-modal__title">Delete role</h3>
        <p class="text-sm text-gray-600">
          Are you sure you want to delete <b>{{ selectedRole && selectedRole.name }}</b>?
        </p>
        <div class="mt-4 flex flex-wrap justify-end gap-2">
          <button type="button" class="tenant-btn-secondary tenant-btn-sm" @click="closeDeleteModal">Cancel</button>
          <button type="button" class="tenant-btn-submit tenant-btn-sm bg-red-700 hover:bg-red-800" @click="deleteRole">Delete</button>
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

const ROLE_FIELD_IDS = { name: 'role-form-name' }

const roles = ref([])
const permissions = ref([])
const loading = ref(true)
const error = ref('')
const showModal = ref(false)
const showDeleteModal = ref(false)
const editMode = ref(false)
const selectedRole = ref(null)
const form = ref({ name: '', permissions: [] })

const fetchRoles = async () => {
  loading.value = true
  try {
    const res = await axiosTenant.get('/roles')
    roles.value = res.data.data?.data || res.data.data || res.data || []
  } catch (e) {
    error.value = 'Failed to load roles.'
  } finally {
    loading.value = false
  }
}

const fetchPermissions = async () => {
  try {
    const res = await axiosTenant.get('/permissions')
    permissions.value = res.data.data?.data || res.data.data || res.data || []
  } catch (e) {
    error.value = 'Failed to load permissions.'
  }
}

const openAddModal = () => {
  editMode.value = false
  form.value = { name: '', permissions: [] }
  error.value = ''
  showModal.value = true
}

const openEditModal = (role) => {
  editMode.value = true
  form.value = { name: role.name, permissions: role.permissions.map(p => p.name) }
  selectedRole.value = role
  error.value = ''
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  selectedRole.value = null
  error.value = ''
}

const addRole = async () => {
  error.value = ''
  try {
    const payload = { ...form.value }
    await axiosTenant.post('/roles', payload)
    await fetchRoles()
    closeModal()
  } catch (e) {
    focusFirstValidationField(e, ROLE_FIELD_IDS)
    error.value = e.response?.data?.message || 'Failed to add role.'
  }
}

const updateRole = async () => {
  error.value = ''
  if (!selectedRole.value) return;
  try {
    const payload = { ...form.value }
    await axiosTenant.put(`/roles/${selectedRole.value?.id}`, payload)
    await fetchRoles()
    closeModal()
  } catch (e) {
    focusFirstValidationField(e, ROLE_FIELD_IDS)
    error.value = e.response?.data?.message || 'Failed to update role.'
  }
}

const openDeleteModal = (role) => {
  selectedRole.value = role
  error.value = ''
  showDeleteModal.value = true
}

const closeDeleteModal = () => {
  showDeleteModal.value = false
  selectedRole.value = null
  error.value = ''
}

const deleteRole = async () => {
  error.value = ''
  if (!selectedRole.value) return;
  try {
    await axiosTenant.delete(`/roles/${selectedRole.value?.id}`)
    await fetchRoles()
    closeDeleteModal()
  } catch (e) {
    error.value = e.response?.data?.message || 'Failed to delete role.'
  }
}

watch(showModal, async (open) => {
  if (open) {
    await nextTick()
    document.getElementById('role-form-name')?.focus({ preventScroll: true })
  }
})

onMounted(() => {
  fetchRoles()
  fetchPermissions()
})
</script>

<style scoped>
.roles-table {
  width: 100%;
  border-collapse: collapse;
  margin-bottom: 2rem;
}
.roles-table th, .roles-table td {
  border: 1px solid #ddd;
  padding: 8px;
}
.roles-table th {
  background: #f4f4f4;
}
.perm-badge {
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