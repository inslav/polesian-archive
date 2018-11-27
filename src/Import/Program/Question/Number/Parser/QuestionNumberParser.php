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

namespace App\Import\Program\Question\Number\Parser;

use App\Import\Program\Question\Number\QuestionNumber;
use App\Import\Program\Question\Number\QuestionNumberInterface;
use InvalidArgumentException;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class QuestionNumberParser implements QuestionNumberParserInterface
{
    /**
     * @param string $questionNumber
     *
     * @throws InvalidArgumentException
     *
     * @return QuestionNumberInterface
     */
    public function parseQuestionNumber(string $questionNumber): QuestionNumberInterface
    {
        $questionNumberParts = explode('.', $questionNumber);

        $partsCount = \count($questionNumberParts);

        $minimalPartsCount = 2;
        $maximalPartsCount = 3;

        if ($partsCount < $minimalPartsCount || $partsCount > $maximalPartsCount) {
            throw new InvalidArgumentException(
                sprintf('Unexpected question number "%s" parts count "%d"', $questionNumber, $partsCount)
            );
        }

        $programNumber = $questionNumberParts[0];
        $paragraphNumber = null;
        $subparagraphLetter = null;
        $isAdditional = false;

        $paragraphAndSubparagraph = $questionNumberParts[1];

        if ('доп' === $questionNumberParts[$partsCount - 1]) {
            $isAdditional = true;

            if ($minimalPartsCount === $partsCount) {
                $paragraphAndSubparagraph = null;
            }
        }

        if (null !== $paragraphAndSubparagraph) {
            if (1 === preg_match('/^(\d+)$/u', $paragraphAndSubparagraph, $matches)) {
                $paragraphNumber = (int) $matches[1];
            } elseif (1 === preg_match('/^([а-я]{1})$/u', $paragraphAndSubparagraph, $matches)) {
                $subparagraphLetter = $matches[1];
            } elseif (1 === preg_match('/^(\d+)([а-я]{1})$/u', $paragraphAndSubparagraph, $matches)) {
                $paragraphNumber = (int) $matches[1];
                $subparagraphLetter = $matches[2];
            } else {
                throw new InvalidArgumentException(
                    sprintf('Cannot parse question name and letter "%s"', $paragraphAndSubparagraph)
                );
            }
        }

        return new QuestionNumber(
            $programNumber,
            $paragraphNumber,
            $subparagraphLetter,
            $isAdditional
        );
    }
}
