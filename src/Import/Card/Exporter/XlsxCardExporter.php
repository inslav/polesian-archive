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

namespace App\Import\Card\Exporter;

use App\Entity\Card\Card;
use App\Entity\Card\Collector;
use App\Entity\Card\Informant;
use App\Entity\Card\Keyword;
use App\Entity\Card\Question;
use App\Entity\Card\Term;
use App\Import\Card\Formatter\QuestionNumber\Formatter\QuestionNumberFormatterInterface;
use App\Import\Card\Formatter\VillageFullName\Formatter\VillageFullNameFormatterInterface;
use App\Import\Card\Importer\XlsxCardImporter;
use App\Repository\Card\CardRepository;
use InvalidArgumentException;
use PhpOffice\PhpSpreadsheet;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class XlsxCardExporter implements CardExporterInterface
{
    /**
     * @var CardRepository
     */
    private $cardRepository;

    /**
     * @var QuestionNumberFormatterInterface
     */
    private $questionNumberFormatter;

    /**
     * @var VillageFullNameFormatterInterface
     */
    private $villageFullNameFormatter;

    /**
     * @param CardRepository                    $cardRepository
     * @param QuestionNumberFormatterInterface  $questionNumberFormatter
     * @param VillageFullNameFormatterInterface $villageFullNameFormatter
     */
    public function __construct(
        CardRepository $cardRepository,
        QuestionNumberFormatterInterface $questionNumberFormatter,
        VillageFullNameFormatterInterface $villageFullNameFormatter
    ) {
        $this->cardRepository = $cardRepository;
        $this->questionNumberFormatter = $questionNumberFormatter;
        $this->villageFullNameFormatter = $villageFullNameFormatter;
    }

    /**
     * @param string   $pathToFile
     * @param int|null $bunchSize
     *
     * @throws PhpSpreadsheet\Exception
     * @throws InvalidArgumentException
     */
    public function export(string $pathToFile, ?int $bunchSize): void
    {
        if (null !== $bunchSize && $bunchSize <= 0) {
            throw new InvalidArgumentException('Bunch size can only be null or greater than zero');
        }

        $cards = $this->cardRepository->findAllOrderedByDefault();

        if (null === $bunchSize) {
            $this->exportCards($cards, $pathToFile);
        } else {
            foreach (array_chunk($cards, $bunchSize) as $bunchIndex => $cardsBunch) {
                $this->exportCards($cardsBunch, $this->getBunchPathToFile($pathToFile, $bunchIndex));
            }
        }
    }

    /**
     * @param array  $cards
     * @param string $pathToFile
     *
     * @throws PhpSpreadsheet\Exception
     */
    private function exportCards(array $cards, string $pathToFile): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        foreach ($cards as $entityIndex => $card) {
            $rowIndex = $entityIndex + 1;

            foreach ($this->getColumnValues($card) as $rawColumnIndex => $columnValue) {
                $columnIndex = $rawColumnIndex + 1;

                $sheet->getColumnDimensionByColumn($columnIndex)->setAutoSize(true);

                $sheet->setCellValueByColumnAndRow($columnIndex, $rowIndex, $columnValue);
            }
        }

        $writer = new Xlsx($spreadsheet);
        $writer->save($pathToFile);
    }

    /**
     * @param string $pathToFile
     * @param int    $bunchIndex
     *
     * @return string
     */
    private function getBunchPathToFile(string $pathToFile, int $bunchIndex): string
    {
        $pathToFileParts = explode('.', $pathToFile);

        $formattedBunchIndex = (string) ($bunchIndex + 1);

        if (\count($pathToFileParts) > 1) {
            $extension = array_pop($pathToFileParts);

            $pathToFileParts[] = $formattedBunchIndex;
            $pathToFileParts[] = $extension;
        } else {
            $pathToFileParts[] = $formattedBunchIndex;
        }

        return implode('.', $pathToFileParts);
    }

    /**
     * @param Card $card
     *
     * @return array
     */
    private function getColumnValues(Card $card): array
    {
        $formatQuestion = function (Question $question): string {
            return $this->questionNumberFormatter->formatQuestion($question);
        };

        $formatKeyword = function (Keyword $keyword): string {
            return $keyword->getName();
        };

        $formatTerm = function (Term $term): string {
            return $term->getName();
        };

        $formatInformant = function (Informant $informant): string {
            return $informant->getName();
        };

        $formatCollector = function (Collector $collector): string {
            return $collector->getName();
        };

        return [
            (string) $card->getId(),
            $this->villageFullNameFormatter->formatVillage($card->getVillage()),
            $card->getKhutor(),
            implode(XlsxCardImporter::QUESTIONS_DELIMITER, $card->getQuestions()->map($formatQuestion)->toArray()),
            (string) $card->getYear(),
            null !== $card->getSeason() ? $card->getSeason()->getName() : '',
            $card->getHasPositiveAnswer()
                ? XlsxCardImporter::HAS_POSITIVE_ANSWER_TRUE
                : XlsxCardImporter::HAS_POSITIVE_ANSWER_FALSE,
            $card->getText(),
            $card->getDescription(),
            implode(XlsxCardImporter::KEYWORDS_DELIMITER, $card->getKeywords()->map($formatKeyword)->toArray()),
            implode(XlsxCardImporter::TERMS_DELIMITER, $card->getTerms()->map($formatTerm)->toArray()),
            implode(XlsxCardImporter::INFORMANTS_DELIMITER, $card->getInformants()->map($formatInformant)->toArray()),
            implode(XlsxCardImporter::COLLECTORS_DELIMITER, $card->getCollectors()->map($formatCollector)->toArray()),
        ];
    }
}
