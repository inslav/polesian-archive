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

use App\Entity\Informer;
use App\ImportDb\Alpha\Entity\AlphaInformer;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class InformerStorage extends AbstractManyToManyEntityStorage
{
    /**
     * @return string
     */
    protected function getAlphaEntityClass(): string
    {
        return AlphaInformer::class;
    }

    /**
     * @param object|AlphaInformer $alphaEntity
     *
     * @return string
     */
    protected function getAlphaEntityKey(object $alphaEntity): string
    {
        return $alphaEntity->getInformator();
    }

    /**
     * @param object|AlphaInformer $alphaEntity
     *
     * @return string|null
     */
    protected function getAlphaCardKey(object $alphaEntity): ?string
    {
        return $alphaEntity->getSpvnkey();
    }

    /**
     * @param object|AlphaInformer $alphaEntity
     *
     * @return object
     */
    protected function createEntity(object $alphaEntity): object
    {
        return (new Informer())
            ->setName($this->valueConverter->getTrimmed($alphaEntity->getInformator()))
        ;
    }
}