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

namespace App\ImportDb\Alpha\ValueTrimmer;

use InvalidArgumentException;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class AlphaValueConverter implements AlphaValueConverterInterface
{
    /**
     * @param string|null $value
     *
     * @return string|null
     */
    public function getTrimmedOrNull(?string $value): ?string
    {
        if (null !== $value) {
            $trimmedValue = trim($value);

            if ('' !== $trimmedValue) {
                return $trimmedValue;
            }
        }

        return null;
    }

    /**
     * @param string $value
     *
     * @return string
     */
    public function getTrimmed(string $value): string
    {
        return trim($value);
    }

    /**
     * @param string $value
     *
     * @return int
     */
    public function getInt(string $value): int
    {
        if (1 === preg_match('/\D/', $value)) {
            throw new InvalidArgumentException(sprintf('Cannot parse int from value "%s"', $value));
        }

        return (int) $value;
    }
}
