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

namespace App\Formatter\QuestionNumber\Formatter;

use App\Formatter\QuestionNumber\Converter\QuestionToQuestionNumberConverterInterface;
use App\Formatter\QuestionNumber\Parser\QuestionNumberParser;
use App\Formatter\QuestionNumber\QuestionNumberInterface;
use App\Persistence\Entity\Card\Question;
use InvalidArgumentException;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class QuestionNumberFormatter implements QuestionNumberFormatterInterface
{
    private $questionToQuestionNumberConverter;

    public function __construct(QuestionToQuestionNumberConverterInterface $questionToQuestionNumberConverter)
    {
        $this->questionToQuestionNumberConverter = $questionToQuestionNumberConverter;
    }

    public function format(QuestionNumberInterface $questionNumber): string
    {
        if (null === $questionNumber->getParagraphNumber() && null !== $questionNumber->getSubparagraphLetter()) {
            $message = 'Cannot format question number: subparagraph is set while paragraph is null';

            throw new InvalidArgumentException($message);
        }

        $formattedProgram = $questionNumber->getProgramNumber();

        $formattedParagraphAndSubparagraph = null !== $questionNumber->getParagraphNumber()
            ? implode(
                '',
                [
                    QuestionNumberParser::QUESTION_NUMBER_PARTS_DELIMITER,
                    (string) $questionNumber->getParagraphNumber(),
                    $questionNumber->getSubparagraphLetter() ?? '',
                ]
            )
            : '';

        $formattedIsAdditional = $questionNumber->getIsAdditional()
            ? implode(
                '',
                [
                    QuestionNumberParser::QUESTION_NUMBER_PARTS_DELIMITER,
                    QuestionNumberParser::QUESTION_NUMBER_IS_ADDITIONAL_PART,
                ]
            )
            : '';

        return implode(
            '',
            [
                $formattedProgram,
                $formattedParagraphAndSubparagraph,
                $formattedIsAdditional,
            ]
        );
    }

    public function formatQuestion(Question $question): string
    {
        return $this->format($this->questionToQuestionNumberConverter->convert($question));
    }
}
