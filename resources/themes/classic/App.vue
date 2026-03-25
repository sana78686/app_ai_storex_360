<template>
  <div id="app">
    <!-- <router-view></router-view> -->
    <router-view />
  </div>
</template>

<script>
import { computed } from 'vue';
import { useRoute } from 'vue-router';
import { useI18n } from 'vue-i18n';

export default {
  name: 'App',
  setup() {
    useI18n();
    const route = useRoute();

    const isAuthPage = computed(() => {
      // Hide frontend navbar for auth pages and admin pages
      return route.path.includes('/login') ||
             route.path.includes('/register') ||
             route.path.startsWith('/dashboard');
    });

    return {
      isAuthPage
    };
  }
};
</script>

<style>
@import 'bootstrap/dist/css/bootstrap.min.css';
@import '@fortawesome/fontawesome-free/css/all.min.css';

body {
  margin: 0;
  font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
}

#app {
  min-height: 100vh;
}

:root {
  --primary-color: #ff4c3b;
  --secondary-color: #2c3e50;
  --text-color: #333;
  --light-bg: #f8f9fa;
}

.btn-primary {
  background-color: var(--primary-color);
  border-color: var(--primary-color);
  padding: 10px 25px;
  border-radius: 5px;
  font-weight: 500;
}

.btn-primary:hover {
  background-color: #e6392a;
  border-color: #e6392a;
}
.pt-22 {
    padding-top: 5rem;
}

/* Remove underlines from anchor tags */
a {
  text-decoration: none;
}

a:hover {
  text-decoration: none;
}

a:focus {
  text-decoration: none;
}

a:visited {
  text-decoration: none;
}
</style>
