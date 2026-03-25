<template>
  <div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold">Permissions</h1>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="flex justify-center items-center py-8">
      <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
    </div>

    <!-- Permissions List -->
    <div v-else class="bg-white rounded-lg shadow-md overflow-hidden">
      <table class="min-w-full">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
            <!-- <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Guard Name</th> -->
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created At</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-if="permissions.length === 0">
            <td colspan="4" class="px-6 py-4 text-center text-gray-500">
              No permissions found
            </td>
          </tr>
          <tr v-for="permission in permissions" :key="permission.id">
            <td class="px-6 py-4 whitespace-nowrap">{{ permission.name }}</td>

            <td class="px-6 py-4 whitespace-nowrap">{{ formatDate(permission.created_at) }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
              <button
                @click="editPermission(permission)"
                class="text-indigo-600 hover:text-indigo-900 mr-3"
              >
                Edit
              </button>
              <button
                @click="deletePermission(permission.id)"
                class="text-red-600 hover:text-red-900"
              >
                Delete
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Edit Permission Modal -->
    <div v-if="showEditModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full flex items-center justify-center">
      <div class="relative p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
          <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Edit Permission</h3>
          <form @submit.prevent="updatePermission">
            <div class="mb-4">
              <label for="permissionName" class="block text-sm font-medium text-gray-700">Permission Name</label>
              <input
                type="text"
                id="permissionName"
                v-model="editingPermission.name"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                required
              />
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
                class="inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:text-sm"
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
  name: 'Permissions',
  setup() {
    const permissions = ref([]);
    const loading = ref(false);
    const showEditModal = ref(false);
    const isUpdating = ref(false);
    const editingPermission = ref({
      id: null,
      name: ''
    });

    const fetchPermissions = async () => {
      try {
        loading.value = true;
        console.log('Fetching permissions...');
        const response = await axiosCentral.get('permissions');
        console.log('API Response:', response.data);

        if (response.data && response.data.status && response.data.data) {
          permissions.value = response.data.data.data;
        } else {
          console.error('Invalid response format:', response.data);
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Failed to load permissions. Please try again later.'
          });
        }
      } catch (error) {
        console.error('Error details:', {
          message: error.message,
          response: error.response?.data,
          status: error.response?.status
        });
        
        if (error.response?.status === 401) {
          Swal.fire({
            icon: 'error',
            title: 'Authentication Error',
            text: 'Please log in to view permissions.'
          });
        } else {
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: error.response?.data?.message || 'Failed to fetch permissions. Please try again later.'
          });
        }
      } finally {
        loading.value = false;
      }
    };

    const editPermission = (permission) => {
      editingPermission.value = {
        id: permission.id,
        name: permission.name
      };
      showEditModal.value = true;
    };

    const updatePermission = async () => {
      try {
        isUpdating.value = true;
        const response = await axiosCentral.put(`permissions/${editingPermission.value.id}`, {
          name: editingPermission.value.name
        });

        if (response.data.status) {
          await Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: 'Permission updated successfully',
            confirmButtonColor: '#3085d6'
          });
          showEditModal.value = false;
          fetchPermissions(); // Refresh the list
        }
      } catch (error) {
        console.error('Error updating permission:', error);
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: error.response?.data?.message || 'Failed to update permission. Please try again later.',
          confirmButtonColor: '#3085d6'
        });
      } finally {
        isUpdating.value = false;
      }
    };

    const deletePermission = async (id) => {
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
          await axiosCentral.delete(`permissions/${id}`);
          await Swal.fire(
            'Deleted!',
            'Permission has been deleted.',
            'success'
          );
          fetchPermissions(); // Refresh the list after deletion
        }
      } catch (error) {
        console.error('Error deleting permission:', error);
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: error.response?.data?.message || 'Failed to delete permission. Please try again later.'
        });
      }
    };

    const formatDate = (date) => {
      return new Date(date).toLocaleDateString();
    };

    onMounted(() => {
      fetchPermissions();
    });

    return {
      permissions,
      loading,
      showEditModal,
      isUpdating,
      editingPermission,
      editPermission,
      updatePermission,
      deletePermission,
      formatDate
    };
  }
};
</script>

<style scoped>
/* Add any component-specific styles here */
</style> 