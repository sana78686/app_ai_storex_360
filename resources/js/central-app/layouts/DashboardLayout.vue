<template>
  <div class="dashboard">
    <!-- Sidebar -->
    <aside class="sidebar" :class="{ 'collapsed': isSidebarCollapsed, 'show-sidebar': isSidebarVisible }">
      <div class="sidebar-header">
        <h2 class="brand">SaleTodayStore</h2>
        <button class="toggle-btn" @click="toggleSidebar">
          <i class="fas" :class="isSidebarCollapsed ? 'fa-chevron-right' : 'fa-bars'"></i>
        </button>
      </div>
      <nav class="sidebar-nav">
        <ul>
          <li>
            <router-link to="/dashboard" class="nav-link" active-class="active">
              <i class="fas fa-home"></i>
              <span>Dashboard</span>
            </router-link>
          </li>
          <li>
            <router-link to="/dashboard/tenants" class="nav-link" active-class="active">
              <i class="fas fa-store"></i>
              <span>Tenants</span>
            </router-link>
          </li>
          <li>
            <router-link to="/users" class="nav-link" active-class="active">
              <i class="fas fa-users"></i>
              <span>Users</span>
            </router-link>
          </li>
          <li>
            <router-link to="/dashboard/roles" class="nav-link" active-class="active">
              <i class="fas fa-user-tag"></i>
              <span>Roles</span>
            </router-link>
          </li>
          <li>
            <router-link to="/dashboard/permissions" class="nav-link" active-class="active">
              <i class="fas fa-key"></i>
              <span>Permissions</span>
            </router-link>
          </li>
          <li>
            <router-link to="/dashboard/plans" class="nav-link" active-class="active">
              <i class="fas fa-crown"></i>
              <span>Plans</span>
            </router-link>
          </li>
        </ul>
      </nav>
    </aside>

    <!-- Overlay for mobile when sidebar is open -->
    <div v-if="isSidebarVisible && windowWidth <= 768" class="sidebar-overlay" @click="toggleSidebar"></div>

    <!-- Main Content -->
    <main class="main">
      <header class="navbar">
        <div class="navbar-left">
          <!-- Show toggle button on small screens -->
          <button v-if="windowWidth <= 768" class="toggle-btn menu-toggle-btn" @click="toggleSidebar">
            <i class="fas fa-bars"></i>
          </button>
          <h1>{{ pageTitle }}</h1>
        </div>
        <div class="navbar-right">
          <div class="user-menu">
            <span class="user-name">{{ user.email }}</span>
            <button @click="handleLogout" class="btn btn-outline-danger">
              <i class="fas fa-sign-out-alt"></i> Logout
            </button>
          </div>
        </div>
      </header>

      <div class="content">
        <router-view></router-view>
      </div>
    </main>
  </div>
</template>

<script>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
 import axiosCentral from '@/api/axiosCentral'

export default {
  name: 'CentralDashboardLayout',
  setup() {
    const router = useRouter()
    const route = useRoute()
    const isSidebarCollapsed = ref(false)
    const isSidebarVisible = ref(true) // Default to visible on large screens
    const windowWidth = ref(window.innerWidth)

    const user = computed(() => {
      return JSON.parse(localStorage.getItem('user') || '{}')
    })

    const pageTitle = computed(() => {
      return route.meta.title || (route.name ? route.name.toString().replace(/([A-Z])/g, ' $1').replace(/^./, (str) => str.toUpperCase()) : 'Dashboard')
    })

    const toggleSidebar = () => {
      if (windowWidth.value <= 768) {
        isSidebarVisible.value = !isSidebarVisible.value
        if (isSidebarVisible.value) {
          isSidebarCollapsed.value = false
        }
      } else {
        isSidebarCollapsed.value = !isSidebarCollapsed.value
        isSidebarVisible.value = true
      }
    }

    const handleLogout = async () => {
      try {
        const central_token = localStorage.getItem('central_token')
        if (!central_token) {
          router.push('/login')
          return
        }

        await axiosCentral.post('/api/logout', {}, {
          headers: {
            'Authorization': `Bearer ${central_token}`,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
          }
        })

        localStorage.removeItem('central_token')
        localStorage.removeItem('user')
        await router.replace('/login')
      } catch (error) {
        console.error('Logout failed:', error)
        localStorage.removeItem('central_token')
        localStorage.removeItem('user')
        await router.replace('/login')
      }
    }

    onMounted(() => {
      const handleResize = () => {
        windowWidth.value = window.innerWidth
        if (window.innerWidth <= 768) {
          isSidebarVisible.value = false
          isSidebarCollapsed.value = false
        } else {
          isSidebarVisible.value = true
          isSidebarCollapsed.value = false
        }
      }

      window.addEventListener('resize', handleResize)
      handleResize() // Call on mount to set initial state
    })

    onUnmounted(() => {
      window.removeEventListener('resize', handleResize)
    })

    return {
      isSidebarCollapsed,
      isSidebarVisible,
      windowWidth,
      user,
      pageTitle,
      toggleSidebar,
      handleLogout,
    }
  }
}
</script>

<style scoped>
/* General Layout */
.dashboard {
  display: flex;
  min-height: 100vh;
  background-color: #f0f2f5;
}

