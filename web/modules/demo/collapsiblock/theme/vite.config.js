// eslint-disable-next-line
import { defineConfig } from 'vite';
import { resolve } from 'path';

export default defineConfig(({ mode }) => {
  return {
    build: {
      manifest: true,
      lib: {
        entry: resolve(__dirname, 'src/js/collapsiblock.js'),
        name: 'Collapsiblock',
      },
      rollupOptions: {
        input: 'src/js/collapsiblock.js',
      },
    },
    css: { devSourcemap: true },
    define: {
      'process.env.NODE_ENV':
        mode === 'production' ? '"production"' : '"development"',
    },
    resolve: {
      alias: { '@': resolve(__dirname, 'src/') },
    },
    server: {
      watch: {
        usePolling: true,
      },
    },
  };
});
