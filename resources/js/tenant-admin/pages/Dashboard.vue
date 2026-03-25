<template>
  <div class="dashboard-layout">
    <aside :class="['sidebar', { collapsed }]">
      <div class="sidebar-header">
        <span v-if="!collapsed" class="sidebar-brand">Your Name</span>
        <button @click="toggleSidebar" class="toggle-btn">
          <i :class="collapsed ? 'fas fa-chevron-right' : 'fas fa-chevron-left'"></i>
        </button>
      </div>
       <!-- Professional Navigation Bar -->
      <nav>
        <router-link
          v-for="item in menu"
          :key="item.path"
          :to="item.path"
          class="sidebar-link"
          :class="{ active: $route.path === item.path }"
        >
          <i :class="item.icon" style="color: var(--main-green); margin-right: 1rem;"></i>
          <span v-if="!collapsed">{{ item.label }}</span>

        </router-link>

        <!-- Category link that toggles the component -->
        <!-- <a

          class="sidebar-link"

          style="cursor: pointer;"
        > -->
          <!-- <i class="fas fa-tags" style="color: var(--main-green); margin-right: 1rem;"></i> -->
          <!-- <span v-if="!collapsed">Category</span> -->
        <!-- </a> -->
      </nav>
    </aside>
    <div class="main-content">
      <header class="navbar">
        <div class="navbar-left">
          <!-- <img src="/logo.png" alt="Logo" class="navbar-logo" /> -->
          <span class="navbar-branding">YourBrand</span>
        </div>
        <div class="navbar-center">
          <input type="text" class="search-bar" placeholder="Search..." v-model="searchQuery" />
        </div>
        <div class="navbar-right">
          <button class="icon-btn">
            <i class="fas fa-bell"></i>
          </button>
          <div class="profile-dropdown" @mousedown.stop="toggleDropdown">

        <div v-if="user.email" class="avatar-placeholder">
  {{ user.email.charAt(0).toUpperCase() }}
</div>

<img v-else :src="user.avatar" class="avatar" @error="onImageError" />
            <!-- <i class="fas fa-chevron-down"></i> -->
            <div v-if="dropdownOpen" class="dropdown-menu d-block bg-white">
                 <span class="user-name text-dark">{{ user.email }}</span>
              <button class="text-dark"  @click="handleLogout">Logout</button>
            </div>
          </div>
        </div>
      </header>
      <main>
        <div class="dashboard-widgets">
          <div class="stat-card">
            <div class="stat-icon stat-sales"><i class="fas fa-shopping-cart"></i></div>
            <div class="stat-info">
              <div class="stat-title">Total Sales</div>
              <div class="stat-value">2.4586</div>
              <div class="stat-progress"><div class="progress-bar" style="width: 70%"></div></div>
            </div>
          </div>
          <div class="stat-card">
            <div class="stat-icon stat-growth"><i class="fas fa-chart-line"></i></div>
            <div class="stat-info">
              <div class="stat-title">Growth Rate</div>
              <div class="stat-value">45%</div>
              <div class="stat-progress"><div class="progress-bar" style="width: 45%"></div></div>
            </div>
          </div>
          <div class="stat-card">
            <div class="stat-icon stat-revenue"><i class="fas fa-dollar-sign"></i></div>
            <div class="stat-info">
              <div class="stat-title">Revenue</div>
              <div class="stat-value">$2,453</div>
              <div class="stat-progress"><div class="progress-bar" style="width: 60%"></div></div>
            </div>
          </div>
        </div>

        <!-- Show Category component when showCategory is true -->
        <!-- <Category v-if="showCategory" />
        <Users v-if="showUsers" /> -->

        <router-view />
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount, computed } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import axiosTenant from '@/api/axiosTenant'
import Category from './Category.vue'
import Users from './Users.vue'

const router = useRouter()
const user = ref({ email: '' })
const collapsed = ref(false)
const toggleSidebar = () => (collapsed.value = !collapsed.value)
const searchQuery = ref('')
const dropdownOpen = ref(false)
const toggleDropdown = () => (dropdownOpen.value = !dropdownOpen.value)
const showCategory = ref(false) // Add this to control Category visibility
const showUsers = ref(false) // Add this to control Category visibility

