/*
 * This file is part of Polesian Archive.
 *
 * Copyright (c) Institute of Slavic Studies of the Russian Academy of Sciences
 *
 * Polesian Archive is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, version 3.
 *
 * Polesian Archive is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code. If you have not received
 * a copy of the GNU General Public License along with Polesian Archive,
 * see <http://www.gnu.org/licenses/>.
 */

const Encore = require('@symfony/webpack-encore');

function getSelect2Localizations() {

    const fs = require('fs');

    const select2LocalizationFolder = './node_modules/select2/dist/js/i18n/';
    return fs
        .readdirSync(select2LocalizationFolder)
        .filter(fileName => fileName.endsWith('.js'))
        .map(fileName => select2LocalizationFolder + fileName);
}

if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    .setOutputPath('public/build/')
    .setPublicPath('/build')
    .cleanupOutputBeforeBuild()
    .disableSingleRuntimeChunk()
    .enableSourceMaps(!Encore.isProduction())
    .enableVersioning(Encore.isProduction())
    .enableSassLoader()
    .copyFiles({
        from: './assets/images',
        to: 'images/[path][name].[ext]',
    })
    .configureBabelPresetEnv((config) => {
        config.useBuiltIns = 'usage';
        config.corejs = 3;
    })
    .addEntry('js/card/list', ['./assets/js/pages/site/card/list.js', ...getSelect2Localizations()])
    .addStyleEntry('css/card/list', './assets/scss/pages/site/card/list.scss')
    .addStyleEntry('css/card/show', './assets/scss/pages/site/card/show.scss')
    .addStyleEntry('css/polesian-program/index', './assets/scss/pages/site/polesian_program/index.scss')
    .addStyleEntry('css/polesian-program/program', './assets/scss/pages/site/polesian_program/program.scss')
    .addStyleEntry('css/polesian-program/paragraph', './assets/scss/pages/site/polesian_program/paragraph.scss')
    .addStyleEntry('css/polesian-program/subparagraph', './assets/scss/pages/site/polesian_program/subparagraph.scss')
    .addStyleEntry('css/site/security/login', './assets/scss/pages/site/security/login.scss')
;

module.exports = Encore.getWebpackConfig();
