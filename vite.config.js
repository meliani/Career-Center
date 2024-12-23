import { defineConfig, loadEnv } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig(({ mode }) => {
  // Load .env files
  const env = loadEnv(mode, process.cwd(), '');

  return {
    plugins: [
      laravel({
        input: [
          'resources/css/app.css',
          'resources/css/pdf.css',
          'resources/js/app.js',
          'resources/css/filament/app/theme.css',
        ],
        refresh: true,
      }),
    ],
    base: env.VITE_APP_URL ? `${env.VITE_APP_URL}/` : '/',
    build: {
      rollupOptions: {
        input: {
          pdf: 'resources/css/pdf.css',
          app: 'resources/css/app.css',
          theme: 'resources/css/filament/app/theme.css',
          js: 'resources/js/app.js',
        },
        output: {
          assetFileNames: (assetInfo) => {
            if (assetInfo.name === 'pdf.css') {
              return 'assets/pdf.css';
            }
            return 'assets/[name]-[hash][extname]';
          }
        }
      },
      // Ensure CSS is extracted to separate files
      cssCodeSplit: true,
    },
  };
});
