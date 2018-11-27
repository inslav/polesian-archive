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

namespace App\Import\Program\Question\Number;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
interface QuestionNumberInterface
{
    /**
     * @return string
     */
    public function getProgramNumber(): string;

    /**
     * @return int|null
     */
    public function getParagraphNumber(): ?int;

    /**
     * @return string|null
     */
    public function getSubparagraphLetter(): ?string;

    /**
     * @return bool
     */
    public function getIsAdditional(): bool;
}
