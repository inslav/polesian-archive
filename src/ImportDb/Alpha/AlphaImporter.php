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

namespace App\ImportDb\Alpha;

use App\Entity\Card;
use App\ImportDb\Alpha\Entity\AlphaCard;
use App\ImportDb\Alpha\SkippedCard\Collector\SkippedAlphaCardsCollectorInterface;
use App\ImportDb\Alpha\SkippedCard\Converter\SkippedAlphaCardConverterInterface;
use App\ImportDb\Alpha\Storage\ManyToMany\AbstractManyToManyEntityStorage;
use App\ImportDb\Alpha\Storage\ManyToMany\CollectorStorage;
use App\ImportDb\Alpha\Storage\ManyToMany\InformerStorage;
use App\ImportDb\Alpha\Storage\ManyToMany\KeywordStorage;
use App\ImportDb\Alpha\Storage\ManyToMany\TermStorage;
use App\ImportDb\Alpha\Storage\ManyToOne\QuestionStorage;
use App\ImportDb\Alpha\Storage\ManyToOne\SeasonStorage;
use App\ImportDb\Alpha\Storage\ManyToOne\VillageStorage;
use App\ImportDb\Alpha\ValueTrimmer\AlphaValueConverterInterface;
use Doctrine\Common\Persistence\ObjectManager;
use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class AlphaImporter implements AlphaImporterInterface
{
    /**
     * @var ObjectManager
     */
    private $alphaObjectManager;

    /**
     * @var ObjectManager
     */
    private $defaultObjectManager;

    /**
     * @var AlphaValueConverterInterface
     */
    private $valueConverter;

    /**
     * @var SkippedAlphaCardsCollectorInterface
     */
    private $skippedAlphaCardsCollector;

    /**
     * @var SkippedAlphaCardConverterInterface
     */
    private $skippedAlphaCardConverter;

    /**
     * @var VillageStorage
     */
    private $villageStorage;

    /**
     * @var QuestionStorage
     */
    private $questionStorage;

    /**
     * @var SeasonStorage
     */
    private $seasonStorage;

    /**
     * @var KeywordStorage
     */
    private $keywordStorage;

    /**
     * @var TermStorage
     */
    private $termStorage;

    /**
     * @var InformerStorage
     */
    private $informerStorage;

    /**
     * @var CollectorStorage
     */
    private $collectorStorage;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param RegistryInterface                   $doctrine
     * @param AlphaValueConverterInterface        $valueConverter
     * @param SkippedAlphaCardsCollectorInterface $skippedAlphaCardsCollector
     * @param SkippedAlphaCardConverterInterface  $skippedAlphaCardConverter
     * @param VillageStorage                      $villageStorage
     * @param QuestionStorage                     $questionStorage
     * @param SeasonStorage                       $seasonStorage
     * @param KeywordStorage                      $keywordsStorage
     * @param TermStorage                         $termsStorage
     * @param InformerStorage                     $informersStorage
     * @param CollectorStorage                    $collectorsStorage
     * @param LoggerInterface                     $logger
     */
    public function __construct(
        RegistryInterface $doctrine,
        AlphaValueConverterInterface $valueConverter,
        SkippedAlphaCardsCollectorInterface $skippedAlphaCardsCollector,
        SkippedAlphaCardConverterInterface $skippedAlphaCardConverter,
        VillageStorage $villageStorage,
        QuestionStorage $questionStorage,
        SeasonStorage $seasonStorage,
        KeywordStorage $keywordsStorage,
        TermStorage $termsStorage,
        InformerStorage $informersStorage,
        CollectorStorage $collectorsStorage,
        LoggerInterface $logger
    ) {
        $this->alphaObjectManager = $doctrine->getManager('alpha');
        $this->defaultObjectManager = $doctrine->getManager('default');
        $this->valueConverter = $valueConverter;
        $this->skippedAlphaCardsCollector = $skippedAlphaCardsCollector;
        $this->skippedAlphaCardConverter = $skippedAlphaCardConverter;
        $this->villageStorage = $villageStorage;
        $this->questionStorage = $questionStorage;
        $this->seasonStorage = $seasonStorage;
        $this->keywordStorage = $keywordsStorage;
        $this->termStorage = $termsStorage;
        $this->informerStorage = $informersStorage;
        $this->collectorStorage = $collectorsStorage;
        $this->logger = $logger;
    }

    /**
     * @param string $pathToSkippedAlphaCardsLogFile
     */
    public function import(string $pathToSkippedAlphaCardsLogFile): void
    {
        $this->logger->info(sprintf('Running Alpha Import %s', static::class));

        $this->importCards($pathToSkippedAlphaCardsLogFile);

        $this->logger->info('Alpha Import has been successfully finished');
    }

    /**
     * @param string $pathToSkippedAlphaCardsLogFile
     */
    private function importCards(string $pathToSkippedAlphaCardsLogFile): void
    {
        $this->logger->info('Importing AlphaCards');

        $alphaCards = $this->getAlphaCardsForImport();

        $this->convertAlphaCardsToCards($alphaCards);

        $this->writeSkippedAlphaCardsToFile($pathToSkippedAlphaCardsLogFile);

        $this->writeCardsToDb();

        $this->logger->info('Cards import has been successfully finished');
    }

    /**
     * @param AlphaCard[] $alphaCards
     */
    private function convertAlphaCardsToCards(array $alphaCards): void
    {
        $alphaCardsCount = \count($alphaCards);

        $this->logger->info(
            sprintf('Starting AlphaCards to Cards conversion. Total AlphaCards count: "%d"', $alphaCardsCount)
        );

        $processingAlphaCardIndex = 0;

        foreach ($alphaCards as $alphaCard) {
            $this->convertAlphaCardToCard(
                $alphaCard,
                $processingAlphaCardIndex,
                $alphaCardsCount - $processingAlphaCardIndex
            );

            ++$processingAlphaCardIndex;
        }

        $this->logger->info('AlphaCards to Cards conversion has been successfully finished');
    }

    /**
     * @param AlphaCard $alphaCard
     * @param int       $processingAlphaCardIndex
     * @param int       $alphaCardsLeft
     */
    private function convertAlphaCardToCard(
        AlphaCard $alphaCard,
        int $processingAlphaCardIndex,
        int $alphaCardsLeft
    ): void {
        try {
            $this->logger->info(
                sprintf(
                    'Trying to convert %d\'th AlphaCard "%s" to Card (%d left)',
                    $processingAlphaCardIndex,
                    trim($alphaCard->getSpvnkey()),
                    $alphaCardsLeft
                )
            );

            $text = $this->valueConverter->getTrimmed($alphaCard->getDtext());

            if ($this->isTextValueBroken($text)) {
                throw new InvalidArgumentException('Cannot save card with special characters in its text');
            }

            $description = $this->valueConverter->getTrimmed($alphaCard->getOptext());

            if ($this->isTextValueBroken($description)) {
                throw new InvalidArgumentException('Cannot save card with special characters in its description');
            }

            $card = (new Card())
                ->setVillage($this->villageStorage->getEntity($alphaCard))
                ->setKhutor($this->valueConverter->getTrimmedOrNull($alphaCard->getHutor()))
                ->setQuestions([$this->questionStorage->getEntity($alphaCard)])
                ->setYear($this->valueConverter->getInt($alphaCard->getGod()))
                ->setSeason($this->seasonStorage->getEntity($alphaCard))
                ->setHasPositiveAnswer(null === $this->valueConverter->getTrimmedOrNull($alphaCard->getOtv()))
                ->setText($text)
                ->setDescription($description)
                ->setKeywords($this->keywordStorage->getAndPersistEntities($alphaCard))
                ->setTerms($this->termStorage->getAndPersistEntities($alphaCard))
                ->setInformers($this->informerStorage->getAndPersistEntities($alphaCard))
                ->setCollectors($this->collectorStorage->getAndPersistEntities($alphaCard))
            ;

            $this->defaultObjectManager->persist($card);
        } catch (\Throwable $throwable) {
            $this->logger->error(
                sprintf('Error occurred while converting alpha card "%s" to card', trim($alphaCard->getSpvnkey())),
                ['exception' => $throwable]
            );

            $this->skippedAlphaCardsCollector->add(
                $this->skippedAlphaCardConverter->convertAlphaCardToSkippedAlphaCard($alphaCard)
            );
        }
    }

    /**
     * @param string $pathToSkippedAlphaCardsLogFile
     */
    private function writeSkippedAlphaCardsToFile(string $pathToSkippedAlphaCardsLogFile): void
    {
        $this->logger->info(sprintf('Writing skipped AlphaCards to file "%s"', $pathToSkippedAlphaCardsLogFile));

        file_put_contents(
            $pathToSkippedAlphaCardsLogFile,
            json_encode(
                $this->skippedAlphaCardsCollector->getSkippedAlphaCards(),
                JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT
            )
        );

        $this->logger->info('Skipped AlphaCards has been successfully written');
    }

    private function writeCardsToDb(): void
    {
        $this->logger->info('Flushing DB');

        $this->defaultObjectManager->flush();

        $this->logger->info('DB has been successfully flushed');
    }

    /**
     * @return AlphaCard[]
     */
    private function getAlphaCardsForImport(): array
    {
        $this->logger->info('Retrieving AlphaCards from DB');

        return $this->alphaObjectManager->getRepository(AlphaCard::class)->findAll();
    }

    private function importEntitiesWithoutRelationToCard(): void
    {
        $this->logger->info('Importing alpha-entities without relation to AlphaCard');

        foreach ($this->getManyToManyAlphaEntityStorages() as $storage) {
            $storage->getAndPersistEntitiesWithoutRelationToAlphaCard();
        }

        $this->logger->info('Flushing DB');

        $this->defaultObjectManager->flush();

        $this->logger->info('Clearing persistence state of all entities');

        $this->defaultObjectManager->clear();

        $this->logger->info('Import of alpha-entities without relation to AlphaCard has been successfully finished');
    }

    /**
     * @return AbstractManyToManyEntityStorage[]
     */
    private function getManyToManyAlphaEntityStorages(): array
    {
        return [
            $this->collectorStorage,
            $this->informerStorage,
            $this->keywordStorage,
            $this->termStorage,
        ];
    }

    /**
     * @param string $textValue
     *
     * @return bool
     */
    private function isTextValueBroken(string $textValue): bool
    {
        return false !== mb_strpos($textValue, \chr(0)) || false !== mb_strpos($textValue, \chr(1));
    }
}
