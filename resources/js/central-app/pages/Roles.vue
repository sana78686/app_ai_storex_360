<template>
  <div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold">Roles</h1>
      <button
        @click="showCreateModal = true"
        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
      >
        Add New Role
      </button>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="flex justify-center items-center py-8">
      <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
    </div>

    <!-- Roles List -->
    <div v-else class="bg-white rounded-lg shadow-md overflow-hidden">
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Guard Name</th>
          
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created At</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-if="roles.length === 0">
              <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                No roles found
              </td>
            </tr>
            <tr v-for="role in roles" :key="role.id">
              <td class="px-6 py-4 whitespace-nowrap">{{ role.name }}</td>
              <td class="px-6 py-4 whitespace-nowrap">{{ role.guard_name }}</td>
              
              <td class="px-6 py-4 whitespace-nowrap">{{ formatDate(role.created_at) }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                <button
                  @click="editRole(role)"
                  class="text-indigo-600 hover:text-indigo-900 mr-3"
                >
                  Edit
                </button>
                <button
                  @click="deleteRole(role.id)"
                  class="text-red-600 hover:text-red-900"
                >
                  Delete
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Create Role Modal -->
    <div v-if="showCreateModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full flex items-center justify-center z-50 p-4">
      <div class="relative p-5 border shadow-lg rounded-md bg-white w-full max-w-md mx-auto">
        <div class="mt-3">
          <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Create New Role</h3>
          <form @submit.prevent="createRole">
            <div class="mb-4">
              <label for="newRoleName" class="block text-sm font-medium text-gray-700">Role Name</label>
              <input
                type="text"
                id="newRoleName"
                v-model="newRole.name"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                required
              />
            </div>
            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-2">Permissions</label>
              <div class="grid grid-cols-2 gap-2 max-h-48 overflow-y-auto border p-2 rounded-md">
                <div v-for="permission in allPermissions" :key="permission.id" class="flex items-center">
                  <input
                    type="checkbox"
                    :id="`new-permission-${permission.id}`"
                    :value="permission.name"
                    v-model="newRole.selectedPermissions"
                    class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"
                  >
                  <label :for="`new-permission-${permission.id}`" class="ml-2 block text-sm text-gray-900">{{ permission.name }}</label>
                </div>
              </div>
            </div>
            <div class="flex justify-end space-x-3">
              <button
                type="button"
                @click="showCreateModal = false"
                class="inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:text-sm"
              >
                Cancel
              </button>
              <button
                type="submit"
                :disabled="isCreating"
                class="inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:text-sm"
              >
                {{ isCreating ? 'Creating...' : 'Create' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Edit Role Modal -->
    <div v-if="showEditModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full flex items-center justify-center z-50 p-4">
      <div class="relative p-5 border shadow-lg rounded-md bg-white w-full max-w-md mx-auto">
        <div class="mt-3">
          <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Edit Role</h3>
          <form @submit.prevent="updateRole">
            <div class="mb-4">
              <label for="roleName" class="block text-sm font-medium text-gray-700">Role Name</label>
              <input
                type="text"
                id="roleName"
                v-model="editingRole.name"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                required
              />
            </div>
            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-2">Permissions</label>
              <div class="grid grid-cols-2 gap-2 max-h-48 overflow-y-auto border p-2 rounded-md">
                <div v-for="permission in allPermissions" :key="permission.id" class="flex items-center">
                  <input
                    type="checkbox"
                    :id="`edit-permission-${permission.id}`"
                    :value="permission.name"
                    v-model="editingRole.selectedPermissions"
                    class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"
                  >
                  <label :for="`edit-permission-${permission.id}`" class="ml-2 block text-sm text-gray-900">{{ permission.name }}</label>
                </div>
              </div>
            </div>
            <div class="flex justify-end space-x-3">
              <button
                type="button"
                @click="showEditModal = false"
                class="inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:text-sm"
              >
                Cancel
              </button>
              <button
                type="submit"
                :disabled="isUpdating"
                class="inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:text-sm"
              >
                {{ isUpdating ? 'Updating...' : 'Update' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue';
import axiosCentral from '@/api/axiosCentral';
import Swal from 'sweetalert2';

export default {
  name: 'Roles',
  setup() {
    const roles = ref([]);
    const allPermissions = ref([]);
    const loading = ref(false);
    const showCreateModal = ref(false);
    const showEditModal = ref(false);
    const isCreating = ref(false);
    const isUpdating = ref(false);
    const newRole = ref({
      name: '',
      selectedPermissions: []
    });
    const editingRole = ref({
      id: null,
      name: '',
      selectedPermissions: []
    });

    const fetchRoles = async () => {
      try {
        loading.value = true;
        const response = await axiosCentral.get('roles');
        if (response.data && response.data.status && Array.isArray(response.data.data.data)) {
          roles.value = response.data.data.data;
        } else {
          console.error('Invalid response format for roles:', response.data);
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Failed to load roles. Please try again later.'
          });
        }
      } catch (error) {
        console.error('Error fetching roles:', error);
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: error.response?.data?.message || 'Failed to fetch roles. Please try again later.'
        });
      } finally {
        loading.value = false;
      }
    };

    const fetchPermissions = async () => {
      try {
        const response = await axiosCentral.get('permissions');
        if (response.data && response.data.status && Array.isArray(response.data.data.data)) {
          allPermissions.value = response.data.data.data;
        } else {
          console.error('Invalid response format for permissions:', response.data);
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Failed to load permissions for selection. Please try again later.'
          });
        }
      } catch (error) {
        console.error('Error fetching permissions:', error);
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: error.response?.data?.message || 'Failed to fetch permissions for selection. Please try again later.'
        });
      }
    };

    const createRole = async () => {
      isCreating.value = true;
      try {
        const payload = {
          name: newRole.value.name,
          permissions: newRole.value.selectedPermissions
        };
        const response = await axiosCentral.post('roles', payload);
        if (response.data.status) {
          await Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: 'Role created successfully',
            confirmButtonColor: '#3085d6'
          });
          showCreateModal.value = false;
          newRole.value.name = '';
          newRole.value.selectedPermissions = [];
          fetchRoles();
        }
      } catch (error) {
        console.error('Error creating role:', error);
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: error.response?.data?.message || 'Failed to create role. Please try again later.',
          confirmButtonColor: '#3085d6'
        });
      } finally {
        isCreating.value = false;
      }
    };

    const editRole = (role) => {
      editingRole.value = { 
        ...role,
        selectedPermissions: role.permissions ? role.permissions.map(p => p.name) : []
      };
      showEditModal.value = true;
    };

    const updateRole = async () => {
      isUpdating.value = true;
      try {
        const payload = {
          name: editingRole.value.name,
          permissions: editingRole.value.selectedPermissions
        };
        const response = await axiosCentral.put(`roles/${editingRole.value.id}`, payload);
        if (response.data.status) {
          await Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: 'Role updated successfully',
            confirmButtonColor: '#3085d6'
          });
          showEditModal.value = false;
          fetchRoles();
        }
      } catch (error) {
        console.error('Error updating role:', error);
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: error.response?.data?.message || 'Failed to update role. Please try again later.',
          confirmButtonColor: '#3085d6'
        });
      } finally {
        isUpdating.value = false;
      }
    };

    const deleteRole = async (id) => {
      try {
        const result = await Swal.fire({
          title: 'Are you sure?',
          text: "You won't be able to revert this!",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Yes, delete it!'
        });

        if (result.isConfirmed) {
          await axiosCentral.delete(`roles/${id}`);
          await Swal.fire(
            'Deleted!',
            'Role has been deleted.',
            'success'
          );
          fetchRoles();
        }
      } catch (error) {
        console.error('Error deleting role:', error);
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: error.response?.data?.message || 'Failed to delete role. Please try again later.'
        });
      }
    };

    const formatDate = (date) => {
      return new Date(date).toLocaleDateString();
    };

    onMounted(() => {
      fetchRoles();
      fetchPermissions();
    });

    return {
      roles,
      allPermissions,
      loading,
      showCreateModal,
      showEditModal,
      isCreating,
      isUpdating,
      newRole,
      editingRole,
      fetchRoles,
      createRole,
      editRole,
      updateRole,
      deleteRole,
      formatDate
    };
  }
};
</script>

<style scoped>
/* Add any component-specific styles here if needed */
/* Responsive Modal Adjustments */
.fixed.inset-0 {
  padding: 20px; /* Add padding around the modal on all screen sizes */
}

.relative.p-5.border {
  width: 100%; /* Ensure content fills available width in modal */
  max-width: 500px; /* Max width for consistency */
  margin: auto; /* Center the modal horizontally */
}

@media (max-width: 768px) {
  .relative.p-5.border {
    max-width: 90vw; /* Occupy more width on smaller screens */
  }
}

.grid.grid-cols-2 {
  grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); /* Responsive grid for permissions */
}
</style> 