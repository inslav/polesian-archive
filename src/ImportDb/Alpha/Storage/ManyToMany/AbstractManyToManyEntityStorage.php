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

namespace App\ImportDb\Alpha\Storage\ManyToMany;

use App\ImportDb\Alpha\Entity\AlphaCard;
use App\ImportDb\Alpha\ValueTrimmer\AlphaValueConverterInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
abstract class AbstractManyToManyEntityStorage
{
    /**
     * @var ObjectManager
     */
    protected $alphaObjectManager;

    /**
     * @var ObjectManager
     */
    protected $defaultObjectManager;

    /**
     * @var AlphaValueConverterInterface
     */
    protected $valueConverter;

    /**
     * @var object[]
     */
    private $entityByAlphaEntityKeyCache = [];

    /**
     * @var object[][]
     */
    private $alphaEntitiesByAlphaCardKeyCache;

    /**
     * @var object[]
     */
    private $alphaEntitiesWithoutRelationToAlphaCard;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param RegistryInterface            $doctrine
     * @param AlphaValueConverterInterface $valueConverter
     * @param LoggerInterface              $logger
     */
    public function __construct(
        RegistryInterface $doctrine,
        AlphaValueConverterInterface $valueConverter,
        LoggerInterface $logger
    ) {
        $this->alphaObjectManager = $doctrine->getManager('alpha');
        $this->defaultObjectManager = $doctrine->getManager('default');
        $this->valueConverter = $valueConverter;
        $this->logger = $logger;
    }

    /**
     * @param AlphaCard $alphaCard
     *
     * @return object[]
     */
    final public function getAndPersistEntities(AlphaCard $alphaCard): array
    {
        $entities = [];

        foreach ($this->getAlphaEntities($alphaCard) as $alphaEntity) {
            $entities[] = $this->getEntity($alphaEntity);
        }

        return $entities;
    }

    /**
     * @return object[]
     */
    final public function getAndPersistEntitiesWithoutRelationToAlphaCard(): array
    {
        if (null === $this->alphaEntitiesWithoutRelationToAlphaCard) {
            $this->createAlphaEntitiesCache();
        }

        $this->logger->info(
            sprintf(
                'Converting "%d" alpha-entities without relation to AlphaCard in "%s" to entities',
                \count($this->alphaEntitiesWithoutRelationToAlphaCard),
                static::class
            )
        );

        $entitiesWithoutRelationToAlphaCard = [];

        foreach ($this->alphaEntitiesWithoutRelationToAlphaCard as $alphaEntity) {
            $entitiesWithoutRelationToAlphaCard[] = $this->getEntity($alphaEntity);
        }

        $this->logger->info(
            sprintf(
                'Conversion of alpha-entities without relation to AlphaCardin "%s" has been successfully finished',
                static::class
            )
        );

        return $entitiesWithoutRelationToAlphaCard;
    }

    /**
     * @return string
     */
    abstract protected function getAlphaEntityClass(): string;

    /**
     * @param object $alphaEntity
     *
     * @return string
     */
    abstract protected function getAlphaEntityKey(object $alphaEntity): string;

    /**
     * @param object $alphaEntity
     *
     * @return string|null
     */
    abstract protected function getAlphaCardKey(object $alphaEntity): ?string;

    /**
     * @param object $alphaEntity
     *
     * @return object
     */
    abstract protected function createEntity(object $alphaEntity): object;

    /**
     * @param object $alphaEntity
     *
     * @return object
     */
    private function getEntity(object $alphaEntity): object
    {
        $alphaEntityKey = $this->getAlphaEntityKey($alphaEntity);

        if (!\array_key_exists($alphaEntityKey, $this->entityByAlphaEntityKeyCache)) {
            $entity = $this->createEntity($alphaEntity);

            $this->defaultObjectManager->persist($entity);

            $this->entityByAlphaEntityKeyCache[$alphaEntityKey] = $entity;
        }

        return $this->entityByAlphaEntityKeyCache[$alphaEntityKey];
    }

    /**
     * @param AlphaCard $alphaCard
     *
     * @return object[]
     */
    private function getAlphaEntities(AlphaCard $alphaCard): array
    {
        if (null === $this->alphaEntitiesByAlphaCardKeyCache) {
            $this->createAlphaEntitiesCache();
        }

        $alphaCardKey = $alphaCard->getSpvnkey();

        return \array_key_exists($alphaCardKey, $this->alphaEntitiesByAlphaCardKeyCache)
            ? $this->alphaEntitiesByAlphaCardKeyCache[$alphaCard->getSpvnkey()]
            : [];
    }

    private function createAlphaEntitiesCache(): void
    {
        $this->logger->debug(sprintf('Creating alpha-entities cache for "%s"', static::class));

        $this->alphaEntitiesByAlphaCardKeyCache = [];

        $this->alphaEntitiesWithoutRelationToAlphaCard = [];

        $alphaEntityClass = $this->getAlphaEntityClass();

        $this->logger->debug(sprintf('Loading alpha-entities of class "%s"', $alphaEntityClass));

        $alphaEntities = $this->alphaObjectManager->getRepository($alphaEntityClass)->findAll();

        $this->logger->debug(
            sprintf('Loaded "%d" alpha-entities of class "%s"', \count($alphaEntities), $alphaEntityClass)
        );

        foreach ($alphaEntities as $alphaEntity) {
            $alphaCardKey = $this->getAlphaCardKey($alphaEntity);

            if (null === $alphaCardKey) {
                $this->alphaEntitiesWithoutRelationToAlphaCard[] = $alphaEntity;
            } else {
                if (!\array_key_exists($alphaCardKey, $this->alphaEntitiesByAlphaCardKeyCache)) {
                    $this->alphaEntitiesByAlphaCardKeyCache[$alphaCardKey] = [];
                }

                $this->alphaEntitiesByAlphaCardKeyCache[$alphaCardKey][] = $alphaEntity;
            }
        }

        $this->logger->info(sprintf('Created alpha-entities cache for class "%s"', static::class));
    }
}