const handleLogout = async () => {
  try {
    await axiosTenant.post('/logout')
    localStorage.removeItem('tenant_token')
    localStorage.removeItem('tenant_user')
    router.push('/login')
  } catch (error) {
    localStorage.removeItem('tenant_token')
    localStorage.removeItem('tenant_user')
    router.push('/login')
  }
}


// Close dropdown on outside click
const handleClickOutside = (event) => {
  setTimeout(() => {
  if (!event.target.closest('.profile-dropdown')) {
    dropdownOpen.value = false
  }
  }, 0)
}
onMounted(() => {
  const storedUser = localStorage.getItem('tenant_user')
  if (storedUser) {
    user.value = JSON.parse(storedUser)
  }
  document.addEventListener('mousedown', handleClickOutside)
})
onBeforeUnmount(() => {
  document.removeEventListener('mousedown', handleClickOutside)
})

const menu = [
  { path: '/dashboard', icon: 'fas fa-tachometer-alt', label: 'Dashboard' },
  { path: '/orders', icon: 'fas fa-shopping-cart', label: 'Orders' },
  { path: '/dashboard/products', icon: 'fas fa-box', label: 'Products' },
  { path: '/dashboard/products/create', icon: 'fas fa-plus', label: 'Add Product' },
  { path: '/customers', icon: 'fas fa-users', label: 'Customers' },
  { path: '/dashboard/users', icon: 'fas fa-user', label: 'Users' },
  { path: '/dashboard/category', icon: 'fas fa-tags', label: 'Category' },
  { path: '/dashboard/permissions', icon: 'fas fa-key', label: 'Permissions' },
  { path: '/dashboard/roles', icon: 'fas fa-user-shield', label: 'Roles' },
  { path: '/dashboard/attributes', icon: 'fas fa-list', label: 'Attributes' },
  { path: '/dashboard/settings', icon: 'fas fa-cog', label: 'Settings' },
]
</script>

<style scoped>
:root {
  --border-green: #2CA37C;
  --sidebar-bg: #2B2B2B;
  --main-bg: #FFFFFF;
  --light-gray: #EAEAEA;
  --main-green: #00B894;
  --chart-green: #27AE60;
  --dark-text: #333333;
  --light-text: #888888;
}
.dashboard-layout {
  display: flex;
  min-height: 100vh;
  border-top: 30px solid #2CA37C;
}
.avatar-placeholder {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: #6b7280; /* gray */
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 600;
  text-transform: uppercase;
}

