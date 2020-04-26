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

namespace App\Download\Formatter\Xlsx;

use App\Download\Formatter\FormatterInterface;
use App\Formatter\QuestionNumber\Converter\QuestionToQuestionNumberConverter;
use App\Formatter\QuestionNumber\Formatter\QuestionNumberFormatterInterface;
use App\Persistence\Entity\Card\Card;
use App\Persistence\Entity\Card\Collector;
use App\Persistence\Entity\Card\Keyword;
use App\Persistence\Entity\Card\Question;
use App\Persistence\Entity\Card\Term;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\PropertyAccess\Exception\RuntimeException;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class XlsxFormatter implements FormatterInterface
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
        $spreadsheet = new Spreadsheet();

        $worksheet = $spreadsheet->getActiveSheet();

        $rowIndex = 1;

        $this->addHeaderToWorksheet($worksheet, $rowIndex++);

        foreach ($cards as $card) {
            $this->addCardToWorksheet($card, $worksheet, $rowIndex++);
        }

        $this->setColumnHeights($worksheet);

        $temporaryFileName = tempnam(sys_get_temp_dir(), 'xlsx');

        if (false === $temporaryFileName) {
            throw new RuntimeException('Cannot create temporary file');
        }

        $writer = new Xlsx($spreadsheet);
        $writer->save($temporaryFileName);

        $fileContent = file_get_contents($temporaryFileName, true);

        if (false === $fileContent) {
            throw new RuntimeException('Cannot read from temporary file');
        }

        return $fileContent;
    }

    /**
     * @param Card[] $cards
     */
    public function getFileName(array $cards): string
    {
        return 'cards.xlsx';
    }

    public function getContentType(): string
    {
        return 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
    }

    public function addHeaderToWorksheet(Worksheet $worksheet, int $rowIndex): void
    {
        $schema = $this->getSchema();

        $columnIndex = 1;

        foreach (array_keys($schema) as $headerLabel) {
            $worksheet->setCellValueExplicitByColumnAndRow(
                $columnIndex++,
                $rowIndex,
                $this->translator->trans($headerLabel),
                DataType::TYPE_STRING
            );
        }
    }

    private function addCardToWorksheet(
        Card $card,
        Worksheet $worksheet,
        int $rowIndex
    ): void {
        $schema = $this->getSchema();

        $columnIndex = 1;

        foreach ($schema as $xlsxColumnData) {
            $worksheet->setCellValueExplicitByColumnAndRow(
                $columnIndex++,
                $rowIndex,
                $xlsxColumnData->getValueGetter()($card),
                DataType::TYPE_STRING
            );
        }
    }

    private function setColumnHeights(Worksheet $worksheet): void
    {
        $schema = $this->getSchema();

        $columnIndex = 1;

        foreach ($schema as $xlsxColumnData) {
            $worksheet
                ->getColumnDimensionByColumn($columnIndex++)
                ->setWidth($xlsxColumnData->getWidth());
        }
    }

    /**
     * @return callable[]
     */
    private function getSchema(): array
    {
        return [
            'controller.card.list.download.xlsx.schema.id' => new XlsxColumnData(
                5,
                function (Card $card): string {
                    return (string) $card->getId();
                }
            ),
            'controller.card.list.download.xlsx.schema.question' => new XlsxColumnData(
                15,
                function (Card $card): string {
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
                }
            ),
            'controller.card.list.download.xlsx.schema.year' => new XlsxColumnData(
                15,
                function (Card $card): string {
                    $formattedYear = sprintf('%d', $card->getYear());

                    $formattedYearWithSeason = $formattedYear;

                    $season = $card->getSeason();

                    if (null !== $season) {
                        $formattedYearWithSeason = sprintf('%s (%s)', $formattedYear, $season->getName());
                    }

                    return $formattedYearWithSeason;
                }
            ),
            'controller.card.list.download.xlsx.schema.village' => new XlsxColumnData(
                50,
                function (Card $card): string {
                    return sprintf(
                        '%s область, %s район, %s %s',
                        $card->getVillage()->getRaion()->getOblast()->getName(),
                        $card->getVillage()->getRaion()->getName(),
                        $card->getVillage()->getName(),
                        null !== $card->getKhutor() ? sprintf('(хутор %s)', $card->getKhutor()) : ''
                    );
                }
            ),
            'controller.card.list.download.xlsx.schema.text' => new XlsxColumnData(
                50,
                function (Card $card): string {
                    return $card->getText();
                }
            ),
            'controller.card.list.download.xlsx.schema.description' => new XlsxColumnData(
                50,
                function (Card $card): string {
                    return $card->getDescription();
                }
            ),
            'controller.card.list.download.xlsx.schema.collectors' => new XlsxColumnData(
                20,
                function (Card $card): string {
                    return implode(
                        ', ',
                        $card
                            ->getCollectors()
                            ->map(function (Collector $collector): string {
                                return $collector->getName();
                            })
                            ->toArray()
                    );
                }
            ),
            'controller.card.list.download.xlsx.schema.keywords' => new XlsxColumnData(
                20,
                function (Card $card): string {
                    return implode(
                        ', ',
                        $card
                            ->getKeywords()
                            ->map(function (Keyword $keyword): string {
                                return $keyword->getName();
                            })
                            ->toArray()
                    );
                }
            ),
            'controller.card.list.download.xlsx.schema.terms' => new XlsxColumnData(
                20,
                function (Card $card): string {
                    return implode(
                        ', ',
                        $card
                            ->getTerms()
                            ->map(function (Term $term): string {
                                return $term->getName();
                            })
                            ->toArray()
                    );
                }
            ),
        ];
    }
}
