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

namespace App\ImportDb\Alpha\Storage\ManyToOne;

use App\Formatter\QuestionNumber\Parser\QuestionNumberParserInterface;
use App\ImportDb\Alpha\Entity\AlphaCard;
use App\ImportDb\Alpha\ValueTrimmer\AlphaValueConverterInterface;
use App\Persistence\Entity\PolesianProgram\Program;
use Doctrine\Common\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class ProgramStorage extends AbstractManyToOneEntityStorage
{
    private $sectionStorage;

    public function __construct(
        ManagerRegistry $doctrine,
        AlphaValueConverterInterface $valueConverter,
        QuestionNumberParserInterface $questionNumberParser,
        LoggerInterface $logger,
        SectionStorage $sectionStorage
    ) {
        parent::__construct($doctrine, $valueConverter, $questionNumberParser, $logger);
        $this->sectionStorage = $sectionStorage;
    }

    /**
     * @param object|AlphaCard $alphaObject
     */
    protected function getAlphaEntityKey(object $alphaObject): ?string
    {
        return $this->valueConverter->getTrimmed($alphaObject->getNprog());
    }

    /**
     * @param object|AlphaCard $alphaObject
     *
     * @return Program
     */
    protected function createEntity(object $alphaObject): object
    {
        return (new Program())
            ->setSection($this->sectionStorage->getEntity($alphaObject))
            ->setNumber($this->valueConverter->getTrimmed($alphaObject->getNprog()))
        ;
    }
}
