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

namespace App\ImportDb\Alpha\Storage\ManyToOne\Persisted;

use App\Import\Card\Formatter\QuestionNumber\Formatter\QuestionNumberFormatterInterface;
use App\Import\Card\Formatter\QuestionNumber\Parser\QuestionNumberParserInterface;
use App\Import\Card\Formatter\QuestionNumber\QuestionNumberInterface;
use App\ImportDb\Alpha\Entity\AlphaCard;
use App\ImportDb\Alpha\ValueTrimmer\AlphaValueConverterInterface;
use App\Persistence\Entity\PolesianProgram\Subparagraph;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class SubparagraphStorage extends AbstractPersistedManyToOneEntityStorage
{
    /**
     * @var QuestionNumberFormatterInterface
     */
    private $questionNumberFormatter;

    /**
     * @var ParagraphStorage
     */
    private $paragraphStorage;

    /**
     * @param RegistryInterface                $doctrine
     * @param AlphaValueConverterInterface     $valueConverter
     * @param QuestionNumberParserInterface    $questionNumberParser
     * @param LoggerInterface                  $logger
     * @param QuestionNumberFormatterInterface $questionNumberFormatter
     * @param ParagraphStorage                 $paragraphStorage
     */
    public function __construct(
        RegistryInterface $doctrine,
        AlphaValueConverterInterface $valueConverter,
        QuestionNumberParserInterface $questionNumberParser,
        LoggerInterface $logger,
        QuestionNumberFormatterInterface $questionNumberFormatter,
        ParagraphStorage $paragraphStorage
    ) {
        parent::__construct($doctrine, $valueConverter, $questionNumberParser, $logger);
        $this->questionNumberFormatter = $questionNumberFormatter;
        $this->paragraphStorage = $paragraphStorage;
    }

    /**
     * @return string
     */
    protected function getEntityClass(): string
    {
        return Subparagraph::class;
    }

    /**
     * @param object|Subparagraph $entity
     *
     * @return string
     */
    protected function getEntityKey(object $entity): string
    {
        return $this->paragraphStorage->getEntityKey($entity->getParagraph()).$entity->getLetter();
    }

    /**
     * @param object|AlphaCard $alphaObject
     *
     * @return string|null
     */
    protected function getAlphaEntityKey(object $alphaObject): ?string
    {
        $subparagraphLetter = $this->getFixedSubparagraphLetter(
            $this->getQuestionNumber($alphaObject)
        );

        if (null === $subparagraphLetter) {
            return null;
        }

        return $this->paragraphStorage->getAlphaEntityKey($alphaObject).$subparagraphLetter;
    }

    /**
     * @param QuestionNumberInterface $questionNumber
     *
     * @return string|null
     */
    private function getFixedSubparagraphLetter(QuestionNumberInterface $questionNumber): ?string
    {
        $formattedQuestionNumber = $this->questionNumberFormatter->format($questionNumber);

        $knownErrorsMap = [
            'XIII.4в' => 'б',
        ];

        if (array_key_exists($formattedQuestionNumber, $knownErrorsMap)) {
            return $knownErrorsMap[$formattedQuestionNumber];
        }

        return $questionNumber->getSubparagraphLetter();
    }
}
