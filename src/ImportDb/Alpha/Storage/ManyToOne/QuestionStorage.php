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

use App\Entity\Question;
use App\Import\Program\Question\Number\Parser\QuestionNumberParserInterface;
use App\ImportDb\Alpha\Entity\AlphaCard;
use App\ImportDb\Alpha\Storage\ManyToOne\Persisted\ParagraphStorage;
use App\ImportDb\Alpha\Storage\ManyToOne\Persisted\ProgramStorage;
use App\ImportDb\Alpha\Storage\ManyToOne\Persisted\SubparagraphStorage;
use App\ImportDb\Alpha\ValueTrimmer\AlphaValueConverterInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class QuestionStorage extends AbstractManyToOneEntityStorage
{
    /**
     * @var ProgramStorage
     */
    private $programStorage;

    /**
     * @var ParagraphStorage
     */
    private $paragraphStorage;

    /**
     * @var SubparagraphStorage
     */
    private $subparagraphStorage;

    /**
     * @param RegistryInterface             $doctrine
     * @param AlphaValueConverterInterface  $valueConverter
     * @param QuestionNumberParserInterface $questionNumberParser
     * @param LoggerInterface               $logger
     * @param ProgramStorage                $programStorage
     * @param ParagraphStorage              $paragraphStorage
     * @param SubparagraphStorage           $subparagraphStorage
     */
    public function __construct(
        RegistryInterface $doctrine,
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
     * @param AlphaCard $alphaCard
     *
     * @return string|null
     */
    protected function getAlphaEntityKey(AlphaCard $alphaCard): ?string
    {
        $programKey = $this->programStorage->getAlphaEntityKey($alphaCard);
        $paragraphKey = $this->paragraphStorage->getAlphaEntityKey($alphaCard);
        $subparagraphKey = $this->subparagraphStorage->getAlphaEntityKey($alphaCard);
        $isAdditionalKey = $this->getQuestionNumber($alphaCard)->getIsAdditional() ? '1' : '0';

        return $programKey.$paragraphKey.$subparagraphKey.$isAdditionalKey;
    }

    /**
     * @param AlphaCard $alphaCard
     *
     * @return Question
     */
    protected function createEntity(AlphaCard $alphaCard): object
    {
        return (new Question())
            ->setProgram($this->programStorage->getEntity($alphaCard))
            ->setParagraph($this->paragraphStorage->getEntity($alphaCard))
            ->setSubparagraph($this->subparagraphStorage->getEntity($alphaCard))
            ->setIsAdditional($this->getQuestionNumber($alphaCard)->getIsAdditional())
        ;
    }
}
