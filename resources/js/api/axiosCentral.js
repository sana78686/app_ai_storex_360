import axios from 'axios';
const axiosCentral = axios.create({
  baseURL: '/api/v1/central',
  headers: {
    Accept: 'application/json',
    'Content-Type': 'application/json',
  }
});
axiosCentral.interceptors.request.use(
  config => {
    const token = localStorage.getItem('central_token');
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
  },
  error => Promise.reject(error)
);
axiosCentral.interceptors.response.use(
  response => response,
  error => {
    if (error.response?.status === 401 && !window.location.pathname.includes('/signup')) {
      localStorage.removeItem('central_token');
      localStorage.removeItem('central_user');
    //   window.location.href = '/login';
    }
    return Promise.reject(error);
  }
);
export default axiosCentral;
