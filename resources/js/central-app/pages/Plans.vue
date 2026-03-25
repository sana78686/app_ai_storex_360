<template>
  <div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold">Plans</h1>
      <button
        @click="showCreateModal = true"
        class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700"
      >
        Add Plan
      </button>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="flex justify-center items-center py-8">
      <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
    </div>

    <!-- Plans Table -->
    <div v-else class="bg-white rounded-lg shadow-md overflow-hidden">
      <table class="min-w-full">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Currency</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Interval</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created At</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-if="plans.length === 0">
            <td colspan="6" class="px-6 py-4 text-center text-gray-500">
              No plans found
            </td>
          </tr>
          <tr v-for="plan in plans" :key="plan.id">
            <td class="px-6 py-4 whitespace-nowrap">{{ plan.name }}</td>
            <td class="px-6 py-4 whitespace-nowrap">{{ plan.price }}</td>
            <td class="px-6 py-4 whitespace-nowrap">{{ plan.currency }}</td>
            <td class="px-6 py-4 whitespace-nowrap">{{ plan.interval }}</td>
            <td class="px-6 py-4 whitespace-nowrap">{{ formatDate(plan.created_at) }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
              <button @click="editPlan(plan)" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</button>
              <button @click="deletePlan(plan.id)" class="text-red-600 hover:text-red-900">Delete</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Create Plan Modal -->
    <div v-if="showCreateModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center">
      <div class="relative p-5 border w-96 shadow-lg rounded-md bg-white">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Add Plan</h3>
        <form @submit.prevent="createPlan">
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Name</label>
            <input type="text" v-model="newPlan.name"
                   class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                   required>
          </div>
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Price</label>
            <input type="number" v-model="newPlan.price"
                   class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                   min="0" required>
          </div>
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Currency</label>
            <input type="text" v-model="newPlan.currency"
                   class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                   placeholder="usd" required>
          </div>
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Interval</label>
            <select v-model="newPlan.interval"
                    class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
              <option value="month">Month</option>
              <option value="year">Year</option>
            </select>
          </div>
          <div class="mb-4">
  <label class="block text-sm font-medium text-gray-700">Features</label>
  <input type="text" v-model="newPlan.features"
         class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
         placeholder="Comma separated, e.g., Feature A, Feature B">
</div>

          <div class="flex justify-end space-x-3">
            <button type="button" @click="showCreateModal = false" class="px-4 py-2 border rounded-md bg-white text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500">Cancel</button>
            <button type="submit" :disabled="isProcessing" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
              {{ isProcessing ? 'Saving...' : 'Save' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Edit Plan Modal -->
    <div v-if="showEditModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center">
      <div class="relative p-5 border w-96 shadow-lg rounded-md bg-white">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Edit Plan</h3>
        <form @submit.prevent="updatePlan">
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Name</label>
            <input type="text" v-model="editingPlan.name"
                   class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                   required>
          </div>
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Price</label>
            <input type="number" v-model="editingPlan.price"
                   class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                   min="0" required>
          </div>
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Currency</label>
            <input type="text" v-model="editingPlan.currency"
                   class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                   required>
          </div>
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Interval</label>
            <select v-model="editingPlan.interval"
                    class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
              <option value="month">Month</option>
              <option value="year">Year</option>
            </select>
          </div>
          <div class="mb-4">
  <label class="block text-sm font-medium text-gray-700">Features</label>
  <input type="text" v-model="newPlan.features"
         class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
         placeholder="Comma separated, e.g., Feature A, Feature B">
</div>

          <div class="flex justify-end space-x-3">
            <button type="button" @click="showEditModal = false" class="px-4 py-2 border rounded-md bg-white text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500">Cancel</button>
            <button type="submit" :disabled="isProcessing" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
              {{ isProcessing ? 'Updating...' : 'Update' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue';
import axiosCentral from '@/api/axiosCentral';
import Swal from 'sweetalert2';

export default {
  name: 'Plans',
  setup() {
    const plans = ref([]);
    const loading = ref(false);
    const isProcessing = ref(false);
    const showCreateModal = ref(false);
    const showEditModal = ref(false);

    const newPlan = ref({
      name: '',
      price: 0,
      currency: 'usd',
      interval: 'month',
      features: ''
    });

    const editingPlan = ref({
      id: null,
      name: '',
      price: 0,
      currency: 'usd',
      interval: 'month',
      features: ''
    });

    const fetchPlans = async () => {
      loading.value = true;
      try {
        const res = await axiosCentral.get('plans');
        plans.value = res.data.plans || [];
      } catch (err) {
        Swal.fire('Error', err.response?.data?.message || 'Failed to fetch plans', 'error');
      } finally {
        loading.value = false;
      }
    };

    const createPlan = async () => {
      isProcessing.value = true;
      try {
          const payload = {
      ...newPlan.value,
      features: newPlan.value.features.split(',').map(f => f.trim())
          };
        await axiosCentral.post('plans', payload);
        Swal.fire('Success', 'Plan created', 'success');
        showCreateModal.value = false;
        fetchPlans();
        newPlan.value = { name: '', price: 0, currency: 'usd', interval: 'month' };
      } catch (err) {
        Swal.fire('Error', err.response?.data?.message || 'Failed to create plan', 'error');
      } finally {
        isProcessing.value = false;
      }
    };

    const editPlan = (plan) => {
      editingPlan.value = { ...plan };
      showEditModal.value = true;
    };

    const updatePlan = async () => {
      isProcessing.value = true;
      try {
        const payload = {
      ...newPlan.value,
      features: newPlan.value.features.split(',').map(f => f.trim())
          };
        await axiosCentral.put(`plans/${editingPlan.value.id}`, payload);
        Swal.fire('Success', 'Plan updated', 'success');
        showEditModal.value = false;
        fetchPlans();
      } catch (err) {
        Swal.fire('Error', err.response?.data?.message || 'Failed to update plan', 'error');
      } finally {
        isProcessing.value = false;
      }
    };

    const deletePlan = async (id) => {
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
        try {
          await axiosCentral.delete(`plans/${id}`);
          Swal.fire('Deleted!', 'Plan has been deleted.', 'success');
          fetchPlans();
        } catch (err) {
          Swal.fire('Error', err.response?.data?.message || 'Failed to delete plan', 'error');
        }
      }
    };

    const formatDate = (date) => new Date(date).toLocaleDateString();

    onMounted(fetchPlans);

    return {
      plans, loading, isProcessing,
      showCreateModal, showEditModal,
      newPlan, editingPlan,
      fetchPlans, createPlan, editPlan, updatePlan, deletePlan, formatDate
    };
  }
};
</script>

<style scoped>
/* Optional: add your custom styles here */
</style>
