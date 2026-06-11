import axios from 'axios';

const TOKEN_KEY = 'dashboard-auth-token';

const api = axios.create({
  baseURL: import.meta.env.VITE_API_URL || '/api',
  headers: {
    Accept: 'application/json',
    'Content-Type': 'application/json',
  },
  timeout: 30000,
});

api.interceptors.request.use((config) => {
  const token = localStorage.getItem(TOKEN_KEY);
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

api.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401) {
      localStorage.removeItem(TOKEN_KEY);
      if (window.location.pathname !== '/login') {
        window.location.href = '/login';
      }
    }
    return Promise.reject(error);
  }
);

export const authApi = {
  login: (email, password) =>
    api.post('/login', { email, password }).then((r) => r.data.data),
  logout: () => api.post('/logout').then((r) => r.data),
  me: () => api.get('/me').then((r) => r.data.data),
};

export const projectsApi = {
  getAll: (params) => api.get('/projects', { params }).then((r) => r.data.data),
  create: (data) => api.post('/projects', data).then((r) => r.data.data),
  update: (id, data) => api.put(`/projects/${id}`, data).then((r) => r.data.data),
  delete: (id) => api.delete(`/projects/${id}`),
  getStats: () => api.get('/dashboard/stats').then((r) => r.data.data),
};

export const dailyUpdatesApi = {
  getAll: (params) => api.get('/daily-updates', { params }).then((r) => r.data.data),
  create: (data) => api.post('/daily-updates', data).then((r) => r.data.data),
  delete: (id) => api.delete(`/daily-updates/${id}`),
};

export const usersApi = {
  getAll: () => api.get('/users').then((r) => r.data.data),
  getById: (id) => api.get(`/users/${id}`).then((r) => r.data.data),
  create: (data) => api.post('/users', data).then((r) => r.data.data),
  update: (id, data) => api.put(`/users/${id}`, data).then((r) => r.data.data),
  delete: (id) => api.delete(`/users/${id}`),
};
export const contactsApi = {
  getAll: (params) =>
    api.get('/contacts', { params }).then((r) => r.data.data),
  markRead: (id) =>
    api.patch(`/contacts/${id}/read`).then((r) => r.data.data),
  delete: (id) => api.delete(`/contacts/${id}`),
};

export const clientsApi = {
  getAll: () => api.get('/clients').then((r) => r.data.data),
  getById: (id) => api.get(`/clients/${id}`).then((r) => r.data.data),
  create: (data) => api.post('/clients', data).then((r) => r.data.data),
  update: (id, data) => api.put(`/clients/${id}`, data).then((r) => r.data.data),
  delete: (id) => api.delete(`/clients/${id}`),
};

export const mockupsApi = {
  getAll: () => api.get('/mockups').then((r) => r.data.data),
  getByProject: (projectId) =>
    api.get(`/projects/${projectId}/mockups`).then((r) => r.data.data),
  create: (data) => api.post('/mockups', data).then((r) => r.data.data),
  delete: (id) => api.delete(`/mockups/${id}`),
};

export { TOKEN_KEY };
export default api;
