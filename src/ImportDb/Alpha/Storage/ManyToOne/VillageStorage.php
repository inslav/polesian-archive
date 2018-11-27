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

use App\Entity\Village;
use App\ImportDb\Alpha\Entity\AlphaCard;
use App\ImportDb\Alpha\Entity\AlphaVillage;
use InvalidArgumentException;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class VillageStorage extends AbstractManyToOneEntityStorage
{
    /**
     * @var AlphaVillage[]
     */
    private $alphaEntityByAlphaEntityKeyCache;

    /**
     * @param AlphaCard $alphaCard
     *
     * @return string|null
     */
    protected function getAlphaEntityKey(AlphaCard $alphaCard): ?string
    {
        return $this->valueConverter->getTrimmed($alphaCard->getSelokey());
    }

    /**
     * @param AlphaCard $alphaCard
     *
     * @return Village
     */
    protected function createEntity(AlphaCard $alphaCard): object
    {
        $alphaVillage = $this->getAlphaEntity($alphaCard);

        return (new Village())
            ->setName($this->valueConverter->getTrimmed($alphaVillage->getSelo()))
            ->setRaion($this->valueConverter->getTrimmed($alphaVillage->getDistrict()))
            ->setOblast($this->valueConverter->getTrimmed($alphaVillage->getRegion()))
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

        if (!array_key_exists($alphaEntityKey, $this->alphaEntityByAlphaEntityKeyCache)) {
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

            if (array_key_exists($alphaEntityKey, $alphaEntityByAlphaEntityKeyCache)) {
                throw new InvalidArgumentException(sprintf('Duplicate village key %s', $alphaEntityKey));
            }

            $alphaEntityByAlphaEntityKeyCache[$alphaEntityKey] = $alphaEntity;
        }

        return $alphaEntityByAlphaEntityKeyCache;
    }
}
