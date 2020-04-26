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

namespace App\Download\Formatter\Txt;

use App\Download\Formatter\FormatterInterface;
use App\Formatter\QuestionNumber\Converter\QuestionToQuestionNumberConverter;
use App\Formatter\QuestionNumber\Formatter\QuestionNumberFormatterInterface;
use App\Persistence\Entity\Card\Card;
use App\Persistence\Entity\Card\Collector;
use App\Persistence\Entity\Card\Keyword;
use App\Persistence\Entity\Card\Question;
use App\Persistence\Entity\Card\Term;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class TxtFormatter implements FormatterInterface
{
    private $questionToQuestionNumberConverter;

    private $questionNumberFormatter;

    private $translator;

    public function __construct(
        QuestionToQuestionNumberConverter $questionToQuestionNumberConverter,
        QuestionNumberFormatterInterface $questionNumberFormatter,
        TranslatorInterface $translator
    ) {
        $this->questionToQuestionNumberConverter = $questionToQuestionNumberConverter;
        $this->questionNumberFormatter = $questionNumberFormatter;
        $this->translator = $translator;
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

        return implode("----------\r\n\r\n", $formattedCards);
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
        $schema = $this->getSchema();

        $formattedCard = '';

        foreach ($schema as $columnName => $columnValueGetter) {
            $formattedCard .= sprintf(
                "%s: %s\r\n\r\n",
                $this->translator->trans($columnName),
                $columnValueGetter($card)
            );
        }

        return $formattedCard;
    }

    /**
     * @return callable[]
     */
    private function getSchema(): array
    {
        return [
            'controller.card.list.download.txt.schema.id' => function (Card $card): string {
                return (string) $card->getId();
            },
            'controller.card.list.download.txt.schema.question' => function (Card $card): string {
                return implode(
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
            },
            'controller.card.list.download.txt.schema.year' => function (Card $card): string {
                $formattedYear = sprintf('%d', $card->getYear());

                $formattedYearWithSeason = $formattedYear;

                $season = $card->getSeason();

                if (null !== $season) {
                    $formattedYearWithSeason = sprintf('%s (%s)', $formattedYear, $season->getName());
                }

                return $formattedYearWithSeason;
            },
            'controller.card.list.download.txt.schema.village' => function (Card $card): string {
                return sprintf(
                    '%s область, %s район, %s %s',
                    $card->getVillage()->getRaion()->getOblast()->getName(),
                    $card->getVillage()->getRaion()->getName(),
                    $card->getVillage()->getName(),
                    null !== $card->getKhutor() ? sprintf('(хутор %s)', $card->getKhutor()) : ''
                );
            },
            'controller.card.list.download.txt.schema.text' => function (Card $card): string {
                return $card->getText();
            },
            'controller.card.list.download.txt.schema.description' => function (Card $card): string {
                return $card->getDescription();
            },
            'controller.card.list.download.txt.schema.collectors' => function (Card $card): string {
                return implode(
                    ', ',
                    $card
                        ->getCollectors()
                        ->map(function (Collector $collector): string {
                            return $collector->getName();
                        })
                        ->toArray()
                );
            },
            'controller.card.list.download.txt.schema.keywords' => function (Card $card): string {
                return implode(
                    ', ',
                    $card
                        ->getKeywords()
                        ->map(function (Keyword $keyword): string {
                            return $keyword->getName();
                        })
                        ->toArray()
                );
            },
            'controller.card.list.download.txt.schema.terms' => function (Card $card): string {
                return implode(
                    ', ',
                    $card
                        ->getTerms()
                        ->map(function (Term $term): string {
                            return $term->getName();
                        })
                        ->toArray()
                );
            },
        ];
    }
}