.sidebar {
  width: 250px;
  min-width: 250px;
  height: 100vh;
  background-color: #2c3e50;
  color: #ecf0f1;
  padding: 1.5rem 1rem;
  box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
  transition: width 0.3s ease, min-width 0.3s ease, transform 0.3s ease;
  overflow: hidden;
  position: fixed;
  top: 0;
  left: 0;
  z-index: 1000;
}

.sidebar.collapsed {
  width: 80px;
  min-width: 80px;
  overflow: hidden;
}

.sidebar-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
  padding: 0 0.5rem;
}

.brand {
  font-size: 1.8rem;
  font-weight: bold;
  color: #fff;
  white-space: nowrap;
  opacity: 1;
  transition: opacity 0.3s ease;
}

.sidebar.collapsed .brand {
  opacity: 0;
  width: 0;
  overflow: hidden;
}

.toggle-btn {
  background: none;
  border: none;
  color: #ecf0f1;
  font-size: 1.2rem;
  cursor: pointer;
  transition: transform 0.3s ease;
}

/* Hide toggle button on small screen sidebar header */
@media (max-width: 768px) {
  .sidebar-header .toggle-btn {
    display: none;
  }
}

.sidebar.collapsed .toggle-btn {
  transform: rotate(0deg);
}

.sidebar-nav ul {
  list-style: none;
  padding: 0;
  margin: 0;
}

.sidebar-nav li {
  margin-bottom: 0.5rem;
}

.nav-link {
  display: flex;
  align-items: center;
  color: #ecf0f1;
  text-decoration: none;
  padding: 0.8rem 1rem;
  border-radius: 5px;
  transition: background-color 0.2s ease, padding 0.2s ease;
  white-space: nowrap;
  overflow: hidden;
}

.nav-link:hover,
.nav-link.active {
  background-color: rgba(255, 255, 255, 0.1);
}

.nav-link i {
  margin-right: 1rem;
  font-size: 1.2rem;
  transition: margin-right 0.3s ease;
}

.sidebar.collapsed .nav-link i {
  margin-right: 0;
  width: 100%; /* Occupy full width for centering */
  text-align: center;
}

.nav-link span {
  opacity: 1;
  transition: opacity 0.3s ease;
}

.sidebar.collapsed .nav-link span {
  opacity: 0;
  width: 0;
  overflow: hidden;
}

.main {
  margin-left: 250px;
  width: calc(100vw - 250px);
  transition: margin-left 0.3s, width 0.3s;
}

.sidebar.collapsed + .main {
  margin-left: 80px;
}

/* Overlay for mobile sidebar */
.sidebar-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5);
  z-index: 999;
  display: none; /* Hidden by default */
}

/* Navbar styles */
.navbar {
  background: white;
  padding: 1rem 2rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.navbar-left {
  display: flex;
  align-items: center;
}

.navbar-left h1 {
  margin: 0;
  font-size: 1.8rem;
  color: #333;
}

.navbar-right {
  display: flex;
  align-items: center;
}

.user-menu {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.user-name {
  font-weight: 500;
  color: #555;
}

/* Content specific styles */
.content {
  padding: 2rem;
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

.btn {
  padding: 0.5rem 1rem;
  border-radius: 5px;
  cursor: pointer;
  transition: background-color 0.2s ease;
}

.btn-outline-danger {
  background-color: transparent;
  color: #dc3545;
  border: 1px solid #dc3545;
}

.btn-outline-danger:hover {
  background-color: #dc3545;
  color: white;
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

/* Responsive adjustments */
@media (max-width: 768px) {
  .dashboard {
    flex-direction: column;
  }
  .sidebar {
    position: fixed;
    width: 200px;
    min-width: 200px;
    height: 100vh;
    z-index: 1000;
    transform: translateX(-100%);
    transition: transform 0.3s ease;
  }
  .sidebar.show-sidebar {
    transform: translateX(0);
  }
  .main {
    margin-left: 0;
    width: 100vw;
  }
  .navbar {
    flex-direction: column;
    align-items: flex-start;
  }
  .sidebar-overlay {
    display: block; /* Show overlay when sidebar is open */
  }
  .navbar-left .menu-toggle-btn {
    display: block; /* Show menu toggle button in navbar */
    margin-right: 1rem;
    color: #333; /* Adjust color for navbar */
  }
  .sidebar-header .toggle-btn {
    display: block; /* Show the internal toggle button to close the sidebar on mobile */
    transform: rotate(0deg); /* Reset rotation */
  }
  .sidebar-header .brand {
    opacity: 1;
    width: auto;
  }
  .sidebar.collapsed .brand {
    opacity: 1;
    width: auto;
  }
  .nav-link span {
    opacity: 1;
    width: auto;
  }
  .sidebar.collapsed .nav-link span {
    opacity: 1;
    width: auto;
  }
  .stats-grid {
    grid-template-columns: 1fr; /* Stack stats vertically */
  }
  .recent-tenants .table-responsive {
    overflow-x: auto; /* Enable horizontal scrolling for tables */
  }
}

body {
  overflow-x: hidden;
}
</style>
