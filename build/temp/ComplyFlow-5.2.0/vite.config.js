import { defineConfig } from 'vite';
import path from 'path';
import { fileURLToPath } from 'url';
import tailwindcss from 'tailwindcss';
import autoprefixer from 'autoprefixer';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

export default defineConfig({
    build: {
        outDir: 'assets/dist',
        emptyOutDir: true,
        rollupOptions: {
            input: {
                admin: path.resolve(__dirname, 'assets/src/js/admin.js'),
                frontend: path.resolve(__dirname, 'assets/src/js/frontend.js'),
                'consent-banner': path.resolve(__dirname, 'assets/src/js/consent-banner.js'),
                'admin-style': path.resolve(__dirname, 'assets/src/css/admin.css'),
                'admin-common': path.resolve(__dirname, 'assets/src/css/admin-common.css'),
                'frontend-style': path.resolve(__dirname, 'assets/src/css/frontend.css'),
                // Dashboard specific stylesheet (modern UI)
                'dashboard-admin': path.resolve(__dirname, 'assets/src/css/dashboard-admin.css'),
                // Dashboard behavior (charts + dark mode)
                'dashboard-admin-js': path.resolve(__dirname, 'assets/src/js/dashboard-admin.js'),
            },
            output: {
                entryFileNames: '[name].js',
                chunkFileNames: 'chunks/[name]-[hash].js',
                assetFileNames: (assetInfo) => {
                    if (assetInfo.name.endsWith('.css')) {
                        return '[name].css';
                    }
                    return 'assets/[name]-[hash][extname]';
                },
            },
        },
        sourcemap: true,
        minify: 'terser',
        terserOptions: {
            compress: {
                drop_console: false,
            },
        },
    },
    css: {
        postcss: {
            plugins: [
                tailwindcss,
                autoprefixer,
            ],
        },
    },
    resolve: {
        alias: {
            '@': path.resolve(__dirname, 'assets/src'),
        },
    },
});
