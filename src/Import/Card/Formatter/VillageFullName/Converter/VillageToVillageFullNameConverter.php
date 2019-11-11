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

namespace App\Import\Card\Formatter\VillageFullName\Converter;

use App\Import\Card\Formatter\VillageFullName\VillageFullName;
use App\Import\Card\Formatter\VillageFullName\VillageFullNameInterface;
use App\Persistence\Entity\Location\Village;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class VillageToVillageFullNameConverter implements VillageToVillageFullNameConverterInterface
{
    public function convertToVillageFullName(Village $village): VillageFullNameInterface
    {
        return new VillageFullName(
            $village->getName(),
            $village->getRaion()->getName(),
            $village->getRaion()->getOblast()->getName(),
            $village->getNumberInAtlas()
        );
    }
}
