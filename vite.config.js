import { fileURLToPath, URL } from 'node:url';
import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';
import vueDevTools from 'vite-plugin-vue-devtools';

// https://vite.dev/config/
export default defineConfig(({ mode }) => {
  const isDevelopment = mode === 'development';

  return {
    base: isDevelopment ? '' : '/~s5145768/',
    server: isDevelopment ? {} : {
      proxy: {
        '/~s5145768/backend': {
          target: 'https://saw.dibris.unige.it/',
          changeOrigin: true,
          secure: true,
        },
      },
    },
    plugins: [
      vue(),
      vueDevTools(),
    ],
    resolve: {
      alias: {
        '@': fileURLToPath(new URL('./src', import.meta.url)),
      },
    },
  };
});
