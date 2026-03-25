<template>
  <div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold">Tenants</h1>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="flex justify-center items-center py-8">
      <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
    </div>

    <!-- Tenants List -->
    <div v-else class="bg-white rounded-lg shadow-md overflow-x-auto">
      <table class="w-full">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Store Name</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Domain</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created At</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-if="tenants.length === 0">
            <td colspan="5" class="px-6 py-4 text-center text-gray-500">
              No tenants found
            </td>
          </tr>
          <tr v-for="tenant in tenants" :key="tenant.id">
            <td class="px-6 py-4 whitespace-nowrap">{{ tenant.store_name }}</td>
            <td class="px-6 py-4 whitespace-nowrap">{{ tenant.domain }}</td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span :class="getStatusClass(tenant.status)">{{ tenant.status }}</span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">{{ formatDate(tenant.created_at) }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
              <button
                @click="editTenant(tenant)"
                class="text-indigo-600 hover:text-indigo-900 mr-3"
              >
                Edit
              </button>
              <button
                @click="deleteTenant(tenant.id)"
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
</template>

<script>
import { ref, onMounted } from 'vue';
import axiosCentral from '@/api/axiosCentral';
import Swal from 'sweetalert2';

export default {
  name: 'Tenants',
  setup() {
    const tenants = ref([]);
    const loading = ref(false);

    const fetchTenants = async () => {
      try {
        loading.value = true;
        console.log('Fetching tenants...');
        const response = await axiosCentral.get('tenants');
        console.log('API Response:', response.data);

        if (response.data && response.data.tenants) {
          tenants.value = response.data.tenants;
        } else {
          console.error('Invalid response format:', response.data);
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Failed to load tenants. Please try again later.'
          });
        }
      } catch (error) {
        console.error('Error details:', {
          message: error.message,
          response: error.response?.data,
          status: error.response?.status
        });
        
        if (error.response?.status === 401) {
          // Handle unauthorized error
          Swal.fire({
            icon: 'error',
            title: 'Authentication Error',
            text: 'Please log in to view tenants.'
          });
          // You might want to redirect to login page here
          // router.push('/login');
        } else {
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: error.response?.data?.message || 'Failed to fetch tenants. Please try again later.'
          });
        }
      } finally {
        loading.value = false;
      }
    };

    const editTenant = (tenant) => {
      // Handle edit
      console.log('Edit tenant:', tenant);
    };

    const deleteTenant = async (id) => {
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
          await axiosCentral.delete(`/api/tenants/${id}`);
          await Swal.fire(
            'Deleted!',
            'Tenant has been deleted.',
            'success'
          );
          fetchTenants(); // Refresh the list after deletion
        }
      } catch (error) {
        console.error('Error deleting tenant:', error);
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: error.response?.data?.message || 'Failed to delete tenant. Please try again later.'
        });
      }
    };

    const getStatusClass = (status) => {
      const classes = {
        active: 'px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800',
        inactive: 'px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800',
        trial: 'px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800'
      };
      return classes[status] || classes.inactive;
    };

    const formatDate = (date) => {
      return new Date(date).toLocaleDateString();
    };

    // Fetch tenants when component is mounted
    onMounted(() => {
      fetchTenants();
    });

    return {
      tenants,
      loading,
      editTenant,
      deleteTenant,
      getStatusClass,
      formatDate
    };
  }
};
</script>

<style scoped>
/* Base styles for the management page (similar to Permissions.vue for consistency) */
.tenants-management {
  padding: 2rem;
  background-color: #f0f2f5;
  min-height: calc(100vh - 64px); /* Adjust based on navbar height */
}

/* Card-like containers */
.bg-white {
  background-color: white;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

/* Form styling */
.shadow {
  box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
}

.shadow-md {
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
}

.appearance-none {
  -webkit-appearance: none;
  -moz-appearance: none;
  appearance: none;
}

.border {
  border-width: 1px;
}

.rounded {
  border-radius: 0.25rem;
}

.w-full {
  width: 100%;
}

.py-2 {
  padding-top: 0.5rem;
  padding-bottom: 0.5rem;
}

.px-3 {
  padding-left: 0.75rem;
  padding-right: 0.75rem;
}

.text-gray-700 {
  color: #4a5568;
}

.leading-tight {
  line-height: 1.25;
}

.focus\:outline-none:focus {
  outline: 0;
}

.focus\:shadow-outline:focus {
  box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.5);
}

/* Buttons */
.bg-blue-500 {
  background-color: #4299e1;
}

.hover\:bg-blue-700:hover {
  background-color: #2b6cb0;
}

.bg-gray-500 {
  background-color: #a0aec0;
}

.hover\:bg-gray-700:hover {
  background-color: #4a5568;
}

.text-white {
  color: #fff;
}

.font-bold {
  font-weight: 700;
}

.rounded {
  border-radius: 0.25rem;
}

.py-2 {
  padding-top: 0.5rem;
  padding-bottom: 0.5rem;
}

.px-4 {
  padding-left: 1rem;
  padding-right: 1rem;
}

.focus\:outline-none:focus {
  outline: 0;
}

.focus\:shadow-outline:focus {
  box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.5);
}

.text-red-500 {
  color: #ef4444;
}

.text-sm {
  font-size: 0.875rem;
}

.mt-4 {
  margin-top: 1rem;
}

.text-center {
  text-align: center;
}

.mb-6 {
  margin-bottom: 1.5rem;
}

.mb-4 {
  margin-bottom: 1rem;
}

/* Table styling (reusing dashboard table styles if available) */
.table-responsive {
  width: 100%;
  overflow-x: auto;
}

.table {
  width: 100%;
  border-collapse: collapse;
}

.table th,
.table td {
  padding: 1rem;
  text-align: left;
  border-bottom: 1px solid #eee;
  white-space: nowrap; /* Prevent text wrapping in table cells */
}

.table th {
  background-color: #f8f9fa;
  font-weight: 600;
  color: #555;
}

/* Action buttons in table */
.btn.btn-sm {
  padding: 0.25rem 0.5rem;
  font-size: 0.875rem;
  margin-right: 0.25rem;
}

.btn-info {
  background: #e3f2fd;
  color: #1976d2;
  border: none;
}

.badge-info {
  background: #e0f2f7;
  color: #0277bd;
}

.btn-info:hover {
  background: #c7e5fd;
}

.btn-warning {
  background: #fff3e0;
  color: #ef6c00;
  border: none;
}

.btn-warning:hover {
  background: #ffe0b2;
}

.btn-danger {
  background: #ffebee;
  color: #c62828;
  border: none;
}

.btn-danger:hover {
  background: #ffcdd2;
}

@media (max-width: 768px) {
  .container {
    padding: 0.5rem !important;
  }
  table {
    display: block;
    width: 100%;
    overflow-x: auto;
    white-space: nowrap;
  }
  th, td {
    padding: 0.5rem !important;
    font-size: 0.9rem;
  }
}
</style> 