.sidebar {
  background: #2B2B2B;
  color: var(--light-text);
  min-height: 100vh;
  width: 220px;
  display: flex;
  flex-direction: column;
  transition: width 0.2s;
  border-left: 30px solid #2CA37C;
}
.sidebar.collapsed {
  width: 100px;
}
.sidebar-header {
  display: flex;
  align-items: center;
  padding: 1rem;
  justify-content: space-between;
}
.toggle-btn {
  background: none;
  border: none;
  color: #fff;
  cursor: pointer;
  font-size: 1.2rem;
}
.sidebar-link {
  display: flex;
  align-items: center;
  /* padding: 0px; */
  color: #fff;
  text-decoration: none;
  border-left: 4px solid transparent;
}
.sidebar-link.active, .sidebar-link:hover {
  background: var(--border-green);
  color: #2CA37C;
  border-left: 4px solid var(--main-green);
}
.sidebar-link i {
  font-size: 1.2rem;
}
.sidebar.collapsed .sidebar-link span {
  display: none;
}
.sidebar-brand {
  background: #2B2B2B;
  color: #fff;
  font-weight: bold;
  font-size: 1.5rem;
  padding: 1.5rem 1rem;
  border-bottom: 1px solid var(--light-gray);
}
.main-content {
  background: var(--main-bg);
  color: var(--dark-text);
  flex: 1;
  display: flex;
  flex-direction: column;
}
.navbar {
  background: #2B2B2B;
  color: #fff;
  padding: 0 2rem;
  display: flex;
  align-items: center;
  border-bottom: 1px solid #2CA37C;
  height: 64px;
  justify-content: space-between;
  position: relative;
}
.navbar-left {
  display: flex;
  align-items: center;
}
.navbar-logo {
  height: 36px;
  margin-right: 1rem;
}
.navbar-branding {
  font-weight: bold;
  font-size: 1.3rem;
  color: #2CA37C;
}
.navbar-center {
  flex: 1;
  display: flex;
  justify-content: center;
}
.search-bar {
  width: 300px;
  padding: 0.5rem 1rem;
  border-radius: 20px;
  border: none;
  background: #222;
  color: #fff;
}
.navbar-right {
  display: flex;
  align-items: center;
  gap: 1.5rem;
}
.icon-btn {
  background: none;
  border: none;
  color: #fff;
  font-size: 1.3rem;
  cursor: pointer;
  margin-right: 1rem;
}
.profile-dropdown {
  display: flex;
  align-items: center;
  cursor: pointer;
  position: relative;
}
.avatar {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  margin-right: 0.5rem;
}
.user-name {
  margin-right: 0.5rem;
}
.dropdown-menu {
  position: absolute;
  right: 0;
  top: 110%;
  background: #2ae911;
  color: #e63f3f;
  border-radius: 6px;
  box-shadow: 0 2px 8px rgba(15, 15, 15, 0.15);
  min-width: 120px;
  z-index: 10;
  padding: 0.5rem 0;
}
.dropdown-menu button {
  background: none;
  border: none;
  color: #d81818;
  width: 100%;
  text-align: left;
  padding: 0.5rem 1rem;
  cursor: pointer;
}
.dropdown-menu button:hover {
  background: #f4f6f9;
}
.user-email {
  color: #fff;
  font-weight: 500;
}
main {
  flex: 1;
  padding: 2rem;
  background: var(--main-bg);
}
.dashboard-widgets {
  display: flex;
  gap: 2rem;
  margin-bottom: 2rem;
  flex-wrap: wrap;
}
.stat-card {
  background: #fff;
  border-radius: 10px;
  box-shadow: 0 2px 8px rgba(44, 163, 124, 0.08);
  display: flex;
  align-items: center;
  padding: 1.5rem 2rem;
  min-width: 220px;
  flex: 1 1 220px;
  max-width: 320px;
}
.stat-icon {
  font-size: 2.2rem;
  margin-right: 1.2rem;
  display: flex;
  align-items: center;
  justify-content: center;
  width: 48px;
  height: 48px;
  border-radius: 50%;
  background: #EAEAEA;
}
.stat-sales { color: #00B894; }
.stat-growth { color: #27AE60; }
.stat-revenue { color: #2CA37C; }
.stat-title {
  font-size: 1rem;
  color: #888888;
  margin-bottom: 0.2rem;
}
.stat-value {
  font-size: 1.5rem;
  font-weight: bold;
  color: #333333;
}
.stat-progress {
  margin-top: 0.5rem;
  background: #EAEAEA;
  border-radius: 4px;
  height: 6px;
  width: 100%;
  overflow: hidden;
}
.progress-bar {
  height: 100%;
  background: linear-gradient(90deg, #00B894 0%, #2CA37C 100%);
  border-radius: 4px;
}
@media (max-width: 900px) {
  .dashboard-layout {
    flex-direction: column;
  }
  .sidebar {
    width: 100%;
    flex-direction: row;
    justify-content: space-between;
    align-items: center;
    padding: 0.5rem;
  }
  .sidebar nav {
    flex-direction: row;
    gap: 0.5rem;
    padding: 0;
  }
  .main-content {
    padding: 0;
  }
}
@media (max-width: 600px) {
  .sidebar {
    flex-direction: column;
    width: 100%;
    padding: 0.5rem 0;
  }
  .sidebar nav {
    flex-direction: column;
    gap: 0.5rem;
  }
  main {
    padding: 1rem;
  }
}
</style>
