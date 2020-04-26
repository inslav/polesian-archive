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

namespace App\ImportDb\Alpha\Exception;

use InvalidArgumentException;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class BrokenCardException extends InvalidArgumentException
{
    private $category;

    public function __construct(string $category, string $message = '')
    {
        parent::__construct($message);

        $this->category = $category;
    }

    /**
     * @return BrokenCardException
     */
    public static function programMismatch(string $alphaEntityKey, string $storageClass): self
    {
        return new self(
            'program',
            sprintf(
                'Cannot create entity with key "%s": "%s" is persisted storage',
                $alphaEntityKey,
                $storageClass
            )
        );
    }

    /**
     * @return BrokenCardException
     */
    public static function specialCharacters(bool $isTextBroken, bool $isDescriptionBroken): self
    {
        if ($isTextBroken && !$isDescriptionBroken) {
            $brokenField = 'text';
        } elseif (!$isTextBroken && $isDescriptionBroken) {
            $brokenField = 'description';
        } elseif ($isTextBroken && $isDescriptionBroken) {
            $brokenField = 'text and description';
        } else {
            throw new InvalidArgumentException('Either "$isTextBroken" or "$isDescriptionBroken" must be true');
        }

        return new self(
            'special-characters',
            sprintf('Cannot save card with special characters in its %s', $brokenField)
        );
    }

    public function getCategory(): string
    {
        return $this->category;
    }
}
