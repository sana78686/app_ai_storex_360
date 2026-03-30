<template>
  <div class="not-found">
    <img :src="logoUrl" :alt="`${appName} logo`" class="not-found-logo" />
    <h1>404 - Page Not Found</h1>
    <p>The page you are looking for does not exist.</p>
    <router-link to="/" class="home-link">Back to home</router-link>
  </div>
</template>

<script>
import { APP_NAME, LOGO_URL } from '@central/brand'

export default {
  name: 'NotFound',
  data() {
    return {
      logoUrl: LOGO_URL,
      appName: APP_NAME
    }
  }
}
</script>

<style scoped>
.not-found {
  text-align: center;
  margin-top: 80px;
  padding: 1rem;
}

.not-found-logo {
  height: 72px;
  width: auto;
  object-fit: contain;
  margin-bottom: 1.5rem;
}

.home-link {
  display: inline-block;
  margin-top: 1rem;
  color: #4f46e5;
  font-weight: 600;
}
</style>
