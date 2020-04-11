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

import $ from 'jquery';
import 'select2';

$(document).ready(() => {

    $('.vyfony-filterable-table-bundle-form-group select[multiple="multiple"]').select2({
        language: $('html').prop('lang'),
        matcher: function (query, element) {

            return undefined !== query.term
                ? termMatches(query.term, element.text) ? element : null
                : element;

            // todo move match logic to separate class
            function termMatches(term, element) {

                const trimmedTerm = term.trim();

                if ('' === trimmedTerm) {
                    return true;
                }

                const loweredTermWithoutAccents = trimmedTerm.replace('́', '').toLowerCase();

                if ('' === loweredTermWithoutAccents) {
                    return true;
                }

                const loweredElementWithoutAccents = element.replace('́', '').toLowerCase();

                return loweredElementWithoutAccents.indexOf(loweredTermWithoutAccents) > -1;
            }
        }
    });

    $('.pa-loading-container')
        .css("margin-left", "-8px")
        .addClass('loaded');
});