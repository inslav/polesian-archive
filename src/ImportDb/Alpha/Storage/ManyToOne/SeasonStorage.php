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

use App\ImportDb\Alpha\Entity\AlphaCard;
use App\Persistence\Entity\Card\Season;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class SeasonStorage extends AbstractManyToOneEntityStorage
{
    /**
     * @param object|AlphaCard $alphaObject
     *
     * @return string|null
     */
    protected function getAlphaEntityKey(object $alphaObject): ?string
    {
        $seasonName = $this->valueConverter->getTrimmedOrNull($alphaObject->getSezon());

        if (null === $seasonName) {
            return null;
        }

        return mb_strtolower($seasonName);
    }

    /**
     * @param object|AlphaCard $alphaObject
     *
     * @return Season
     */
    protected function createEntity(object $alphaObject): object
    {
        return (new Season())
            ->setName(mb_strtolower($this->valueConverter->getTrimmedOrNull($alphaObject->getSezon())))
        ;
    }
}
