import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { createRequire } from 'node:module';
const require = createRequire( import.meta.url );
import ckeditor5 from '@ckeditor/vite-plugin-ckeditor5';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
        ckeditor5( { theme: require.resolve( '@ckeditor/ckeditor5-theme-lark' ) } )
    ],
});
