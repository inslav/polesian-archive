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

namespace App\Import\Program\Question\Number\Formatter;

use App\Converter\QuestionToQuestionNumberConverterInterface;
use App\Entity\Question;
use App\Import\Program\Question\Number\QuestionNumberInterface;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class QuestionNumberFormatter implements QuestionNumberFormatterInterface
{
    /**
     * @var QuestionToQuestionNumberConverterInterface
     */
    private $questionToQuestionNumberConverter;

    /**
     * @param QuestionToQuestionNumberConverterInterface $questionToQuestionNumberConverter
     */
    public function __construct(QuestionToQuestionNumberConverterInterface $questionToQuestionNumberConverter)
    {
        $this->questionToQuestionNumberConverter = $questionToQuestionNumberConverter;
    }

    /**
     * @param QuestionNumberInterface $questionNumber
     *
     * @return string
     */
    public function format(QuestionNumberInterface $questionNumber): string
    {
        $formattedProgram = $questionNumber->getProgramNumber();

        $doesFormattedQuestionHasTwoDots =
            $questionNumber->getIsAdditional()
            && (null !== $questionNumber->getParagraphNumber() || null !== $questionNumber->getSubparagraphLetter());

        $formattedParagraph = $questionNumber->getParagraphNumber() ?? '';
        $formattedSubparagraph = $questionNumber->getSubparagraphLetter() ?? '';

        $formattedIsAdditional = $questionNumber->getIsAdditional() ? 'доп' : '';

        $formattedSecondDot = ($doesFormattedQuestionHasTwoDots ? '.' : '');

        return sprintf(
            '%s.%s%s%s%s',
            $formattedProgram,
            $formattedParagraph,
            $formattedSubparagraph,
            $formattedSecondDot,
            $formattedIsAdditional
        );
    }

    /**
     * @param Question $question
     *
     * @return string
     */
    public function formatQuestion(Question $question): string
    {
        return $this->format($this->questionToQuestionNumberConverter->convert($question));
    }
}
