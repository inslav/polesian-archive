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

namespace App\Import\Card\Formatter\VillageFullName\Parser;

use App\Import\Card\Formatter\VillageFullName\VillageFullName;
use App\Import\Card\Formatter\VillageFullName\VillageFullNameInterface;
use InvalidArgumentException;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class VillageFullNameParser implements VillageFullNameParserInterface
{
    public const VILLAGE_FULL_NAME_PARTS_DELIMITER = ', ';

    /**
     * @param string $villageFullName
     *
     * @return VillageFullNameInterface
     */
    public function parseVillageFullName(string $villageFullName): VillageFullNameInterface
    {
        if (preg_match('/() \((.+)\)/', $villageFullName, $matches)) {
            $nameAndRaionAndOblast = $matches[1];
            $numberInAtlas = $matches[2];
        } else {
            $nameAndRaionAndOblast = $villageFullName;
            $numberInAtlas = null;
        }

        $villageFullNameParts = explode(self::VILLAGE_FULL_NAME_PARTS_DELIMITER, $nameAndRaionAndOblast);

        $partsCount = \count($villageFullNameParts);
        $expectedPartsCount = 3;

        if ($expectedPartsCount !== $partsCount) {
            throw new InvalidArgumentException(
                sprintf('Unexpected village full name "%s" parts count "%d"', $villageFullName, $partsCount)
            );
        }

        return new VillageFullName(
            $villageFullNameParts[0],
            $villageFullNameParts[1],
            $villageFullNameParts[2],
            $numberInAtlas
        );
    }
}
