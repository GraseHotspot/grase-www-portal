// webpack.config.js
var Encore = require('@symfony/webpack-encore');

Encore.autoProvideVariables({
    'bazinga-translator': 'Translator'
})

Encore
    // directory where compiled assets will be stored
    .setOutputPath('public/build/')

    // the public path used by the web server to access the previous directory
    .setPublicPath(Encore.isProduction() ? '/grase/build' : '/build')
    .setManifestKeyPrefix('build')

    // will create public/build/app.js and public/build/app.css
    .addEntry('app', './assets/js/app.js')

    // will create public/build/uam.js and public/build/uam.css
    .addEntry('uam', './assets/js/uam.js')

    // When enabled, Webpack "splits" your files into smaller pieces for greater optimization.
    .splitEntryChunks()

    // will require an extra script tag for runtime.js
    // but, you probably want this, unless you're building a single-page app
    .enableSingleRuntimeChunk()

    // allow legacy applications to use $/jQuery as a global variable
    .autoProvidejQuery()

    // enable source maps during development
    .enableSourceMaps(!Encore.isProduction())

    // empty the outputPath dir before each build
    .cleanupOutputBeforeBuild()

    // show OS notifications when builds finish/fail
    .enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction())
    // enables hashed filenames (e.g. app.abc123.css)
    .enableVersioning(Encore.isProduction())

    // enables @babel/preset-env polyfills
    .configureBabelPresetEnv((config) => {
        config.useBuiltIns = 'usage';
        config.corejs = 3;
    })

    // allow sass/scss files to be processed
    .enableSassLoader()

    // Enable React processing
    //.enableReactPreset()

    .copyFiles({
        from: './assets/images',
        // optional target path, relative to the output dir
        //to: 'images/[path][name].[ext]',

        // if versioning is enabled, add the file hash too
        to: 'images/[path][name].[hash:8].[ext]',

        // only copy files matching this pattern
        pattern: /\.(png|jpg|jpeg|ico|svg)$/
    })
;

// export the final configuration
module.exports = Encore.getWebpackConfig();
