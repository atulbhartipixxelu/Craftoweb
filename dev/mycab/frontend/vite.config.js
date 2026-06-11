import { defineConfig, loadEnv } from 'vite';
import react from '@vitejs/plugin-react';

export default defineConfig(({ mode }) => {
  const env = loadEnv(mode, import.meta.dirname, '');
  const apiUrl =
    env.VITE_API_URL || 'https://craftoweb.com/dev/mycab/api/api';

  return {
    plugins: [react()],
    base: '/dev/mycab/',
    build: {
      outDir: 'dist',
      emptyOutDir: true,
    },
    define: {
      'import.meta.env.VITE_API_URL': JSON.stringify(apiUrl),
    },
    server: {
      port: 5175,
      host: true,
      proxy: {
        '/dev/mycab/api': {
          target: 'http://127.0.0.1:8000',
          changeOrigin: true,
          secure: false,
          rewrite: (p) => p.replace(/^\/dev\/mycab\/api/, ''),
        },
      },
    },
  };
});
