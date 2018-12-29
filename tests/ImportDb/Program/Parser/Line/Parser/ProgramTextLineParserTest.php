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

namespace App\Tests\ImportDb\Program\Parser\Line\Parser;

use App\ImportDb\Program\Parser\Line\Line\Paragraph\ParagraphLine;
use App\ImportDb\Program\Parser\Line\Line\Paragraph\ParagraphLineInterface;
use App\ImportDb\Program\Parser\Line\Line\Program\ProgramLine;
use App\ImportDb\Program\Parser\Line\Line\Program\ProgramLineInterface;
use App\ImportDb\Program\Parser\Line\Line\ProgramTextLineInterface;
use App\ImportDb\Program\Parser\Line\Line\Section\SectionLine;
use App\ImportDb\Program\Parser\Line\Line\Section\SectionLineInterface;
use App\ImportDb\Program\Parser\Line\Line\Subparagraph\SubparagraphLine;
use App\ImportDb\Program\Parser\Line\Line\Subparagraph\SubparagraphLineInterface;
use App\ImportDb\Program\Parser\Line\Parser\ProgramTextLineParser;
use PHPUnit\Framework\TestCase;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class ProgramTextLineParserTest extends TestCase
{
    /**
     * @var ProgramTextLineParser
     */
    private $programTextLineParser;

    protected function setUp(): void
    {
        $this->programTextLineParser = new ProgramTextLineParser();
    }

    /**
     * @dataProvider getSectionLines
     *
     * @param string               $programTextLine
     * @param SectionLineInterface $expectedSectionLine
     */
    public function testParseSectionLine(string $programTextLine, SectionLineInterface $expectedSectionLine): void
    {
        $parsedSectionLine = $this->programTextLineParser->parseProgramTextLine($programTextLine);

        self::assertInstanceOf(SectionLine::class, $parsedSectionLine);

        if ($parsedSectionLine instanceof SectionLine) {
            self::assertSame($expectedSectionLine->getName(), $parsedSectionLine->getName());
        }
    }

    /**
     * @dataProvider getProgramLines
     *
     * @param string               $programTextLine
     * @param ProgramLineInterface $expectedLine
     */
    public function testParseProgramLine(string $programTextLine, ProgramLineInterface $expectedLine): void
    {
        $parsedLine = $this->programTextLineParser->parseProgramTextLine($programTextLine);

        self::assertInstanceOf(ProgramLine::class, $parsedLine);

        if ($parsedLine instanceof ProgramLine) {
            self::assertSame($expectedLine->getNumber(), $parsedLine->getNumber());
            self::assertSame($expectedLine->getName(), $parsedLine->getName());
        }
    }

    /**
     * @dataProvider getParagraphLines
     *
     * @param string                 $programTextLine
     * @param ParagraphLineInterface $expectedLine
     */
    public function testParseParagraphLine(string $programTextLine, ParagraphLineInterface $expectedLine): void
    {
        $parsedLine = $this->programTextLineParser->parseProgramTextLine($programTextLine);

        self::assertInstanceOf(ParagraphLine::class, $parsedLine);

        if ($parsedLine instanceof ParagraphLine) {
            self::assertSame($expectedLine->getNumber(), $parsedLine->getNumber());
            self::assertSame($expectedLine->getTitle(), $parsedLine->getTitle());
            self::assertSame($expectedLine->getText(), $parsedLine->getText());
        }
    }

    /**
     * @dataProvider getSubparagraphLines
     *
     * @param string                    $programTextLine
     * @param SubparagraphLineInterface $expectedLine
     */
    public function testParseSubparagraphLine(string $programTextLine, SubparagraphLineInterface $expectedLine): void
    {
        $parsedLine = $this->programTextLineParser->parseProgramTextLine($programTextLine);

        self::assertInstanceOf(SubparagraphLine::class, $parsedLine);

        if ($parsedLine instanceof SubparagraphLine) {
            self::assertSame($expectedLine->getLetter(), $parsedLine->getLetter());
            self::assertSame($expectedLine->getText(), $parsedLine->getText());
        }
    }

    /**
     * @dataProvider getSectionLines
     * @dataProvider getProgramLines
     * @dataProvider getParagraphLines
     * @dataProvider getSubparagraphLines
     *
     * @param string                   $programTextLine
     * @param ProgramTextLineInterface $expectedLine
     */
    public function testParseWithObjectsEqualityCheck(string $programTextLine, ProgramTextLineInterface $expectedLine): void
    {
        $parsedLine = $this->programTextLineParser->parseProgramTextLine($programTextLine);

        self::assertSame(serialize($expectedLine), serialize($parsedLine));
    }

    /**
     * @return array[]
     */
    public function getSectionLines(): array
    {
        return [
            ['СЕМЕЙНАЯ ОБРЯДНОСТЬ', new SectionLine('СЕМЕЙНАЯ ОБРЯДНОСТЬ')],
        ];
    }

    /**
     * @return array[]
     */
    public function getProgramLines(): array
    {
        return [
            ['I. Свадьба', new ProgramLine('I', 'Свадьба')],
        ];
    }

    /**
     * @return array[]
     */
    public function getParagraphLines(): array
    {
        return [
            ['1. Канун свадьбы.', new ParagraphLine(1, 'Канун свадьбы', null)],
            ['2. Коровай. Как выглядел свадебный коровай? Зарисуйте его схематически сверху и и обозначьте стрелками каждую деталь. Укажите названия всех его деталей и украшений, а именно: подошвы; обода; фигурок из теста (птички, цветочки, шишки, завитушки, месяц, солнце, звезды и т.д.); узоров (крест, елочка, растительный орнамент, защипы на тесте и т.д.); предметов, которые запекаются в него (деньги, яйца и т.д.); кладутся сверху (венок, фигурки не из теста и т.д.); втыкаются в него (растительность, деревце, ветки, палки и т.д.).', new ParagraphLine(2, 'Коровай', 'Как выглядел свадебный коровай? Зарисуйте его схематически сверху и и обозначьте стрелками каждую деталь. Укажите названия всех его деталей и украшений, а именно: подошвы; обода; фигурок из теста (птички, цветочки, шишки, завитушки, месяц, солнце, звезды и т.д.); узоров (крест, елочка, растительный орнамент, защипы на тесте и т.д.); предметов, которые запекаются в него (деньги, яйца и т.д.); кладутся сверху (венок, фигурки не из теста и т.д.); втыкаются в него (растительность, деревце, ветки, палки и т.д.).')],
            ['3. Какой хлеб (хлебцы, булочки) пекли на свадьбе помимо коровая? Как он выглядел и как назывался (короваец, шишка, месяц, верч, дремы и т.д.)?', new ParagraphLine(3, null, 'Какой хлеб (хлебцы, булочки) пекли на свадьбе помимо коровая? Как он выглядел и как назывался (короваец, шишка, месяц, верч, дремы и т.д.)?')],
            ['5. Что делали, чтобы во время жатвы не болела спина: "качались" или кувыркались по земле с приговором (каким?); опоясывались перевяслом (из колосьев, травы "спорыш" и т.д.); затыкали что-либо за пояс (колосья, корешки колосьев, заячью капусту, веточку дуба и т.д.); "топтали" спину, т.е. мяли ее один другому ногами под матицей и т.д.?', new ParagraphLine(5, null, 'Что делали, чтобы во время жатвы не болела спина: "качались" или кувыркались по земле с приговором (каким?); опоясывались перевяслом (из колосьев, травы "спорыш" и т.д.); затыкали что-либо за пояс (колосья, корешки колосьев, заячью капусту, веточку дуба и т.д.); "топтали" спину, т.е. мяли ее один другому ногами под матицей и т.д.?')],
            ['6. Не приглашали ли к ужину мороз, дедов, волка или еще кого-нибудь? Когда? Запишите формулу приглашения.', new ParagraphLine(6, null, 'Не приглашали ли к ужину мороз, дедов, волка или еще кого-нибудь? Когда? Запишите формулу приглашения.')],
            ['33. Запишите легенду о том, что прежде колос начинался от самой земли. Говорится ли в ней о кошачьем или собачьем хлебе, который едят люди?', new ParagraphLine(33, null, 'Запишите легенду о том, что прежде колос начинался от самой земли. Говорится ли в ней о кошачьем или собачьем хлебе, который едят люди?')],
        ];
    }

    /**
     * @return array[]
     */
    public function getSubparagraphLines(): array
    {
        return [
            ['а) Как называлось приданое невесты?', new SubparagraphLine('а', 'Как называлось приданое невесты?')],
        ];
    }
}
