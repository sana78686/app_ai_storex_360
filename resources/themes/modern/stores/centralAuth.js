export const useCentralAuthStore = defineStore('centralAuth', {
    state: () => ({
      token: localStorage.getItem('central_token') || null,
      user: null,
    }),
    actions: {
      async login(credentials) {
        const response = await axiosCentral.post('/login', credentials);
        this.token = response.data.token;
        this.user = response.data.user;
  
        localStorage.setItem('central_token', this.token);
        axiosCentral.defaults.headers.common['Authorization'] = `Bearer ${this.token}`;
      },
  
      logout() {
        this.token = null;
        this.user = null;
        localStorage.removeItem('central_token');
        delete axiosCentral.defaults.headers.common['Authorization'];
      },
    },
  });
  