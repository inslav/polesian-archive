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

use App\ImportDb\Alpha\Entity\AlphaInformant;
use App\Persistence\Entity\Card\Informant;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class InformantStorage extends AbstractManyToManyEntityStorage
{
    protected function getAlphaEntityClass(): string
    {
        return AlphaInformant::class;
    }

    /**
     * @param object|AlphaInformant $alphaEntity
     */
    protected function getAlphaEntityKey(object $alphaEntity): string
    {
        return mb_strtoupper($this->valueConverter->getTrimmed($alphaEntity->getInformator()));
    }

    /**
     * @param object|AlphaInformant $alphaEntity
     */
    protected function getAlphaCardKey(object $alphaEntity): ?string
    {
        return $alphaEntity->getSpvnkey();
    }

    /**
     * @param object|AlphaInformant $alphaEntity
     *
     * @return object|Informant
     */
    protected function createEntity(object $alphaEntity): object
    {
        return (new Informant())
            ->setName(mb_strtoupper($this->valueConverter->getTrimmed($alphaEntity->getInformator())))
        ;
    }
}
