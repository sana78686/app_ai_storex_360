<template>
  <div class="users-page">
    <h2>Users</h2>
    <button class="add-btn" @click="openAddModal">Add User</button>
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

    <!-- Add/Edit Modal -->
    <div v-if="showModal" class="modal-overlay">
      <div class="modal">
        <div>MODAL IS OPEN</div>
        <h3>{{ editMode ? 'Edit User' : 'Add User' }}</h3>
        <form @submit.prevent="editMode ? updateUser() : addUser()">
          <div>
            <label>Name</label>
            <input v-model="form.name" required />
          </div>
          <div>
            <label>Email</label>
            <input v-model="form.email" type="email" required />
          </div>
          <div v-if="!editMode">
            <label>Password</label>
            <input
              v-model="form.password"
              name="password"
              type="password"
              autocomplete="new-password"
              required
            />
          </div>
          <div>
            <label>Roles</label>
            <select v-model="form.roles" multiple required>
              <option v-for="role in roles" :key="role.id" :value="role.id">{{ role.name }}</option>
            </select>
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
        <h3>Delete User</h3>
        <p>Are you sure you want to delete user <b>{{ selectedUser?.email }}</b>?</p>
        <div class="modal-actions">
          <button @click="deleteUser">Yes, Delete</button>
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
  console.log(showModal.value)
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

onMounted(() => {
  fetchUsers()
  fetchRoles()
})
</script>

<style scoped>
.users-page {
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

/* ... (keep your existing styles for other elements) ... */

.modal-overlay {
  position: fixed !important;
  top: 0; left: 0; right: 0; bottom: 0;
  background: rgba(0,0,0,0.3);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 99999 !important;
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

.modal h3 {
  margin-top: 0;
  color: #333;
  font-size: 1.5rem;
}

.modal form {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.modal label {
  display: block;
  margin-bottom: 0.5rem;
  color: #555;
  font-weight: 500;
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

.modal select[multiple] {
  min-height: 100px;
  
}

.modal-actions {
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
  margin-top: 1rem;
}

.modal-actions button {
  padding: 0.75rem 1.5rem;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-weight: 500;
}

.modal-actions button[type="submit"] {
  background: #00B894;
  color: white;
}

.modal-actions button[type="button"] {
  background: #eee;
  color: #333;
}

.error {
  color: #e74c3c;
  margin-top: 1rem;
  padding: 0.75rem;
  background: #fdecea;
  border-radius: 4px;
}
</style>