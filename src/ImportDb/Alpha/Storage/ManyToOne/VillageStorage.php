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
use App\ImportDb\Alpha\Entity\AlphaCard;
use App\ImportDb\Alpha\Entity\AlphaVillage;
use App\ImportDb\Alpha\ValueTrimmer\AlphaValueConverterInterface;
use App\Persistence\Entity\Location\Village;
use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class VillageStorage extends AbstractManyToOneEntityStorage
{
    /**
     * @var RaionStorage
     */
    private $raionStorage;

    /**
     * @var AlphaVillage[]
     */
    private $alphaEntityByAlphaEntityKeyCache;

    /**
     * @param RegistryInterface             $doctrine
     * @param AlphaValueConverterInterface  $valueConverter
     * @param QuestionNumberParserInterface $questionNumberParser
     * @param LoggerInterface               $logger
     * @param RaionStorage                  $raionStorage
     */
    public function __construct(
        RegistryInterface $doctrine,
        AlphaValueConverterInterface $valueConverter,
        QuestionNumberParserInterface $questionNumberParser,
        LoggerInterface $logger,
        RaionStorage $raionStorage
    ) {
        parent::__construct($doctrine, $valueConverter, $questionNumberParser, $logger);
        $this->raionStorage = $raionStorage;
    }

    /**
     * @param object|AlphaCard $alphaObject
     *
     * @return string|null
     */
    protected function getAlphaEntityKey(object $alphaObject): ?string
    {
        return $this->valueConverter->getTrimmed($alphaObject->getSelokey());
    }

    /**
     * @param object|AlphaCard $alphaObject
     *
     * @return Village
     */
    protected function createEntity(object $alphaObject): object
    {
        $alphaVillage = $this->getAlphaEntity($alphaObject);

        return (new Village())
            ->setName($this->valueConverter->getTrimmedOrNull($alphaVillage->getSelo()))
            ->setNumberInAtlas($this->valueConverter->getTrimmedOrNull($alphaVillage->getInd()))
            ->setRaion($this->raionStorage->getEntity($alphaVillage))
        ;
    }

    /**
     * @param AlphaCard $alphaCard
     *
     * @throws InvalidArgumentException
     *
     * @return AlphaVillage
     */
    private function getAlphaEntity(AlphaCard $alphaCard): AlphaVillage
    {
        if (null === $this->alphaEntityByAlphaEntityKeyCache) {
            $this->alphaEntityByAlphaEntityKeyCache = $this->createAlphaEntityByAlphaEntityKeyCache();
        }

        $alphaEntityKey = $alphaCard->getSelokey();

        if (!\array_key_exists($alphaEntityKey, $this->alphaEntityByAlphaEntityKeyCache)) {
            throw new InvalidArgumentException(sprintf('Cannot get alpha village with key %s', $alphaEntityKey));
        }

        return $this->alphaEntityByAlphaEntityKeyCache[$alphaEntityKey];
    }

    /**
     * @return AlphaVillage[]
     */
    private function createAlphaEntityByAlphaEntityKeyCache(): array
    {
        $alphaEntityByAlphaEntityKeyCache = [];

        foreach ($this->alphaObjectManager->getRepository(AlphaVillage::class)->findAll() as $alphaEntity) {
            $alphaEntityKey = $alphaEntity->getSelokey();

            if (\array_key_exists($alphaEntityKey, $alphaEntityByAlphaEntityKeyCache)) {
                throw new InvalidArgumentException(sprintf('Duplicate village key %s', $alphaEntityKey));
            }

            $alphaEntityByAlphaEntityKeyCache[$alphaEntityKey] = $alphaEntity;
        }

        return $alphaEntityByAlphaEntityKeyCache;
    }
}
