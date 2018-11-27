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

use App\Entity\Program\Paragraph;
use App\Import\Program\Question\Number\Parser\QuestionNumberParserInterface;
use App\ImportDb\Alpha\Entity\AlphaCard;
use App\ImportDb\Alpha\ValueTrimmer\AlphaValueConverterInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class ParagraphStorage extends AbstractManyToOneEntityStorage
{
    private const DUMMY_TITLE = null;

    private const DUMMY_TEXT = null;

    /**
     * @var ProgramStorage
     */
    private $programStorage;

    /**
     * @param RegistryInterface             $doctrine
     * @param AlphaValueConverterInterface  $valueConverter
     * @param QuestionNumberParserInterface $questionNumberParser
     * @param LoggerInterface               $logger
     * @param ProgramStorage                $programStorage
     */
    public function __construct(
        RegistryInterface $doctrine,
        AlphaValueConverterInterface $valueConverter,
        QuestionNumberParserInterface $questionNumberParser,
        LoggerInterface $logger,
        ProgramStorage $programStorage
    ) {
        parent::__construct($doctrine, $valueConverter, $questionNumberParser, $logger);
        $this->programStorage = $programStorage;
    }

    /**
     * @param AlphaCard $alphaCard
     *
     * @return string|null
     */
    protected function getAlphaEntityKey(AlphaCard $alphaCard): ?string
    {
        $paragraphNumber = $this->getQuestionNumber($alphaCard)->getParagraphNumber();

        if (null === $paragraphNumber) {
            return null;
        }

        return $this->programStorage->getAlphaEntityKey($alphaCard).$paragraphNumber;
    }

    /**
     * @param AlphaCard $alphaCard
     *
     * @return Paragraph
     */
    protected function createEntity(AlphaCard $alphaCard): object
    {
        return (new Paragraph())
            ->setProgram($this->programStorage->getEntity($alphaCard))
            ->setNumber($this->getQuestionNumber($alphaCard)->getParagraphNumber())
            ->setTitle(self::DUMMY_TITLE)
            ->setText(self::DUMMY_TEXT)
        ;
    }
}