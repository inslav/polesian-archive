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

namespace App\ImportDb\Alpha\Storage\ManyToOne\Persisted;

use App\ImportDb\Alpha\Entity\AlphaCard;
use App\ImportDb\Alpha\Exception\BrokenCardException;
use App\ImportDb\Alpha\Storage\ManyToOne\AbstractManyToOneEntityStorage;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
abstract class AbstractPersistedManyToOneEntityStorage extends AbstractManyToOneEntityStorage
{
    abstract protected function getEntityClass(): string;

    abstract protected function getEntityKey(object $entity): string;

    /**
     * @return object[]
     */
    final protected function getInitialCacheValue(): array
    {
        $entities = $this->defaultObjectManager->getRepository($this->getEntityClass())->findAll();

        $entityKeyExtractor = function (object $entity): string {
            return $this->getEntityKey($entity);
        };

        return array_combine(array_map($entityKeyExtractor, $entities), $entities);
    }

    /**
     * @param object|AlphaCard $alphaObject
     *
     * @throws BrokenCardException
     */
    final protected function createEntity(object $alphaObject): object
    {
        throw BrokenCardException::programMismatch($this->getAlphaEntityKey($alphaObject), static::class);
    }
}
