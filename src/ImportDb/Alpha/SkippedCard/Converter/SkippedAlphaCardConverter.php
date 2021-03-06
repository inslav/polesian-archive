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

namespace App\ImportDb\Alpha\SkippedCard\Converter;

use App\ImportDb\Alpha\Entity\AlphaCard;
use App\ImportDb\Alpha\SkippedCard\SkippedAlphaCard;
use App\ImportDb\Alpha\SkippedCard\SkippedAlphaCardInterface;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class SkippedAlphaCardConverter implements SkippedAlphaCardConverterInterface
{
    public function convertAlphaCardToSkippedAlphaCard(AlphaCard $alphaCard): SkippedAlphaCardInterface
    {
        return new SkippedAlphaCard(
            $alphaCard->getSpvnkey(),
            $alphaCard->getSelokey(),
            $alphaCard->getHutor(),
            $alphaCard->getGod(),
            $alphaCard->getSezon(),
            $alphaCard->getNprog(),
            $alphaCard->getNvopr(),
            $alphaCard->getOtv(),
            $alphaCard->getDtext(),
            $alphaCard->getOptext(),
            $alphaCard->getNum()
        );
    }
}
