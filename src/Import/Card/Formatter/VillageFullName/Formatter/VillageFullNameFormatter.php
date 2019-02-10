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

namespace App\Import\Card\Formatter\VillageFullName\Formatter;

use App\Import\Card\Formatter\VillageFullName\Converter\VillageToVillageFullNameConverterInterface;
use App\Import\Card\Formatter\VillageFullName\Parser\VillageFullNameParser;
use App\Import\Card\Formatter\VillageFullName\VillageFullNameInterface;
use App\Persistence\Entity\Location\Village;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class VillageFullNameFormatter implements VillageFullNameFormatterInterface
{
    /**
     * @var VillageToVillageFullNameConverterInterface
     */
    private $villageToVillageFullNameConverter;

    /**
     * @param VillageToVillageFullNameConverterInterface $villageToVillageFullNameConverter
     */
    public function __construct(VillageToVillageFullNameConverterInterface $villageToVillageFullNameConverter)
    {
        $this->villageToVillageFullNameConverter = $villageToVillageFullNameConverter;
    }

    /**
     * @param VillageFullNameInterface $villageFullName
     *
     * @return string
     */
    public function format(VillageFullNameInterface $villageFullName): string
    {
        return $this->formatNameAndRaionAndOblast($villageFullName).$this->formatNumberInAtlas($villageFullName);
    }

    /**
     * @param Village $village
     *
     * @return string
     */
    public function formatVillage(Village $village): string
    {
        return $this->format($this->villageToVillageFullNameConverter->convertToVillageFullName($village));
    }

    /**
     * @param VillageFullNameInterface $villageFullName
     *
     * @return string
     */
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

    /**
     * @param VillageFullNameInterface $villageFullName
     *
     * @return string
     */
    private function formatNumberInAtlas(VillageFullNameInterface $villageFullName): string
    {
        if (null !== $villageFullName->getNumberInAtlas()) {
            return sprintf(' (%s)', $villageFullName->getNumberInAtlas());
        }

        return '';
    }
}
