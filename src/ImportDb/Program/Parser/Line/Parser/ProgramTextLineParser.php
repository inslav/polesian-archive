<?php

declare(strict_types=1);

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

namespace App\ImportDb\Program\Parser\Line\Parser;

use App\ImportDb\Program\Parser\Line\Line\Paragraph\ParagraphLine;
use App\ImportDb\Program\Parser\Line\Line\Program\ProgramLine;
use App\ImportDb\Program\Parser\Line\Line\ProgramTextLineInterface;
use App\ImportDb\Program\Parser\Line\Line\Section\SectionLine;
use App\ImportDb\Program\Parser\Line\Line\Subparagraph\SubparagraphLine;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class ProgramTextLineParser implements ProgramTextLineParserInterface
{
    public function parseProgramTextLine(string $programTextLine): ProgramTextLineInterface
    {
        if (1 === preg_match('/^(\d+)\. (Запишите .+)$/u', $programTextLine, $matches)) {
            return new ParagraphLine((int) $matches[1], null, $matches[2]);
        }

        if (1 === preg_match('/^(\d+)\. (.+?)(?:\. ([А-Я].+))$/u', $programTextLine, $matches)) {
            return new ParagraphLine((int) $matches[1], $matches[2], $matches[3]);
        }

        if (1 === preg_match('/^(\d+)\. (.+[^\.]|.+\? .+)$/', $programTextLine, $matches)) {
            return new ParagraphLine((int) $matches[1], null, $matches[2]);
        }

        if (1 === preg_match('/^(\d+)\. (.+?)\.?$/', $programTextLine, $matches)) {
            return new ParagraphLine((int) $matches[1], $matches[2], null);
        }

        if (1 === preg_match('/^([а-я]{1})\) (.+)$/u', $programTextLine, $matches)) {
            return new SubparagraphLine($matches[1], $matches[2]);
        }

        $romanNumeralsRegex = '(M{0,4}(?:CM|CD|D?C{0,3})(?:XC|XL|L?X{0,3})(?:IX|IV|V?I{0,3}))';
        if (1 === preg_match(sprintf('/^%s\. (.+)$/', $romanNumeralsRegex), $programTextLine, $matches)) {
            return new ProgramLine($matches[1], $matches[2]);
        }

        return new SectionLine($programTextLine);
    }
}
