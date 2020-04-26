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

namespace App\Portation;

use App\Formatter\QuestionNumber\Formatter\QuestionNumberFormatterInterface;
use App\Formatter\QuestionNumber\Parser\QuestionNumberParserInterface;
use App\Formatter\VillageFullName\Formatter\VillageFullNameFormatterInterface;
use App\Formatter\VillageFullName\Parser\VillageFullNameParserInterface;
use App\Persistence\Entity\Card\Card;
use App\Persistence\Entity\Card\Collector;
use App\Persistence\Entity\Card\Informant;
use App\Persistence\Entity\Card\Keyword;
use App\Persistence\Entity\Card\Question;
use App\Persistence\Entity\Card\Term;
use App\Persistence\Repository\Card\CardRepository;
use App\Persistence\Repository\Card\CollectorRepository;
use App\Persistence\Repository\Card\InformantRepository;
use App\Persistence\Repository\Card\KeywordRepository;
use App\Persistence\Repository\Card\QuestionRepository;
use App\Persistence\Repository\Card\SeasonRepository;
use App\Persistence\Repository\Card\TermRepository;
use App\Persistence\Repository\Location\VillageRepository;
use InvalidArgumentException;
use LogicException;
use Vyfony\Bundle\PortationBundle\Formatter\Bool\BoolFormatterInterface;
use Vyfony\Bundle\PortationBundle\RowType\EntityRow;
use Vyfony\Bundle\PortationBundle\RowType\RowTypeInterface;
use Vyfony\Bundle\PortationBundle\Target\PortationTargetInterface;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class CardPortationTarget implements PortationTargetInterface
{
    private const QUESTIONS_DELIMITER = ', ';

    private const KEYWORDS_DELIMITER = ', ';

    private const TERMS_DELIMITER = ', ';

    private const INFORMANTS_DELIMITER = ', ';

    private const COLLECTORS_DELIMITER = ', ';

    private const NEW_ROW_KEY = '+';

    private $cardRepository;

    private $villageRepository;

    private $questionRepository;

    private $seasonRepository;

    private $keywordRepository;

    private $termRepository;

    private $informantRepository;

    private $collectorRepository;

    private $boolFormatter;

    private $questionNumberFormatter;

    private $villageFullNameFormatter;

    private $questionNumberParser;

    private $villageFullNameParser;

    public function __construct(
        CardRepository $cardRepository,
        VillageRepository $villageRepository,
        QuestionRepository $questionRepository,
        SeasonRepository $seasonRepository,
        KeywordRepository $keywordRepository,
        TermRepository $termRepository,
        InformantRepository $informantRepository,
        CollectorRepository $collectorRepository,
        BoolFormatterInterface $boolFormatter,
        QuestionNumberFormatterInterface $questionNumberFormatter,
        VillageFullNameFormatterInterface $villageFullNameFormatter,
        QuestionNumberParserInterface $questionNumberParser,
        VillageFullNameParserInterface $villageFullNameParser
    ) {
        $this->cardRepository = $cardRepository;
        $this->villageRepository = $villageRepository;
        $this->questionRepository = $questionRepository;
        $this->seasonRepository = $seasonRepository;
        $this->keywordRepository = $keywordRepository;
        $this->termRepository = $termRepository;
        $this->informantRepository = $informantRepository;
        $this->collectorRepository = $collectorRepository;
        $this->boolFormatter = $boolFormatter;
        $this->questionNumberFormatter = $questionNumberFormatter;
        $this->villageFullNameFormatter = $villageFullNameFormatter;
        $this->questionNumberParser = $questionNumberParser;
        $this->villageFullNameParser = $villageFullNameParser;
    }

    /**
     * @return array|string[]
     */
    public function getCellValues(object $entity): array
    {
        if (!$entity instanceof Card) {
            throw $this->createInvalidEntityTypeException($entity);
        }

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
            'id' => (string) $entity->getId(),
            'card.village' => $this->villageFullNameFormatter->formatVillage($entity->getVillage()),
            'card.khutor' => $entity->getKhutor(),
            'card.questions' => implode(
                self::QUESTIONS_DELIMITER,
                $entity->getQuestions()->map($formatQuestion)->toArray()
            ),
            'card.year' => (string) $entity->getYear(),
            'card.season' => null !== $entity->getSeason() ? $entity->getSeason()->getName() : '',
            'card.hasPositiveAnswer' => $this->boolFormatter->format($entity->getHasPositiveAnswer()),
            'card.text' => $entity->getText(),
            'card.description' => $entity->getDescription(),
            'card.keywords' => implode(
                self::KEYWORDS_DELIMITER,
                $entity->getKeywords()->map($formatKeyword)->toArray()
            ),
            'card.terms' => implode(
                self::TERMS_DELIMITER,
                $entity->getTerms()->map($formatTerm)->toArray()
            ),
            'card.informants' => implode(
                self::INFORMANTS_DELIMITER,
                $entity->getInformants()->map($formatInformant)->toArray()
            ),
            'card.collectors' => implode(
                self::COLLECTORS_DELIMITER,
                $entity->getCollectors()->map($formatCollector)->toArray()
            ),
        ];
    }

    /**
     * @return array|string[][]
     */
    public function getNestedCellValuesCollection(object $entity): array
    {
        if (!$entity instanceof Card) {
            throw $this->createInvalidEntityTypeException($entity);
        }

        return [];
    }

    /**
     * @return array|callable[]
     */
    public function getCellValueHandlers(string $newRowKey): array
    {
        if (self::NEW_ROW_KEY !== $newRowKey) {
            throw $this->createInvalidNewRowKeyException($newRowKey);
        }

        return [
            'id' => function (Card $card, string $id): void {
            },
            'card.village' => function (Card $card, string $formattedVillageFullName): void {
                $card->setVillage(
                    $this->villageRepository->findOneByNameAndRaionAndOblastOrCreate(
                        $this->villageFullNameParser->parseVillageFullName($formattedVillageFullName)
                    )
                );
            },
            'card.khutor' => function (Card $card, string $khutor): void {
                $card->setKhutor('' !== $khutor ? $khutor : null);
            },
            'card.questions' => function (Card $card, string $formattedQuestions): void {
                $formattedQuestionNumbers = explode(self::QUESTIONS_DELIMITER, $formattedQuestions);

                $questions = array_map(function (string $formattedQuestionNumber): Question {
                    return $this->questionRepository->createQuestion(
                        $this->questionNumberParser->parseQuestionNumber($formattedQuestionNumber)
                    );
                }, $formattedQuestionNumbers);

                $card->setQuestions($questions);
            },
            'card.year' => function (Card $card, string $formattedYear): void {
                if (($year = (int) $formattedYear) <= 0) {
                    throw new InvalidArgumentException(sprintf('Unexpected year "%s"', $formattedYear));
                }

                $card->setYear($year);
            },
            'card.season' => function (Card $card, string $seasonName): void {
                if ('' !== $seasonName) {
                    $season = $this->seasonRepository->findOneByName($seasonName);

                    if (null === $season) {
                        throw new InvalidArgumentException(sprintf('Cannot find season "%s"', $seasonName));
                    }

                    $card->setSeason($season);
                }
            },
            'card.hasPositiveAnswer' => function (Card $card, string $formattedHasPositiveAnswer): void {
                $card->setHasPositiveAnswer($this->boolFormatter->parse($formattedHasPositiveAnswer));
            },
            'card.text' => function (Card $card, string $text): void {
                $card->setText($text);
            },
            'card.description' => function (Card $card, string $description): void {
                $card->setDescription($description);
            },
            'card.keywords' => function (Card $card, string $formattedFormattedKeywords): void {
                $formattedKeywords = explode(self::KEYWORDS_DELIMITER, $formattedFormattedKeywords);

                $keywords = array_map(function (string $formattedKeyword): Keyword {
                    return $this->keywordRepository->findOneByNameOrCreate($formattedKeyword);
                }, $formattedKeywords);

                $card->setKeywords($keywords);
            },
            'card.terms' => function (Card $card, string $formattedFormattedTerms): void {
                $formattedTerms = explode(self::TERMS_DELIMITER, $formattedFormattedTerms);

                $terms = array_map(function (string $formattedTerm): Term {
                    return $this->termRepository->findOneByNameOrCreate($formattedTerm);
                }, $formattedTerms);

                $card->setTerms($terms);
            },
            'card.informants' => function (Card $card, string $formattedFormattedInformants): void {
                $formattedInformants = explode(self::INFORMANTS_DELIMITER, $formattedFormattedInformants);

                $informants = array_map(function (string $formattedInformant): Informant {
                    return $this->informantRepository->findOneByNameOrCreate($formattedInformant);
                }, $formattedInformants);

                $card->setInformants($informants);
            },
            'card.collectors' => function (Card $card, string $formattedFormattedCollectors): void {
                $formattedCollectors = explode(self::COLLECTORS_DELIMITER, $formattedFormattedCollectors);

                $collectors = array_map(function (string $formattedCollector): Collector {
                    return $this->collectorRepository->findOneByNameOrCreate($formattedCollector);
                }, $formattedCollectors);

                $card->setCollectors($collectors);
            },
        ];
    }

    public function createEntity(string $newRowKey): object
    {
        if (self::NEW_ROW_KEY !== $newRowKey) {
            throw $this->createInvalidNewRowKeyException($newRowKey);
        }

        return new Card();
    }

    /**
     * @return mixed
     */
    public function setNestedEntity(string $entityRowKey, object $entity, object $nestedEntity)
    {
        throw $this->createUnexpectedCallException(__METHOD__);
    }

    /**
     * @return array|object[]
     */
    public function getEntities(): array
    {
        return $this->cardRepository->findAllOrderedByDefault();
    }

    /**
     * @return array|object[]
     */
    public function getNestedEntities(object $entity): array
    {
        throw $this->createUnexpectedCallException(__METHOD__);
    }

    public function getRootRowType(): RowTypeInterface
    {
        return new EntityRow(self::NEW_ROW_KEY, null);
    }

    /**
     * @return array|string[]
     */
    public function getSchema(): array
    {
        return [
            'id',
            'card.village',
            'card.khutor',
            'card.questions',
            'card.year',
            'card.season',
            'card.hasPositiveAnswer',
            'card.text',
            'card.description',
            'card.keywords',
            'card.terms',
            'card.informants',
            'card.collectors',
        ];
    }

    private function createInvalidEntityTypeException(object $entity): InvalidArgumentException
    {
        return new InvalidArgumentException(
            sprintf(
                'Invalid entity type "%s", expected to get "%s"',
                \get_class($entity),
                Card::class
            )
        );
    }

    private function createInvalidNewRowKeyException(string $newRowKey): InvalidArgumentException
    {
        return new InvalidArgumentException(
            sprintf(
                'Invalid new row key "%s", expected to get "%s"',
                $newRowKey,
                self::NEW_ROW_KEY
            )
        );
    }

    private function createUnexpectedCallException(string $methodName): LogicException
    {
        return new LogicException(
            sprintf(
                'Unexpected call to a method "%s"',
                $methodName
            )
        );
    }
}
