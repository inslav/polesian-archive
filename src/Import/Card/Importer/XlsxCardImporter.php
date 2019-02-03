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

namespace App\Import\Card\Importer;

use App\Entity\Card\Card;
use App\Entity\Card\Collector;
use App\Entity\Card\Informant;
use App\Entity\Card\Keyword;
use App\Entity\Card\Question;
use App\Entity\Card\Term;
use App\Import\Card\Formatter\QuestionNumber\Parser\QuestionNumberParserInterface;
use App\Import\Card\Formatter\VillageFullName\Parser\VillageFullNameParserInterface;
use App\Repository\Card\CollectorRepository;
use App\Repository\Card\InformantRepository;
use App\Repository\Card\KeywordRepository;
use App\Repository\Card\QuestionRepository;
use App\Repository\Card\SeasonRepository;
use App\Repository\Card\TermRepository;
use App\Repository\Card\VillageRepository;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use PhpOffice\PhpSpreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class XlsxCardImporter implements CardImporterInterface
{
    public const QUESTIONS_DELIMITER = ', ';

    public const KEYWORDS_DELIMITER = ', ';

    public const TERMS_DELIMITER = ', ';

    public const INFORMANTS_DELIMITER = ', ';

    public const COLLECTORS_DELIMITER = ', ';

    public const HAS_POSITIVE_ANSWER_TRUE = '';

    public const HAS_POSITIVE_ANSWER_FALSE = 'нет';

    private const NEW_ROW_ID = '+';

    /**
     * @var QuestionNumberParserInterface
     */
    private $questionNumberParser;

    /**
     * @var VillageFullNameParserInterface
     */
    private $villageFullNameParser;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var VillageRepository
     */
    private $villageRepository;

    /**
     * @var QuestionRepository
     */
    private $questionRepository;

    /**
     * @var SeasonRepository
     */
    private $seasonRepository;

    /**
     * @var KeywordRepository
     */
    private $keywordRepository;

    /**
     * @var TermRepository
     */
    private $termRepository;

    /**
     * @var InformantRepository
     */
    private $informantRepository;

    /**
     * @var CollectorRepository
     */
    private $collectorRepository;

    /**
     * @param QuestionNumberParserInterface  $questionNumberParser
     * @param VillageFullNameParserInterface $villageFullNameParser
     * @param EntityManagerInterface         $entityManager
     * @param VillageRepository              $villageRepository
     * @param QuestionRepository             $questionRepository
     * @param SeasonRepository               $seasonRepository
     * @param KeywordRepository              $keywordRepository
     * @param TermRepository                 $termRepository
     * @param InformantRepository            $informantRepository
     * @param CollectorRepository            $collectorRepository
     */
    public function __construct(
        QuestionNumberParserInterface $questionNumberParser,
        VillageFullNameParserInterface $villageFullNameParser,
        EntityManagerInterface $entityManager,
        VillageRepository $villageRepository,
        QuestionRepository $questionRepository,
        SeasonRepository $seasonRepository,
        KeywordRepository $keywordRepository,
        TermRepository $termRepository,
        InformantRepository $informantRepository,
        CollectorRepository $collectorRepository
    ) {
        $this->questionNumberParser = $questionNumberParser;
        $this->villageFullNameParser = $villageFullNameParser;
        $this->entityManager = $entityManager;
        $this->villageRepository = $villageRepository;
        $this->questionRepository = $questionRepository;
        $this->seasonRepository = $seasonRepository;
        $this->keywordRepository = $keywordRepository;
        $this->termRepository = $termRepository;
        $this->informantRepository = $informantRepository;
        $this->collectorRepository = $collectorRepository;
    }

    /**
     * @param string $pathToFile
     *
     * @throws PhpSpreadsheet\Exception
     */
    public function import(string $pathToFile): void
    {
        $reader = new Xlsx();

        $spreadsheet = $reader->load($pathToFile);

        $sheet = $spreadsheet->getActiveSheet();

        $rowIndex = 1;

        do {
            $firstCellInTheRowValue = $sheet->getCellByColumnAndRow(1, $rowIndex)->getFormattedValue();

            $rowHasData = '' !== $firstCellInTheRowValue;

            if (self::NEW_ROW_ID === $firstCellInTheRowValue) {
                $card = new Card();

                foreach ($this->getSchema() as $rawColumnIndex => $cellValueHandler) {
                    $columnIndex = $rawColumnIndex + 1;

                    $columnValue = $sheet->getCellByColumnAndRow($columnIndex, $rowIndex)->getFormattedValue();

                    $cellValueHandler($card, $columnValue);
                }

                $this->entityManager->persist($card);

                $this->entityManager->flush();
            }

            ++$rowIndex;
        } while ($rowHasData);
    }

    /**
     * @return callable[]
     */
    private function getSchema(): array
    {
        return [
            function (Card $card, string $id): void {
            },
            function (Card $card, string $formattedVillageFullName): void {
                $card->setVillage(
                    $this->villageRepository->findOneByNameAndRaionAndOblastOrCreate(
                        $this->villageFullNameParser->parseVillageFullName($formattedVillageFullName)
                    )
                );
            },
            function (Card $card, string $khutor): void {
                $card->setKhutor('' !== $khutor ? $khutor : null);
            },
            function (Card $card, string $formattedQuestions): void {
                $formattedQuestionNumbers = explode(self::QUESTIONS_DELIMITER, $formattedQuestions);

                $questions = array_map(function (string $formattedQuestionNumber): Question {
                    return $this->questionRepository->createQuestion(
                        $this->questionNumberParser->parseQuestionNumber($formattedQuestionNumber)
                    );
                }, $formattedQuestionNumbers);

                $card->setQuestions($questions);
            },
            function (Card $card, string $formattedYear): void {
                if (($year = (int) $formattedYear) <= 0) {
                    throw new InvalidArgumentException(sprintf('Unexpected year "%s"', $formattedYear));
                }

                $card->setYear($year);
            },
            function (Card $card, string $seasonName): void {
                if ('' !== $seasonName) {
                    $season = $this->seasonRepository->findOneByName($seasonName);

                    if (null === $season) {
                        throw new InvalidArgumentException(sprintf('Cannot find season "%s"', $seasonName));
                    }

                    $card->setSeason($season);
                }
            },
            function (Card $card, string $formattedHasPositiveAnswer): void {
                $isFormattedAnswerValid = \in_array(
                    $formattedHasPositiveAnswer,
                    [self::HAS_POSITIVE_ANSWER_TRUE, self::HAS_POSITIVE_ANSWER_FALSE],
                    true
                );

                if (!$isFormattedAnswerValid) {
                    throw new InvalidArgumentException(
                        sprintf('Cannot parse answer "%s"', $formattedHasPositiveAnswer)
                    );
                }

                $card->setHasPositiveAnswer(self::HAS_POSITIVE_ANSWER_TRUE === $formattedHasPositiveAnswer);
            },
            function (Card $card, string $text): void {
                $card->setText($text);
            },
            function (Card $card, string $description): void {
                $card->setDescription($description);
            },
            function (Card $card, string $formattedFormattedKeywords): void {
                $formattedKeywords = explode(self::KEYWORDS_DELIMITER, $formattedFormattedKeywords);

                $keywords = array_map(function (string $formattedKeyword): Keyword {
                    return $this->keywordRepository->findOneByNameOrCreate($formattedKeyword);
                }, $formattedKeywords);

                $card->setKeywords($keywords);
            },
            function (Card $card, string $formattedFormattedTerms): void {
                $formattedTerms = explode(self::TERMS_DELIMITER, $formattedFormattedTerms);

                $terms = array_map(function (string $formattedTerm): Term {
                    return $this->termRepository->findOneByNameOrCreate($formattedTerm);
                }, $formattedTerms);

                $card->setTerms($terms);
            },
            function (Card $card, string $formattedFormattedInformants): void {
                $formattedInformants = explode(self::INFORMANTS_DELIMITER, $formattedFormattedInformants);

                $informants = array_map(function (string $formattedInformant): Informant {
                    return $this->informantRepository->findOneByNameOrCreate($formattedInformant);
                }, $formattedInformants);

                $card->setInformants($informants);
            },
            function (Card $card, string $formattedFormattedCollectors): void {
                $formattedCollectors = explode(self::COLLECTORS_DELIMITER, $formattedFormattedCollectors);

                $collectors = array_map(function (string $formattedCollector): Collector {
                    return $this->collectorRepository->findOneByNameOrCreate($formattedCollector);
                }, $formattedCollectors);

                $card->setCollectors($collectors);
            },
        ];
    }
}
