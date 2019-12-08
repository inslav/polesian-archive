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

const encore = require('@symfony/webpack-encore');

encore
    .disableSingleRuntimeChunk()
    .setOutputPath('public/build/')
    .setPublicPath('/build')
    .cleanupOutputBeforeBuild()
    .enableSourceMaps(!encore.isProduction())
    .addStyleEntry('css/base', './assets/scss/base.scss')
    .addStyleEntry('css/card/list', './assets/scss/card/list.scss')
    .addStyleEntry('css/polesian-program/index', './assets/scss/polesian_program/index.scss')
    .addStyleEntry('css/polesian-program/program', './assets/scss/polesian_program/program.scss')
    .addStyleEntry('css/polesian-program/paragraph', './assets/scss/polesian_program/paragraph.scss')
    .addStyleEntry('css/polesian-program/subparagraph', './assets/scss/polesian_program/subparagraph.scss')
    .enableSassLoader()
;

module.exports = encore.getWebpackConfig();
