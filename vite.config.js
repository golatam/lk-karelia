import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import { copyFileSync, mkdirSync, readdirSync } from 'fs';
import { resolve } from 'path';

// Copy Font Awesome webfonts to build output
function copyFontAwesome() {
    return {
        name: 'copy-fontawesome',
        writeBundle() {
            const src = resolve('node_modules/@fortawesome/fontawesome-free/webfonts');
            const dest = resolve('public/build/webfonts');
            mkdirSync(dest, { recursive: true });
            for (const file of readdirSync(src)) {
                copyFileSync(resolve(src, file), resolve(dest, file));
            }
        },
    };
}

export default defineConfig({
    plugins: [
        laravel({
            input: [
                // Legacy: existing jQuery + SCSS (replaces webpack.mix.js)
                'resources/js/main.js',
                'resources/sass/main.scss',
                // New: Vue 3 + Inertia + Tailwind
                'resources/js/app.js',
                'resources/css/app.css',
            ],
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
        copyFontAwesome(),
    ],
    resolve: {
        alias: {
            '@': '/resources/js',
        },
    },
    css: {
        preprocessorOptions: {
            scss: {
                // Suppress legacy @import deprecation warnings from existing SCSS
                silenceDeprecations: ['import', 'global-builtin', 'slash-div'],
            },
        },
    },
    // jQuery global exposure for legacy code
    build: {
        rollupOptions: {
            output: {
                // Keep legacy and new bundles separate
                manualChunks(id) {
                    if (id.includes('node_modules/vue') || id.includes('node_modules/@vue') || id.includes('node_modules/@inertiajs')) {
                        return 'vendor-vue';
                    }
                    if (id.includes('node_modules/jquery') || id.includes('node_modules/select2') || id.includes('node_modules/jquery-ui')) {
                        return 'vendor-jquery';
                    }
                },
            },
        },
    },
});
