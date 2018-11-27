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

namespace App\Import\Program\Question\Number\Formatter;

use App\Entity\Question;
use App\Import\Program\Question\Number\QuestionNumber;
use App\Import\Program\Question\Number\QuestionNumberInterface;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
interface QuestionNumberFormatterInterface
{
    /**
     * @param QuestionNumberInterface $questionNumber
     *
     * @return string
     */
    public function format(QuestionNumberInterface $questionNumber): string;

    /**
     * @param Question $question
     *
     * @return string
     */
    public function formatQuestion(Question $question): string;
}
