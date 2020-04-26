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

namespace App\Formatter\VillageFullName\Formatter;

use App\Formatter\VillageFullName\Converter\VillageToVillageFullNameConverterInterface;
use App\Formatter\VillageFullName\Parser\VillageFullNameParser;
use App\Formatter\VillageFullName\VillageFullNameInterface;
use App\Persistence\Entity\Location\Village;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class VillageFullNameFormatter implements VillageFullNameFormatterInterface
{
    private $villageToVillageFullNameConverter;

    public function __construct(VillageToVillageFullNameConverterInterface $villageToVillageFullNameConverter)
    {
        $this->villageToVillageFullNameConverter = $villageToVillageFullNameConverter;
    }

    public function format(VillageFullNameInterface $villageFullName): string
    {
        return $this->formatNameAndRaionAndOblast($villageFullName).$this->formatNumberInAtlas($villageFullName);
    }

    public function formatVillage(Village $village): string
    {
        return $this->format($this->villageToVillageFullNameConverter->convertToVillageFullName($village));
    }

    private function formatNameAndRaionAndOblast(VillageFullNameInterface $villageFullName): string
    {
        return implode(
            VillageFullNameParser::VILLAGE_FULL_NAME_PARTS_DELIMITER,
            [
                $villageFullName->getName(),
                $villageFullName->getRaion(),
                $villageFullName->getOblast(),
            ]
        );
    }

    private function formatNumberInAtlas(VillageFullNameInterface $villageFullName): string
    {
        if (null !== $villageFullName->getNumberInAtlas()) {
            return sprintf(' (№ %s)', $villageFullName->getNumberInAtlas());
        }

        return '';
    }
}
