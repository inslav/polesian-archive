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

use App\ImportDb\Alpha\Entity\AlphaVillage;
use App\Persistence\Entity\Location\Oblast;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class OblastStorage extends AbstractManyToOneEntityStorage
{
    /**
     * @param object|AlphaVillage $alphaObject
     */
    protected function getAlphaEntityKey(object $alphaObject): ?string
    {
        $oblastName = $this->valueConverter->getTrimmedOrNull($alphaObject->getRegion());

        if (null !== $oblastName) {
            $oblastName = $this->getFixedOblastName($oblastName);
        }

        return $oblastName;
    }

    /**
     * @param object|AlphaVillage $alphaObject
     *
     * @return object|Oblast
     */
    protected function createEntity(object $alphaObject): object
    {
        return (new Oblast())
            ->setName($this->getAlphaEntityKey($alphaObject))
        ;
    }

    private function getFixedOblastName(string $oblastName): string
    {
        $correctOblastNameByOblastName = [
            'Архангелская' => 'Архангельская',
        ];

        if (\array_key_exists($oblastName, $correctOblastNameByOblastName)) {
            return $correctOblastNameByOblastName[$oblastName];
        }

        return $oblastName;
    }
}
