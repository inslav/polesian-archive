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

use App\Formatter\QuestionNumber\Parser\QuestionNumberParserInterface;
use App\Formatter\QuestionNumber\QuestionNumberInterface;
use App\ImportDb\Alpha\Entity\AlphaCard;
use App\ImportDb\Alpha\ValueTrimmer\AlphaValueConverterInterface;
use Doctrine\Common\Persistence\ManagerRegistry;
use LogicException;
use Psr\Log\LoggerInterface;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
abstract class AbstractManyToOneEntityStorage
{
    protected $alphaObjectManager;

    protected $defaultObjectManager;

    protected $valueConverter;

    private $questionNumberParser;

    private $entityByAlphaEntityKeyCache;

    private $logger;

    public function __construct(
        ManagerRegistry $doctrine,
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
                $message = sprintf('Expected to create entity with alpha entity key "%s", got null', $alphaEntityKey);

                throw new LogicException($message);
            }

            $this->defaultObjectManager->persist($entity);

            $this->entityByAlphaEntityKeyCache[$alphaEntityKey] = $entity;
        }

        return $this->entityByAlphaEntityKeyCache[$alphaEntityKey];
    }

    abstract protected function getAlphaEntityKey(object $alphaObject): ?string;

    abstract protected function createEntity(object $alphaObject): object;

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
