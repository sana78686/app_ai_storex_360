<template>
  <div class="permissions-page">
    <h2>Permissions</h2>
    <button class="add-btn" @click="openAddModal">Add Permission</button>
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

    <!-- Add/Edit Modal -->
    <div v-if="showModal" class="modal-overlay">
      <div class="modal">
        <h3>{{ editMode ? 'Edit Permission' : 'Add Permission' }}</h3>
        <form @submit.prevent="editMode ? updatePermission() : addPermission()">
          <div>
            <label>Name</label>
            <input v-model="form.name" />
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
        <h3>Delete Permission</h3>
        <p>Are you sure you want to delete permission <b>{{ selectedPermission && selectedPermission.name }}</b>?</p>
        <div class="modal-actions">
          <button @click="deletePermission">Yes, Delete</button>
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

onMounted(fetchPermissions)
</script>

<style scoped>
.permissions-page {
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