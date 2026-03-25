<template>
  <div class="attributes-page">
    <h2>Attributes</h2>
    <button class="add-btn" @click="openAddModal">Add Attribute</button>
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

    <!-- Add/Edit Modal -->
    <div v-if="showModal" class="modal-overlay">
      <div class="modal">
        <h3>{{ editMode ? 'Edit Attribute' : 'Add Attribute' }}</h3>
        <form @submit.prevent="editMode ? updateAttribute() : addAttribute()">
          <div>
            <label>Name</label>
            <input v-model="form.name" />
          </div>
          <div>
            <label>Values (comma separated)</label>
            <input v-model="form.valuesInput" placeholder="e.g. Red, Blue, Green" />
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
        <h3>Delete Attribute</h3>
        <p>Are you sure you want to delete attribute <b>{{ selectedAttribute && selectedAttribute.name }}</b>?</p>
        <div class="modal-actions">
          <button @click="deleteAttribute">Yes, Delete</button>
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

onMounted(fetchAttributes)
</script>

<style scoped>
.attributes-page {
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