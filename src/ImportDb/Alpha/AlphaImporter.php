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
     * @param RegistryInterface            $doctrine
     * @param AlphaValueConverterInterface $valueConverter
     * @param VillageStorage               $villageStorage
     * @param QuestionStorage              $questionStorage
     * @param SeasonStorage                $seasonStorage
     * @param KeywordStorage               $keywordsStorage
     * @param TermStorage                  $termsStorage
     * @param InformerStorage              $informersStorage
     * @param CollectorStorage             $collectorsStorage
     * @param LoggerInterface              $logger
     */
    public function __construct(
        RegistryInterface $doctrine,
        AlphaValueConverterInterface $valueConverter,
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
        $this->villageStorage = $villageStorage;
        $this->questionStorage = $questionStorage;
        $this->seasonStorage = $seasonStorage;
        $this->keywordStorage = $keywordsStorage;
        $this->termStorage = $termsStorage;
        $this->informerStorage = $informersStorage;
        $this->collectorStorage = $collectorsStorage;
        $this->logger = $logger;
    }

    public function import(): void
    {
        $this->logger->info(sprintf('Running Alpha Import %s', static::class));

        $this->importCards();

        $this->logger->info('Alpha Import has been successfully finished');
    }

    private function importCards(): void
    {
        $this->logger->info('Importing AlphaCards');

        $this->logger->info('Retrieving AlphaCards from DB');

        $alphaCards = $this->alphaObjectManager->getRepository(AlphaCard::class)->findAll();

        $alphaCardsCount = \count($alphaCards);

        $this->logger->info(sprintf('Loaded %d AlphaCards', $alphaCardsCount));

        $this->logger->info('Starting AlphaCards to Cards conversion');

        $processingAlphaCardIndex = 0;

        foreach ($alphaCards as $alphaCard) {
            try {
                $this->logger->info(
                    sprintf(
                        'Trying to convert %d\'th AlphaCard "%s" to Card',
                        ++$processingAlphaCardIndex,
                        trim($alphaCard->getSpvnkey())
                    )
                );

                $card = (new Card())
                    ->setVillage($this->villageStorage->getEntity($alphaCard))
                    ->setKhutor($this->valueConverter->getTrimmedOrNull($alphaCard->getHutor()))
                    ->setQuestions([$this->questionStorage->getEntity($alphaCard)])
                    ->setYear($this->valueConverter->getInt($alphaCard->getGod()))
                    ->setSeason($this->seasonStorage->getEntity($alphaCard))
                    ->setText($this->valueConverter->getTrimmed($alphaCard->getDtext()))
                    ->setDescription($this->valueConverter->getTrimmed($alphaCard->getOptext()))
                    ->setKeywords($this->keywordStorage->getAndPersistEntities($alphaCard))
                    ->setTerms($this->termStorage->getAndPersistEntities($alphaCard))
                    ->setInformers($this->informerStorage->getAndPersistEntities($alphaCard))
                    ->setCollectors($this->collectorStorage->getAndPersistEntities($alphaCard))
                ;

                $this->logger->info('Persisting newly created Card object');

                $this->defaultObjectManager->persist($card);
            } catch (\Throwable $throwable) {
                $this->logger->error(
                    sprintf('Error occurred while converting alpha card "%s" to card', trim($alphaCard->getSpvnkey())),
                    ['exception' => $throwable]
                );
            }

            $this->logger->info(sprintf('%d AlphaCards left', $alphaCardsCount - $processingAlphaCardIndex));
        }

        $this->logger->info('Flushing DB');

        $this->defaultObjectManager->flush();

        $this->logger->info('DB has been successfully flushed');

        $this->logger->info('Cards import has been successfully finished');
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
     * @return array|AbstractManyToManyEntityStorage[]
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
}
