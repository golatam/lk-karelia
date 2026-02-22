const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */
mix.js('resources/js/main.js', 'public/assets/js/app.js')
    .disableNotifications()
    .sass('resources/sass/main.scss', 'public/assets/css/app.css')
    .sourceMaps(false, 'source-map')
    .copy('resources/images', 'public/assets/images');

