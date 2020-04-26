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

use App\ImportDb\Alpha\Entity\AlphaCard;
use App\ImportDb\Alpha\Exception\BrokenCardException;
use App\ImportDb\Alpha\SkippedCard\Collector\SkippedAlphaCardsCollectorInterface;
use App\ImportDb\Alpha\SkippedCard\Converter\SkippedAlphaCardConverterInterface;
use App\ImportDb\Alpha\Storage\ManyToMany\AbstractManyToManyEntityStorage;
use App\ImportDb\Alpha\Storage\ManyToMany\CollectorStorage;
use App\ImportDb\Alpha\Storage\ManyToMany\InformantStorage;
use App\ImportDb\Alpha\Storage\ManyToMany\KeywordStorage;
use App\ImportDb\Alpha\Storage\ManyToMany\TermStorage;
use App\ImportDb\Alpha\Storage\ManyToOne\QuestionStorage;
use App\ImportDb\Alpha\Storage\ManyToOne\SeasonStorage;
use App\ImportDb\Alpha\Storage\ManyToOne\VillageStorage;
use App\ImportDb\Alpha\ValueTrimmer\AlphaValueConverterInterface;
use App\Persistence\Entity\Card\Card;
use Doctrine\Common\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class AlphaImporter implements AlphaImporterInterface
{
    private $filesystem;

    private $alphaObjectManager;

    private $defaultObjectManager;

    private $valueConverter;

    private $skippedAlphaCardsCollector;

    private $skippedAlphaCardConverter;

    private $villageStorage;

    private $questionStorage;

    private $seasonStorage;

    private $keywordStorage;

    private $termStorage;

    private $informantStorage;

    private $collectorStorage;

    private $logger;

    public function __construct(
        Filesystem $filesystem,
        ManagerRegistry $doctrine,
        AlphaValueConverterInterface $valueConverter,
        SkippedAlphaCardsCollectorInterface $skippedAlphaCardsCollector,
        SkippedAlphaCardConverterInterface $skippedAlphaCardConverter,
        VillageStorage $villageStorage,
        QuestionStorage $questionStorage,
        SeasonStorage $seasonStorage,
        KeywordStorage $keywordsStorage,
        TermStorage $termsStorage,
        InformantStorage $informantStorage,
        CollectorStorage $collectorsStorage,
        LoggerInterface $logger
    ) {
        $this->filesystem = $filesystem;
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
        $this->informantStorage = $informantStorage;
        $this->collectorStorage = $collectorsStorage;
        $this->logger = $logger;
    }

    public function import(string $pathToSkippedAlphaCardsLogFile): void
    {
        $this->logger->info(sprintf('Running Alpha Import %s', static::class));

        $this->importCards($pathToSkippedAlphaCardsLogFile);

        $this->logger->info('Alpha Import has been successfully finished');
    }

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
            $description = $this->valueConverter->getTrimmed($alphaCard->getOptext());

            $isTextBroken = $this->isTextValueBroken($text);
            $isDescriptionBroken = $this->isTextValueBroken($description);

            if ($isTextBroken || $isDescriptionBroken) {
                throw BrokenCardException::specialCharacters($isTextBroken, $isDescriptionBroken);
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
                ->setInformants($this->informantStorage->getAndPersistEntities($alphaCard))
                ->setCollectors($this->collectorStorage->getAndPersistEntities($alphaCard))
            ;

            $this->defaultObjectManager->persist($card);
        } catch (\Throwable $throwable) {
            $this->logger->error(
                sprintf('Error occurred while converting alpha card "%s" to card', trim($alphaCard->getSpvnkey())),
                ['exception' => $throwable]
            );

            $errorCategory = 'unknown';

            if ($throwable instanceof BrokenCardException) {
                $errorCategory = $throwable->getCategory();
            }
            $this->skippedAlphaCardsCollector->add(
                $errorCategory,
                $this->skippedAlphaCardConverter->convertAlphaCardToSkippedAlphaCard($alphaCard)
            );
        }
    }

    private function writeSkippedAlphaCardsToFile(string $pathToSkippedAlphaCardsLogDirectory): void
    {
        $this->logger->info(
            sprintf('Writing skipped AlphaCards to directory "%s"', $pathToSkippedAlphaCardsLogDirectory)
        );

        if (!$this->filesystem->exists($pathToSkippedAlphaCardsLogDirectory)) {
            $this->filesystem->mkdir($pathToSkippedAlphaCardsLogDirectory);
        }

        foreach ($this->skippedAlphaCardsCollector->getSkippedAlphaCardsByCategory() as $category => $alphaCards) {
            file_put_contents(
                $pathToSkippedAlphaCardsLogDirectory.\DIRECTORY_SEPARATOR.$category,
                json_encode(
                    $alphaCards,
                    JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT
                )
            );
        }

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
            $this->informantStorage,
            $this->keywordStorage,
            $this->termStorage,
        ];
    }

    private function isTextValueBroken(string $textValue): bool
    {
        return false !== mb_strpos($textValue, \chr(0)) || false !== mb_strpos($textValue, \chr(1));
    }
}
