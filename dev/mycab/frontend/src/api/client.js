import axios from 'axios';

const baseURL =
  import.meta.env.VITE_API_URL || 'https://craftoweb.com/dev/mycab/api/api';

export const api = axios.create({
  baseURL,
  headers: {
    Accept: 'application/json',
    'Content-Type': 'application/json',
  },
});

export function setAuthToken(token) {
  if (token) {
    api.defaults.headers.common.Authorization = `Bearer ${token}`;
  } else {
    delete api.defaults.headers.common.Authorization;
  }
}
