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

namespace App\Tests\Formatter\QuestionNumber\Formatter;

use App\Formatter\QuestionNumber\Converter\QuestionToQuestionNumberConverterInterface;
use App\Formatter\QuestionNumber\Formatter\QuestionNumberFormatter;
use App\Formatter\QuestionNumber\QuestionNumber;
use App\Formatter\QuestionNumber\QuestionNumberInterface;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Throwable;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class QuestionNumberFormatterTest extends TestCase
{
    /**
     * @var QuestionNumberFormatter
     */
    private $questionNumberFormatter;

    protected function setUp(): void
    {
        $this->questionNumberFormatter = new QuestionNumberFormatter(
            $this->createMock(QuestionToQuestionNumberConverterInterface::class)
        );
    }

    /**
     * @dataProvider getQuestionNumbers
     */
    public function testFormat(QuestionNumberInterface $questionNumber, string $expectedFormattedQuestionNumber): void
    {
        $formattedQuestionNumber = $this->questionNumberFormatter->format($questionNumber);

        $this->assertSame($expectedFormattedQuestionNumber, $formattedQuestionNumber);
    }

    /**
     * @dataProvider getInvalidQuestionNumbers
     */
    public function testFormatWithInvalidNumbers(QuestionNumberInterface $questionNumber, Throwable $throwable): void
    {
        $this->expectException(\get_class($throwable));
        $this->expectExceptionMessage($throwable->getMessage());

        $this->questionNumberFormatter->format($questionNumber);
    }

    /**
     * @return array[]
     */
    public function getQuestionNumbers(): array
    {
        return [
            [new QuestionNumber('I', null, null, false), 'I'],
            [new QuestionNumber('I', null, null, true), 'I.доп'],
            [new QuestionNumber('I', 1, null, false), 'I.1'],
            [new QuestionNumber('I', 1, null, true), 'I.1.доп'],
            [new QuestionNumber('I', 1, 'а', false), 'I.1а'],
            [new QuestionNumber('I', 1, 'а', true), 'I.1а.доп'],
        ];
    }

    /**
     * @return array[]
     */
    public function getInvalidQuestionNumbers(): array
    {
        return [
            [new QuestionNumber('I', null, 'а', false), new InvalidArgumentException('Cannot format question number: subparagraph is set while paragraph is null')],
            [new QuestionNumber('I', null, 'а', true), new InvalidArgumentException('Cannot format question number: subparagraph is set while paragraph is null')],
        ];
    }
}
