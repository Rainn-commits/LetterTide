import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { bunny } from 'laravel-vite-plugin/fonts';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/home.css',
                'resources/css/login.css',
                'resources/css/forgot-password.css',
                'resources/css/app-shell.css',
                'resources/css/template-surat.css',
                'resources/css/dashboard.css',
                'resources/css/profile.css',
                'resources/css/auth-base.css',
                'resources/css/pengguna.css',
                'resources/css/kop-surat.css',
                'resources/css/role.css',
                'resources/css/surat-preview.css',
                'resources/css/variables.css',
                'resources/css/surat-form.css',
                'resources/css/surat-arsip.css',
                'resources/css/surat-pilih-template.css',
                'resources/css/surat-riwayat.css',
                'resources/css/surat-edit.css',
            ],
            refresh: true,
            fonts: [
                bunny('Instrument Sans', {
                    weights: [400, 500, 600],
                }),
            ],
        }),
        tailwindcss(),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
