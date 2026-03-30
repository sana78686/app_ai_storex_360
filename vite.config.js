import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'
import vue from '@vitejs/plugin-vue'
import path from 'path'
export default defineConfig({
     plugins: [
        vue(),
        laravel({
    input: {
      /** Standalone CSS entry (e.g. blades without JS bundle) — same tokens as SPA */
      app: 'resources/css/app.css',
      central: 'resources/js/central-app/main.js',
      tenantAdmin: 'resources/js/tenant-admin/main.js',

      classic: 'resources/themes/classic/main.js',
    //   modern: 'resources/themes/modern/main.js',
    //   prism: 'resources/themes/prism/main.js',
    },
    refresh: true,
  })],
   resolve: {
    alias: {
      '@': path.resolve(__dirname, 'resources/js'),
'@css': path.resolve(__dirname, 'resources/css'),
       // apps
      '@central': path.resolve(__dirname, 'resources/js/central-app'),
      '@tenant': path.resolve(__dirname, 'resources/js/tenant-admin'),

      // themes
      '@theme-classic': path.resolve(__dirname, 'resources/themes/classic'),
      '@theme-modern': path.resolve(__dirname, 'resources/themes/modern'),
      '@theme-prism': path.resolve(__dirname, 'resources/themes/prism'),
    },
  },

    build: {
        rollupOptions: {
            output: {
                manualChunks: {
                    // split vendor libs for caching
                    vendor: ['vue', 'axios', 'lodash'],
                },
            },
        },
    },
})
