const mix = require('laravel-mix')
const MomentLocalesPlugin = require('moment-locales-webpack-plugin')
const TsconfigPathsPlugin = require('tsconfig-paths-webpack-plugin')
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

mix.sass('resources/sass/app.scss', 'public/css').
  ts('resources/js/app.tsx', 'public/js').
  react().
  webpackConfig({
    cache: {
      type: 'filesystem',
    },
    plugins: [
      new MomentLocalesPlugin({
        localesToKeep: ['ru'],
      }),
    ],
    resolve: {
      plugins: [new TsconfigPathsPlugin()],
    },
    module: {
      rules: [
        {
          test: /\.(jsx|js|tsx|ts)$/,
          loader: 'eslint-loader',
          enforce: 'pre',
          exclude: /(node_modules)/,
          options: {
            formatter: require('eslint-friendly-formatter'),
          },
        },
      ],
    },
  }).
  version()
