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

use App\Import\Card\Formatter\QuestionNumber\Parser\QuestionNumberParserInterface;
use App\ImportDb\Alpha\Entity\AlphaCard;
use App\ImportDb\Alpha\ValueTrimmer\AlphaValueConverterInterface;
use App\Persistence\Entity\PolesianProgram\Paragraph;
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
     * @param object|AlphaCard $alphaObject
     *
     * @return string|null
     */
    protected function getAlphaEntityKey(object $alphaObject): ?string
    {
        $paragraphNumber = $this->getQuestionNumber($alphaObject)->getParagraphNumber();

        if (null === $paragraphNumber) {
            return null;
        }

        return $this->programStorage->getAlphaEntityKey($alphaObject).$paragraphNumber;
    }

    /**
     * @param object|AlphaCard $alphaObject
     *
     * @return Paragraph
     */
    protected function createEntity(object $alphaObject): object
    {
        return (new Paragraph())
            ->setProgram($this->programStorage->getEntity($alphaObject))
            ->setNumber($this->getQuestionNumber($alphaObject)->getParagraphNumber())
            ->setTitle(self::DUMMY_TITLE)
            ->setText(self::DUMMY_TEXT)
        ;
    }
}
