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

namespace App\ImportDb\Program;

use App\ImportDb\Program\Parser\Line\Line\Paragraph\ParagraphLineInterface;
use App\ImportDb\Program\Parser\Line\Line\Program\ProgramLineInterface;
use App\ImportDb\Program\Parser\Line\Line\Section\SectionLineInterface;
use App\ImportDb\Program\Parser\Line\Line\Subparagraph\SubparagraphLineInterface;
use App\ImportDb\Program\Parser\Line\Parser\ProgramTextLineParserInterface;
use App\Persistence\Entity\PolesianProgram\Paragraph;
use App\Persistence\Entity\PolesianProgram\Program;
use App\Persistence\Entity\PolesianProgram\Section;
use App\Persistence\Entity\PolesianProgram\Subparagraph;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\ORMException;
use InvalidArgumentException;
use LogicException;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class ProgramImporter implements ProgramImporterInterface
{
    private $programTextLineParser;

    private $registry;

    public function __construct(
        ProgramTextLineParserInterface $programTextLineParser,
        ManagerRegistry $registry
    ) {
        $this->programTextLineParser = $programTextLineParser;
        $this->registry = $registry;
    }

    /**
     * @throws InvalidArgumentException
     * @throws ORMException
     */
    public function importProgram(string $pathToSourceFile): void
    {
        $programTextLines = explode(\PHP_EOL, file_get_contents($pathToSourceFile));

        $currentSection = null;
        $currentProgram = null;
        $currentParagraph = null;

        foreach ($programTextLines as $programTextLine) {
            $parsedLine = $this->programTextLineParser->parseProgramTextLine($programTextLine);

            if ($parsedLine instanceof SectionLineInterface) {
                $currentSection = $this->registry
                    ->getRepository(Section::class)
                    ->createSection($parsedLine->getName());
                $currentProgram = null;
                $currentParagraph = null;
            } elseif ($parsedLine instanceof ProgramLineInterface) {
                if (null === $currentSection) {
                    throw $this->createException(Program::class, Section::class);
                }

                $currentProgram = $this->registry
                    ->getRepository(Program::class)
                    ->createProgram($parsedLine->getNumber(), $parsedLine->getName(), $currentSection)
                ;
                $currentParagraph = null;
            } elseif ($parsedLine instanceof ParagraphLineInterface) {
                if (null === $currentProgram) {
                    throw $this->createException(Paragraph::class, Program::class);
                }

                $currentParagraph = $this->registry
                    ->getRepository(Paragraph::class)
                    ->createParagraph(
                        $parsedLine->getNumber(),
                        $parsedLine->getTitle(),
                        $parsedLine->getText(),
                        $currentProgram
                    )
                ;
            } elseif ($parsedLine instanceof SubparagraphLineInterface) {
                if (null === $currentParagraph) {
                    throw $this->createException(Subparagraph::class, Paragraph::class);
                }

                $this->registry
                    ->getRepository(Subparagraph::class)
                    ->createSubparagraph($parsedLine->getLetter(), $parsedLine->getText(), $currentParagraph)
                ;
            } else {
                throw new InvalidArgumentException('Unknown program text line parse result');
            }
        }

        $this->registry->getManager()->flush();
    }

    private function createException(string $currentInstanceClass, string $neededInstanceClass): LogicException
    {
        return new LogicException(
            sprintf(
                'Cannot create "%s" instance: needed "%s" instance is not created',
                $currentInstanceClass,
                $neededInstanceClass
            )
        );
    }
}
