import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react'

// For subdirectory deploy set VITE_BASE_URL=/your-folder/ in .env.production
const base = process.env.VITE_BASE_URL || '/'

export default defineConfig({
  base,
  plugins: [react()],
  server: {
    port: 5173,
    host: true,
  },
  build: {
    outDir: 'dist',
    assetsDir: 'assets',
    sourcemap: false,
    rollupOptions: {
      output: {
        manualChunks: {
          vendor: ['react', 'react-dom', 'react-router-dom'],
          motion: ['framer-motion'],
        },
      },
    },
  },
})
