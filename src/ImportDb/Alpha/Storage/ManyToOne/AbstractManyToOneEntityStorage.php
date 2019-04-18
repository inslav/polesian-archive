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

namespace App\ImportDb\Alpha\Storage\ManyToOne;

use App\Import\Card\Formatter\QuestionNumber\Parser\QuestionNumberParserInterface;
use App\Import\Card\Formatter\QuestionNumber\QuestionNumberInterface;
use App\ImportDb\Alpha\Entity\AlphaCard;
use App\ImportDb\Alpha\ValueTrimmer\AlphaValueConverterInterface;
use Doctrine\Common\Persistence\ObjectManager;
use LogicException;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
abstract class AbstractManyToOneEntityStorage
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
     * @var QuestionNumberParserInterface
     */
    private $questionNumberParser;

    /**
     * @var object[]
     */
    private $entityByAlphaEntityKeyCache;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param RegistryInterface             $doctrine
     * @param AlphaValueConverterInterface  $valueConverter
     * @param QuestionNumberParserInterface $questionNumberParser
     * @param LoggerInterface               $logger
     */
    public function __construct(
        RegistryInterface $doctrine,
        AlphaValueConverterInterface $valueConverter,
        QuestionNumberParserInterface $questionNumberParser,
        LoggerInterface $logger
    ) {
        $this->alphaObjectManager = $doctrine->getManager('alpha');
        $this->defaultObjectManager = $doctrine->getManager('default');
        $this->valueConverter = $valueConverter;
        $this->questionNumberParser = $questionNumberParser;
        $this->logger = $logger;
    }

    /**
     * @param object $alphaObject
     *
     * @return object|null
     */
    final public function getEntity(object $alphaObject): ?object
    {
        $this->initializeCache();

        $alphaEntityKey = $this->getAlphaEntityKey($alphaObject);

        if (null === $alphaEntityKey) {
            return null;
        }

        if (!\array_key_exists($alphaEntityKey, $this->entityByAlphaEntityKeyCache)) {
            $entity = $this->createEntity($alphaObject);

            if (null === $entity) {
                throw new LogicException(
                    sprintf('Expected to create entity with alpha entity key "%s", got null', $alphaEntityKey)
                );
            }

            $this->defaultObjectManager->persist($entity);

            $this->entityByAlphaEntityKeyCache[$alphaEntityKey] = $entity;
        }

        return $this->entityByAlphaEntityKeyCache[$alphaEntityKey];
    }

    /**
     * @param object $alphaObject
     *
     * @return string|null
     */
    abstract protected function getAlphaEntityKey(object $alphaObject): ?string;

    /**
     * @param object $alphaObject
     *
     * @return object
     */
    abstract protected function createEntity(object $alphaObject): object;

    /**
     * @param AlphaCard $alphaCard
     *
     * @return QuestionNumberInterface
     */
    final protected function getQuestionNumber(AlphaCard $alphaCard): QuestionNumberInterface
    {
        return $this->questionNumberParser
            ->parseQuestionNumber(
                $this->valueConverter->getTrimmed($alphaCard->getNprog()).
                '.'.
                $this->valueConverter->getTrimmed($alphaCard->getNvopr())
            );
    }

    /**
     * @return object[]
     */
    protected function getInitialCacheValue(): array
    {
        return [];
    }

    private function initializeCache(): void
    {
        if (null === $this->entityByAlphaEntityKeyCache) {
            $this->logger->debug(sprintf('Initializing entity by alpha entity key cache in "%s"', static::class));

            $this->entityByAlphaEntityKeyCache = $this->getInitialCacheValue();
        }
    }
}
