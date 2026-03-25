<template>
  <div class="tenant-home">
    <h1>Welcome to Your Store</h1>
    <div class="actions">
      <router-link to="/login" class="btn btn-primary">Login</router-link>
      <router-link to="/register" class="btn btn-secondary">Register</router-link>
    </div>
  </div>
</template>

<script>
export default {
  name: 'TenantHome'
}
</script>

<style scoped>
.tenant-home {
  text-align: center;
  padding: 2rem;
}

.actions {
  margin-top: 2rem;
}

.btn {
  margin: 0 0.5rem;
  padding: 0.5rem 1.5rem;
}

.btn-secondary {
  background-color: #6c757d;
  border-color: #6c757d;
  color: white;
}

.btn-secondary:hover {
  background-color: #5a6268;
  border-color: #545b62;
}
</style> 