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
use App\ImportDb\Alpha\Storage\ManyToOne\Persisted\ParagraphStorage;
use App\ImportDb\Alpha\Storage\ManyToOne\Persisted\ProgramStorage;
use App\ImportDb\Alpha\Storage\ManyToOne\Persisted\SubparagraphStorage;
use App\ImportDb\Alpha\ValueTrimmer\AlphaValueConverterInterface;
use App\Persistence\Entity\Card\Question;
use Doctrine\Common\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class QuestionStorage extends AbstractManyToOneEntityStorage
{
    private $programStorage;

    private $paragraphStorage;

    private $subparagraphStorage;

    public function __construct(
        ManagerRegistry $doctrine,
        AlphaValueConverterInterface $valueConverter,
        QuestionNumberParserInterface $questionNumberParser,
        LoggerInterface $logger,
        ProgramStorage $programStorage,
        ParagraphStorage $paragraphStorage,
        SubparagraphStorage $subparagraphStorage
    ) {
        parent::__construct($doctrine, $valueConverter, $questionNumberParser, $logger);
        $this->programStorage = $programStorage;
        $this->paragraphStorage = $paragraphStorage;
        $this->subparagraphStorage = $subparagraphStorage;
    }

    /**
     * @param object|AlphaCard $alphaObject
     */
    protected function getAlphaEntityKey(object $alphaObject): ?string
    {
        $programKey = $this->programStorage->getAlphaEntityKey($alphaObject);
        $paragraphKey = $this->paragraphStorage->getAlphaEntityKey($alphaObject);
        $subparagraphKey = $this->subparagraphStorage->getAlphaEntityKey($alphaObject);
        $isAdditionalKey = $this->getQuestionNumber($alphaObject)->getIsAdditional() ? '1' : '0';

        return $programKey.$paragraphKey.$subparagraphKey.$isAdditionalKey;
    }

    /**
     * @param object|AlphaCard $alphaObject
     *
     * @return Question
     */
    protected function createEntity(object $alphaObject): object
    {
        return (new Question())
            ->setProgram($this->programStorage->getEntity($alphaObject))
            ->setParagraph($this->paragraphStorage->getEntity($alphaObject))
            ->setSubparagraph($this->subparagraphStorage->getEntity($alphaObject))
            ->setIsAdditional($this->getQuestionNumber($alphaObject)->getIsAdditional())
        ;
    }
}
