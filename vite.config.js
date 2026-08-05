import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';
import fs from 'fs';
import { transformSync } from 'esbuild';

import crypto from 'crypto';

function processWidgetJs() {
    try {
        if (!fs.existsSync('resources/js/widget.js')) return;
        const code = fs.readFileSync('resources/js/widget.js', 'utf-8');
        const minified = transformSync(code, { minify: true, loader: 'js' });
        const hash = crypto.createHash('md5').update(minified.code).digest('hex').substring(0, 8);
        const hashedFilename = `widget.${hash}.js`;

        // Clean up previous hashed assets
        if (fs.existsSync('public')) {
            const existingFiles = fs.readdirSync('public');
            existingFiles.forEach(f => {
                if (/^widget\.[a-f0-9]{8}\.js$/.test(f) && f !== hashedFilename) {
                    try { fs.unlinkSync(`public/${f}`); } catch (e) {}
                }
            });
        }

        // Write files
        fs.writeFileSync(`public/${hashedFilename}`, minified.code);
        fs.writeFileSync('public/widget.js', minified.code);
        fs.writeFileSync('public/widget-manifest.json', JSON.stringify({
            hash: hash,
            file: hashedFilename,
            updated_at: new Date().toISOString()
        }, null, 2));

        console.log(`✓ Successfully compiled & hashed resources/js/widget.js -> public/${hashedFilename}`);
    } catch (err) {
        console.error('Error processing widget.js:', err);
    }
}

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
                processWidgetJs();
            },
            handleHotUpdate({ file }) {
                if (file.endsWith('resources/js/widget.js')) {
                    processWidgetJs();
                }
            },
            closeBundle() {
                processWidgetJs();
            }
        }
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
