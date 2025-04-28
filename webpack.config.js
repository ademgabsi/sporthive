const Encore = require('@symfony/webpack-encore');

Encore
    // Configuration de base
    .setOutputPath('public/build/')
    .setPublicPath('/build')
    .enableSingleRuntimeChunk()
    .cleanupOutputBeforeBuild()
    .enableSourceMaps(!Encore.isProduction())

    // Entrées JavaScript
    .addEntry('map', './assets/js/map.js')
    
    // Configuration CSS/SCSS
    .enableSassLoader()
    .enablePostCssLoader()

    // Copier les assets de Leaflet
    .copyFiles({
        from: './node_modules/leaflet/dist/images',
        to: 'images/[path][name].[ext]',
        pattern: /\.(png|svg)$/
    })

    // Configuration des assets
    .configureLoaderRule('images', (loader) => {
        loader.test = /\.(png|svg|jpg|jpeg|gif)$/i;
        loader.type = 'asset/resource';
      })

module.exports = Encore.getWebpackConfig();