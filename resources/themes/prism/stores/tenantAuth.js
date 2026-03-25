import { defineStore } from 'pinia';
import axiosTenant from '../api/axiosTenant';

export const useTenantAuthStore = defineStore('tenantAuth', {
  state: () => ({
    token: null,
    user: null,
  }),
  actions: {
    async login(credentials) {
      const response = await axiosTenant.post('/login', credentials);
      this.token = response.data.token;
      this.user = response.data.user;
      axiosTenant.defaults.headers.common['Authorization'] = `Bearer ${this.token}`;
    },
    logout() {
      this.token = null;
      this.user = null;
      delete axiosTenant.defaults.headers.common['Authorization'];
    },
  },
});
