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
}

#app {
  min-height: 100vh;
}

:root {
  --primary-gradient: linear-gradient(135deg, #25d366 0%, #128c7e 100%);
  --primary-gradient-hover: linear-gradient(135deg, #20c55c 0%, #0f7a70 100%);
  --primary-color: #25d366;
  --primary-color-hover: #128c7e;
  --primary-glow: rgba(37, 211, 102, 0.35);
  --secondary-color: #2c3e50;
  --text-color: #333;
  --light-bg: #f8f9fa;
}

.btn-primary {
  background-color: #128c7e;
  background-image: var(--primary-gradient);
  border-color: transparent;
  padding: 10px 25px;
  border-radius: 9999px;
  font-weight: 600;
  box-shadow: 0 8px 20px var(--primary-glow);
  transition:
    background-image 0.15s ease,
    box-shadow 0.15s ease,
    filter 0.15s ease;
}

.btn-primary:hover {
  background-image: var(--primary-gradient-hover);
  border-color: transparent;
  box-shadow: 0 10px 24px var(--primary-glow);
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
