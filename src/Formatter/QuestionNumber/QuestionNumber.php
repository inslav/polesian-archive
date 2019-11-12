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

namespace App\Formatter\QuestionNumber;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class QuestionNumber implements QuestionNumberInterface
{
    /**
     * @var string
     */
    private $programNumber;

    /**
     * @var int|null
     */
    private $paragraphNumber;

    /**
     * @var string|null
     */
    private $subparagraphLetter;

    /**
     * @var bool
     */
    private $isAdditional;

    public function __construct(
        string $programNumber,
        ?int $paragraphNumber,
        ?string $subparagraphLetter,
        bool $isAdditional
    ) {
        $this->programNumber = $programNumber;
        $this->paragraphNumber = $paragraphNumber;
        $this->subparagraphLetter = $subparagraphLetter;
        $this->isAdditional = $isAdditional;
    }

    public function getProgramNumber(): string
    {
        return $this->programNumber;
    }

    public function getParagraphNumber(): ?int
    {
        return $this->paragraphNumber;
    }

    public function getSubparagraphLetter(): ?string
    {
        return $this->subparagraphLetter;
    }

    public function getIsAdditional(): bool
    {
        return $this->isAdditional;
    }
}
