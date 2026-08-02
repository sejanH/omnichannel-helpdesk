import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';
import fs from 'fs';
import { transformSync } from 'esbuild';

export default defineConfig({
    css: {
        preprocessorOptions: {
            scss: {
                api: 'modern-compiler',
                silenceDeprecations: ['import'],
            },
        },
    },
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
        {
            name: 'minify-widget-js',
            buildStart() {
                this.addWatchFile('resources/js/widget.js');
            },
            handleHotUpdate({ file }) {
                if (file.endsWith('resources/js/widget.js')) {
                    try {
                        const code = fs.readFileSync('resources/js/widget.js', 'utf-8');
                        const minified = transformSync(code, { minify: true, loader: 'js' });
                        fs.writeFileSync('public/widget.js', minified.code);
                        console.log('✓ [HMR] Re-compiled & minified resources/js/widget.js -> public/widget.js');
                    } catch (err) {
                        console.error('Error minifying widget.js on HMR:', err);
                    }
                }
            },
            closeBundle() {
                try {
                    if (fs.existsSync('resources/js/widget.js')) {
                        const code = fs.readFileSync('resources/js/widget.js', 'utf-8');
                        const minified = transformSync(code, { minify: true, loader: 'js' });
                        fs.writeFileSync('public/widget.js', minified.code);
                        console.log('✓ [Build] Successfully compiled & minified resources/js/widget.js -> public/widget.js');
                    }
                } catch (err) {
                    console.error('Error minifying widget.js on build:', err);
                }
            }
        }
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
