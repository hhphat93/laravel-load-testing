let mix = require('laravel-mix');

require('mix-tailwindcss');


mix.js('resources/js/app.js', 'js')
    .css('resources/css/app.css', 'css')
    .setPublicPath('public')
    .vue();
