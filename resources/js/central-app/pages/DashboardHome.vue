<template>
  <div class="dashboard-home-content">
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-icon">
          <i class="fas fa-store"></i>
        </div>
        <div class="stat-info">
          <h3>Total Tenants</h3>
          <p>{{ stats.tenants }}</p>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon">
          <i class="fas fa-users"></i>
        </div>
        <div class="stat-info">
          <h3>Total Users</h3>
          <p>{{ stats.users }}</p>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon">
          <i class="fas fa-shopping-cart"></i>
        </div>
        <div class="stat-info">
          <h3>Active Stores</h3>
          <p>{{ stats.activeStores }}</p>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon">
          <i class="fas fa-clock"></i>
        </div>
        <div class="stat-info">
          <h3>Trial Stores</h3>
          <p>{{ stats.trialStores }}</p>
        </div>
      </div>
    </div>

    <div class="recent-tenants">
      <h2>Recent Tenants</h2>
      <div class="table-responsive">
        <table class="table">
          <thead>
            <tr>
              <th>Store Name</th>
              <th>Domain</th>
              <th>Status</th>
              <th>Created At</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="recentTenants.length === 0">
              <td colspan="5" class="text-center">No recent tenants found.</td>
            </tr>
            <tr v-for="tenant in recentTenants" :key="tenant.id">
              <td>{{ tenant.store_name }}</td>
              <td>{{ tenant.domain }}</td>
              <td>
                <span :class="['badge', tenant.status === 'active' ? 'badge-success' : 'badge-warning']">
                  {{ tenant.status }}
                </span>
              </td>
              <td>{{ formatDate(tenant.created_at) }}</td>
              <td>
                <button class="btn btn-sm btn-info" @click="viewTenant(tenant)">
                  <i class="fas fa-eye"></i>
                </button>
                <button class="btn btn-sm btn-warning" @click="editTenant(tenant)">
                  <i class="fas fa-edit"></i>
                </button>
                <button class="btn btn-sm btn-danger" @click="deleteTenant(tenant)">
                  <i class="fas fa-trash"></i>
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue';
import axiosCentral from '@/api/axiosCentral';  // Fixed import path
import { useRouter } from 'vue-router';

export default {
  name: 'DashboardHome',
  setup() {
    const router = useRouter();
    const stats = ref({
      tenants: 0,
      users: 0,
      activeStores: 0,
      trialStores: 0,
    });
    const recentTenants = ref([]);

    const formatDate = (date) => {
      return new Date(date).toLocaleDateString();
    };

    const fetchStats = async () => {
      try {
        const response = await axiosCentral.get('/dashboard/stats');
        stats.value = response.data;
      } catch (error) {
        console.error('Failed to fetch stats:', error);
      }
    };

    const fetchRecentTenants = async () => {
      try {
        console.log('Fetching recent tenants...');
        const response = await axiosCentral.get('/tenants');
        console.log('API Response:', response.data);
        
        if (!response.data.tenants) {
          console.error('No tenants array in response:', response.data);
          return;
        }

        // Get the tenants array from the response and sort by created_at
        recentTenants.value = response.data.tenants
          .sort((a, b) => new Date(b.created_at) - new Date(a.created_at))
          .slice(0, 10); // Get only the 10 most recent tenants
        
        console.log('Processed tenants:', recentTenants.value);
      } catch (error) {
        console.error('Failed to fetch recent tenants:', error);
        if (error.response) {
          console.error('Error response:', error.response.data);
          console.error('Error status:', error.response.status);
        }
      }
    };

    const viewTenant = (tenant) => {
      router.push(`/tenants/${tenant.id}`);
    };

    const editTenant = (tenant) => {
      router.push(`/tenants/${tenant.id}/edit`);
    };

    const deleteTenant = async (tenant) => {
      if (confirm('Are you sure you want to delete this tenant?')) {
        try {
          await axiosCentral.delete(`/tenants/${tenant.id}`);
          await fetchRecentTenants();
          await fetchStats();
        } catch (error) {
          console.error('Failed to delete tenant:', error);
        }
      }
    };

    onMounted(() => {
      fetchStats();
      fetchRecentTenants();
    });

    return {
      stats,
      recentTenants,
      formatDate,
      viewTenant,
      editTenant,
      deleteTenant,
    };
  },
};
</script>

<style scoped>
/* Reuse styles from CentralDashboard.vue for consistency */
.dashboard-home-content {
  /* Add any specific padding/margin if needed, but CentralDashboard's main handles most */
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.stat-card {
  background: white;
  padding: 1.5rem;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  display: flex;
  align-items: center;
}

.stat-icon {
  width: 48px;
  height: 48px;
  background: #e3f2fd;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-right: 1rem;
}

.stat-icon i {
  font-size: 1.5rem;
  color: #1976d2;
}

.stat-info h3 {
  margin: 0;
  font-size: 0.9rem;
  color: #666;
}

.stat-info p {
  margin: 0.5rem 0 0;
  font-size: 1.5rem;
  font-weight: bold;
  color: #333;
}

.recent-tenants {
  background: white;
  padding: 1.5rem;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.recent-tenants h2 {
  margin-top: 0;
  margin-bottom: 1.5rem;
  color: #333;
}

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

.badge {
  padding: 0.25rem 0.5rem;
  border-radius: 4px;
  font-size: 0.875rem;
}

.badge-success {
  background: #e8f5e9;
  color: #2e7d32;
}

.badge-warning {
  background: #fff3e0;
  color: #ef6c00;
}

.btn-sm {
  padding: 0.25rem 0.5rem;
  font-size: 0.875rem;
  margin-right: 0.25rem;
}

.btn-info {
  background: #e3f2fd;
  color: #1976d2;
  border: none;
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

/* Responsive adjustments (apply consistent media queries) */
@media (max-width: 768px) {
  .stats-grid {
    grid-template-columns: 1fr; /* Stack stats vertically */
  }
}
</style> 