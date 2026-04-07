<template>
  <div class="users-page tenant-dashboard-page max-w-6xl px-3 py-4 sm:px-4">
    <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
      <h2 class="tenant-dashboard-page__title text-xl">Users</h2>
      <button type="button" class="tenant-btn-submit tenant-btn-sm" @click="openAddModal">Add user</button>
    </div>
    <div v-if="loading" class="loading">Loading users...</div>
    <div v-else>
      <table class="users-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Role(s)</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="user in users" :key="user.id">
            <td>{{ user.id }}</td>
            <td>{{ user.name }}</td>
            <td>{{ user.email }}</td>
            <td>
              <span v-if="user.roles && user.roles.length">
                {{ user.roles.map(r => r.name).join(', ') }}
              </span>
              <span v-else>N/A</span>
            </td>
            <td>
              <button @click="openEditModal(user)" class="edit-btn">Edit</button>
              <button @click="openDeleteModal(user)" class="delete-btn">Delete</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div v-if="showModal" class="tenant-crud-modal-overlay" @click.self="closeModal">
      <div class="tenant-crud-modal max-w-lg" role="dialog" aria-modal="true">
        <h3 class="tenant-crud-modal__title">{{ editMode ? 'Edit user' : 'Add user' }}</h3>
        <form class="space-y-3" @submit.prevent="editMode ? updateUser() : addUser()">
          <div>
            <div class="tenant-label-row">
              <label class="tenant-field-label mb-0" for="user-form-name">Name</label>
              <span class="tenant-required-mark" aria-hidden="true">*</span>
              <TenantFieldTip label="Name" text="The person’s display name in the admin." />
            </div>
            <input
              id="user-form-name"
              v-model="form.name"
              type="text"
              required
              class="tenant-input-shopify tenant-form-input-global w-full px-3 py-2 text-[13px]"
              autocomplete="name"
            />
          </div>
          <div>
            <div class="tenant-label-row">
              <label class="tenant-field-label mb-0" for="user-form-email">Email</label>
              <span class="tenant-required-mark" aria-hidden="true">*</span>
              <TenantFieldTip label="Email" text="Used to sign in. Must be unique." />
            </div>
            <input
              id="user-form-email"
              v-model="form.email"
              type="email"
              required
              class="tenant-input-shopify tenant-form-input-global w-full px-3 py-2 text-[13px]"
              autocomplete="email"
            />
          </div>
          <div v-if="!editMode">
            <div class="tenant-label-row">
              <label class="tenant-field-label mb-0" for="user-form-password">Password</label>
              <span class="tenant-required-mark" aria-hidden="true">*</span>
              <TenantFieldTip label="Password" text="A strong password for the new account." />
            </div>
            <input
              id="user-form-password"
              v-model="form.password"
              name="password"
              type="password"
              autocomplete="new-password"
              required
              class="tenant-input-shopify tenant-form-input-global w-full px-3 py-2 text-[13px]"
            />
          </div>
          <div>
            <div class="tenant-label-row">
              <label class="tenant-field-label mb-0" for="user-form-roles">Roles</label>
              <span class="tenant-required-mark" aria-hidden="true">*</span>
              <TenantFieldTip label="Roles" text="Hold Ctrl (Windows) or Cmd (Mac) to pick more than one role." />
            </div>
            <select
              id="user-form-roles"
              v-model="form.roles"
              multiple
              required
              class="tenant-input-shopify tenant-form-input-global min-h-[7rem] w-full px-3 py-2 text-[13px]"
            >
              <option v-for="role in roles" :key="role.id" :value="role.id">{{ role.name }}</option>
            </select>
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
        <h3 class="tenant-crud-modal__title">Delete user</h3>
        <p class="text-sm text-gray-600">
          Are you sure you want to delete <b>{{ selectedUser?.email }}</b>?
        </p>
        <div class="mt-4 flex flex-wrap justify-end gap-2">
          <button type="button" class="tenant-btn-secondary tenant-btn-sm" @click="closeDeleteModal">Cancel</button>
          <button type="button" class="tenant-btn-submit tenant-btn-sm bg-red-700 hover:bg-red-800" @click="deleteUser">Delete</button>
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

const USER_FIELD_IDS = {
  name: 'user-form-name',
  email: 'user-form-email',
  password: 'user-form-password',
  roles: 'user-form-roles',
}

const users = ref([])
const roles = ref([])
const loading = ref(true)
const error = ref('')
const showModal = ref(false)
const showDeleteModal = ref(false)
const editMode = ref(false)
const selectedUser = ref(null)
const form = ref({ name: '', email: '', password: '', roles: [] })

const fetchUsers = async () => {
  loading.value = true
  try {
    const res = await axiosTenant.get('/users')
    users.value = res.data.data || []
  } catch (e) {
    error.value = 'Failed to load users.'
  } finally {
    loading.value = false
  }
}

const fetchRoles = async () => {
  try {
    const res = await axiosTenant.get('/users/roles')
    roles.value = res.data.data || []
  } catch (e) {
    error.value = 'Failed to load roles.'
  }
}

const openAddModal = () => {
  editMode.value = false
  form.value = { name: '', email: '', password: '', roles: [] }
  error.value = ''
  showModal.value = true
}

const openEditModal = (user) => {
  editMode.value = true
  form.value = {
    name: user.name || '',
    email: user.email || '',
    password: '',
    roles: user.roles ? user.roles.map(r => r.id) : []
  }
  selectedUser.value = user
  error.value = ''
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  selectedUser.value = null
  error.value = ''
}

const addUser = async () => {
  error.value = ''
  try {
    const payload = { ...form.value }
    const res = await axiosTenant.post('/signup', payload)
    await fetchUsers()
    closeModal()
  } catch (e) {
    focusFirstValidationField(e, USER_FIELD_IDS)
    error.value = e.response?.data?.message || 'Failed to add user.'
  }
}

const updateUser = async () => {
  error.value = ''
  try {
    const payload = { ...form.value }
    delete payload.password
    const res = await axiosTenant.put(`/users/${selectedUser.value.id}`, payload)
    await fetchUsers()
    closeModal()
  } catch (e) {
    focusFirstValidationField(e, USER_FIELD_IDS)
    error.value = e.response?.data?.message || 'Failed to update user.'
  }
}

const openDeleteModal = (user) => {
  selectedUser.value = user
  error.value = ''
  showDeleteModal.value = true
}

const closeDeleteModal = () => {
  showDeleteModal.value = false
  selectedUser.value = null
  error.value = ''
}

const deleteUser = async () => {
  error.value = ''
  try {
    await axiosTenant.delete(`/users/${selectedUser.value.id}`)
    await fetchUsers()
    closeDeleteModal()
  } catch (e) {
    error.value = e.response?.data?.message || 'Failed to delete user.'
  }
}

watch(showModal, async (open) => {
  if (open) {
    await nextTick()
    document.getElementById('user-form-name')?.focus({ preventScroll: true })
  }
})

onMounted(() => {
  fetchUsers()
  fetchRoles()
})
</script>

<style scoped>
.users-table {
  width: 100%;
  border-collapse: collapse;
  margin-bottom: 2rem;
}
.users-table th, .users-table td {
  border: 1px solid #ddd;
  padding: 8px;
}
.users-table th {
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