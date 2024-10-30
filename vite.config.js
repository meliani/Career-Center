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
          'resources/js/app.js',
          'resources/css/filament/app/theme.css',
        ],
        refresh: true,
      }),
    ],
    base: env.VITE_APP_URL ? `${env.VITE_APP_URL}/` : '/',
  };
});
