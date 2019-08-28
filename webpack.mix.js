let mix = require('laravel-mix');

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
mix.js(['resources/assets/js/app.js'], 'public/js')
    .options({
        uglify: {
            uglifyOptions: {
                compress: {
                    drop_console: true
                }
            }
        }})
    .sass('resources/assets/sass/app.scss', 'public/css');

mix.js('resources/assets/js/plugins.js', 'public/js')
    .options({
    uglify: {
        uglifyOptions: {
            compress: {
                drop_console: true
            }
        }
    }});

mix.js('resources/assets/js/admin/admin_main.js', 'public/js/admin')
    .options({
        uglify: {
            uglifyOptions: {
                compress: {
                    drop_console: true
                }
            }
        }});
mix.js('resources/assets/js/user1/user1_main.js', 'public/js/user1')
    .options({
        uglify: {
            uglifyOptions: {
                compress: {
                    drop_console: true
                }
            }
        }});
mix.js('resources/assets/js/user2/user2_main.js', 'public/js/user2')
    .options({
        uglify: {
            uglifyOptions: {
                compress: {
                    drop_console: true
                }
            }
        }});
mix.js('resources/assets/js/user3/user3_main.js', 'public/js/user3')
    .options({
        uglify: {
            uglifyOptions: {
                compress: {
                    drop_console: true
                }
            }
        }});
mix.js('resources/assets/js/user4/user4_main.js', 'public/js/user4')
    .options({
        uglify: {
            uglifyOptions: {
                compress: {
                    drop_console: true
                }
            }
        }});
mix.js('resources/assets/js/user5/user5_main.js', 'public/js/user5')
    .options({
        uglify: {
            uglifyOptions: {
                compress: {
                    drop_console: true
                }
            }
        }});

mix.browserSync('industry.loc');
mix.disableNotifications();