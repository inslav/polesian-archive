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

namespace App\ImportDb\Alpha\SkippedCard;

use JsonSerializable;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
interface SkippedAlphaCardInterface extends JsonSerializable
{
    /**
     * @return string
     */
    public function getSpvnkey(): string;

    /**
     * @return string|null
     */
    public function getSelokey(): ?string;

    /**
     * @return string
     */
    public function getHutor(): string;

    /**
     * @return string
     */
    public function getGod(): string;

    /**
     * @return string|null
     */
    public function getSezon(): ?string;

    /**
     * @return string
     */
    public function getNprog(): string;

    /**
     * @return string
     */
    public function getNvopr(): string;

    /**
     * @return string|null
     */
    public function getOtv(): ?string;

    /**
     * @return string
     */
    public function getDtext(): string;

    /**
     * @return string
     */
    public function getOptext(): string;

    /**
     * @return string
     */
    public function getNum(): string;
}
