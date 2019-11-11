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

namespace App\Download\Format;

use App\Import\Card\Formatter\QuestionNumber\Converter\QuestionToQuestionNumberConverter;
use App\Import\Card\Formatter\QuestionNumber\Formatter\QuestionNumberFormatterInterface;
use App\Persistence\Entity\Card\Card;
use App\Persistence\Entity\Card\Collector;
use App\Persistence\Entity\Card\Keyword;
use App\Persistence\Entity\Card\Question;
use App\Persistence\Entity\Card\Term;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class TxtFormatter implements FormatterInterface
{
    /**
     * @var QuestionToQuestionNumberConverter
     */
    private $questionToQuestionNumberConverter;

    /**
     * @var QuestionNumberFormatterInterface
     */
    private $questionNumberFormatter;

    public function __construct(
        QuestionToQuestionNumberConverter $questionToQuestionNumberConverter,
        QuestionNumberFormatterInterface $questionNumberFormatter
    ) {
        $this->questionToQuestionNumberConverter = $questionToQuestionNumberConverter;
        $this->questionNumberFormatter = $questionNumberFormatter;
    }

    /**
     * @param Card[]|array $cards
     */
    public function format(array $cards): string
    {
        $formattedCards = [];

        foreach ($cards as $card) {
            $formattedCards[] = $this->formatCard($card);
        }

        return implode(PHP_EOL.'----------'.PHP_EOL.PHP_EOL, $formattedCards);
    }

    /**
     * @param Card[] $cards
     */
    public function getFileName(array $cards): string
    {
        return 'cards.txt';
    }

    public function getContentType(): string
    {
        return 'text/plain';
    }

    private function formatCard(Card $card): string
    {
        $formattedQuestions = implode(
            ', ',
            $card
                ->getQuestions()
                ->map(function (Question $question): string {
                    return $this->questionNumberFormatter->format(
                        $this->questionToQuestionNumberConverter->convert($question)
                    );
                })
                ->toArray()
        );

        $formattedYear = sprintf('%d', $card->getYear());

        $formattedYearWithSeason = $formattedYear;

        $season = $card->getSeason();

        if (null !== $season) {
            $formattedYearWithSeason = sprintf('%s (%s)', $formattedYear, $season->getName());
        }

        $formattedVillage = sprintf(
            '%s область, %s район, %s %s',
            $card->getVillage()->getRaion()->getOblast()->getName(),
            $card->getVillage()->getRaion()->getName(),
            $card->getVillage()->getName(),
            null !== $card->getKhutor() ? sprintf('(хутор %s)', $card->getKhutor()) : ''
        );

        $formattedCollectors = implode(
            ', ',
            $card
                ->getCollectors()
                ->map(function (Collector $collector): string {
                    return $collector->getName();
                })
                ->toArray()
        );

        $formattedKeywords = implode(
            ', ',
            $card
                ->getKeywords()
                ->map(function (Keyword $keyword): string {
                    return $keyword->getName();
                })
                ->toArray()
        );

        $formattedTerms = implode(
            ', ',
            $card
                ->getTerms()
                ->map(function (Term $term): string {
                    return $term->getName();
                })
                ->toArray()
        );

        return <<<EOT
id: {$card->getId()}

вопрос: $formattedQuestions

год: $formattedYearWithSeason

село: $formattedVillage

диалектный текст: {$card->getText()}

описание: {$card->getDescription()}

собиратели: $formattedCollectors

ключевые слова: $formattedKeywords

термины: $formattedTerms

EOT;
    }
}
