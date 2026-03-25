<template>
  <div class="roles-page">
    <h2>Roles</h2>
    <button class="add-btn" @click="openAddModal">Add Role</button>
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

    <!-- Add/Edit Modal -->
    <div v-if="showModal" class="modal-overlay">
      <div class="modal">
        <h3>{{ editMode ? 'Edit Role' : 'Add Role' }}</h3>
        <form @submit.prevent="editMode ? updateRole() : addRole()">
          <div>
            <label>Name</label>
            <input v-model="form.name" />
          </div>
          <div>
            <label>Permissions</label>
            <div class="perm-list">
              <label v-for="perm in permissions" :key="perm.id" class="perm-checkbox">
                <input type="checkbox" :value="perm.name" v-model="form.permissions" />
                {{ perm.name }}
              </label>
            </div>
          </div>
          <div class="modal-actions">
            <button type="submit">{{ editMode ? 'Update' : 'Add' }}</button>
            <button type="button" @click="closeModal">Cancel</button>
          </div>
          <div v-if="error" class="error">{{ error }}</div>
        </form>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div v-if="showDeleteModal" class="modal-overlay">
      <div class="modal">
        <h3>Delete Role</h3>
        <p>Are you sure you want to delete role <b>{{ selectedRole && selectedRole.name }}</b>?</p>
        <div class="modal-actions">
          <button @click="deleteRole">Yes, Delete</button>
          <button @click="closeDeleteModal">Cancel</button>
        </div>
        <div v-if="error" class="error">{{ error }}</div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axiosTenant from '@/api/axiosTenant'

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

onMounted(() => {
  fetchRoles()
  fetchPermissions()
})
</script>

<style scoped>
.roles-page {
  padding: 2rem;
}
.add-btn {
  margin-bottom: 1rem;
  padding: 0.5rem 1.2rem;
  background: #00B894;
  color: #fff;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-weight: 500;
}
.add-btn:hover {
  background: #009e7a;
}
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
.perm-list {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
  margin-bottom: 1rem;
}
.perm-checkbox {
  display: flex;
  align-items: center;
  gap: 0.3rem;
  font-size: 0.95em;
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
.modal-overlay {
  position: fixed;
  top: 0; left: 0; right: 0; bottom: 0;
  background: rgba(0,0,0,0.3);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}
.modal {
    position: relative !important;
  background: #fff !important;
  color: #222 !important;
  padding: 2rem !important;
  border-radius: 8px !important;
  min-width: 350px !important;
  max-width: 480px !important;
  width: 100%;
  box-shadow: 0 2px 16px rgba(0,0,0,0.15) !important;
  z-index: 100000 !important;
  display: block !important;
  max-height: 90vh;
  overflow-y: auto;
}
.modal-actions {
  margin-top: 1rem;
  display: flex;
  gap: 1rem;
}
.error {
  color: #d81818;
  margin-top: 0.5rem;
}
.modal input,
.modal select {
  width: 100%;
  padding: 0.5rem;
  margin-top: 0.2rem;
  margin-bottom: 1rem;
  border: 1px solid #ccc;
  border-radius: 4px;
  font-size: 1rem;
  background: #fff;
  color: #222;
}
</style> 