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

namespace App\FilterableTable\Filter\Parameter;

use App\Formatter\QuestionNumber\Formatter\QuestionNumberFormatterInterface;
use App\Formatter\QuestionNumber\Parser\QuestionNumberParserInterface;
use App\Formatter\QuestionNumber\QuestionNumberInterface;
use App\Persistence\Entity\Card\Question;
use App\Persistence\Repository\Card\QuestionRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Romans\Filter\RomanToInt;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Vyfony\Bundle\FilterableTableBundle\Filter\Configurator\Parameter\ExpressionBuilderInterface;
use Vyfony\Bundle\FilterableTableBundle\Filter\Configurator\Parameter\FilterParameterInterface;
use Vyfony\Bundle\FilterableTableBundle\Persistence\QueryBuilder\Alias\AliasFactoryInterface;
use Vyfony\Bundle\FilterableTableBundle\Persistence\QueryBuilder\Parameter\ParameterFactoryInterface;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class QuestionFilterParameter implements FilterParameterInterface, ExpressionBuilderInterface
{
    private $aliasFactory;

    private $parameterFactory;

    private $questionNumberFormatter;

    private $questionNumberParser;

    private $romanToIntConverter;

    private $questionRepository;

    public function __construct(
        AliasFactoryInterface $aliasFactory,
        ParameterFactoryInterface $parameterFactory,
        QuestionNumberFormatterInterface $questionNumberFormatter,
        QuestionNumberParserInterface $questionNumberParser,
        RomanToInt $romanToIntConverter,
        QuestionRepository $questionRepository
    ) {
        $this->aliasFactory = $aliasFactory;
        $this->parameterFactory = $parameterFactory;
        $this->questionNumberFormatter = $questionNumberFormatter;
        $this->questionNumberParser = $questionNumberParser;
        $this->romanToIntConverter = $romanToIntConverter;
        $this->questionRepository = $questionRepository;
    }

    public function getQueryParameterName(): string
    {
        return 'questions';
    }

    public function getType(): string
    {
        return ChoiceType::class;
    }

    public function getOptions(EntityManager $entityManager): array
    {
        return [
            'label' => 'controller.card.list.filter.question',
            'attr' => [
                'class' => '',
                'data-vyfony-filterable-table-filter-parameter' => true,
            ],
            'expanded' => false,
            'multiple' => true,
            'choices' => $this->createChoices(),
        ];
    }

    /**
     * @param mixed $formData
     */
    public function buildWhereExpression(QueryBuilder $queryBuilder, $formData, string $entityAlias): ?string
    {
        $formattedQuestionNumbers = $formData;

        if (0 === \count($formattedQuestionNumbers)) {
            return null;
        }

        $queryBuilder
            ->innerJoin(
                $entityAlias.'.questions',
                $questionAlias = $this->aliasFactory->createAlias(static::class, 'question')
            )
            ->innerJoin(
                $questionAlias.'.program',
                $programAlias = $this->aliasFactory->createAlias(static::class, 'program')
            )
            ->innerJoin(
                $questionAlias.'.paragraph',
                $paragraphAlias = $this->aliasFactory->createAlias(static::class, 'paragraph')
            )
            ->innerJoin(
                $questionAlias.'.subparagraph',
                $subparagraphAlias = $this->aliasFactory->createAlias(static::class, 'subparagraph')
            )
        ;

        $parseQuestionNumber = function (string $formattedQuestionNumber): QuestionNumberInterface {
            return $this->questionNumberParser->parseQuestionNumber($formattedQuestionNumber);
        };

        $questionNumbers = array_map($parseQuestionNumber, $formattedQuestionNumbers);

        $orWhereClauses = [];

        foreach ($questionNumbers as $index => $questionNumber) {
            $queryBuilder->setParameter(
                $programNumberParameter = $this->parameterFactory->createParameter(
                    $programNumberFieldAlias = $programAlias.'.number',
                    $index
                ),
                $questionNumber->getProgramNumber()
            );

            $queryBuilder->setParameter(
                $paragraphNumberParameter = $this->parameterFactory->createParameter(
                    $paragraphNumberFieldAlias = $paragraphAlias.'.number',
                    $index
                ),
                $questionNumber->getParagraphNumber()
            );

            $queryBuilder->setParameter(
                $subparagraphLetterParameter = $this->parameterFactory->createParameter(
                    $subparagraphLetterFieldAlias = $subparagraphAlias.'.letter',
                    $index
                ),
                $questionNumber->getSubparagraphLetter()
            );

            $queryBuilder->setParameter(
                $questionIsAdditionalParameter = $this->parameterFactory->createParameter(
                    $questionIsAdditionalFieldAlias = $questionAlias.'.isAdditional',
                    $index
                ),
                $questionNumber->getIsAdditional()
            );

            $orWhereClauses[] = (string) $queryBuilder->expr()->andX(
                $queryBuilder->expr()->eq($programNumberFieldAlias, $programNumberParameter),
                $queryBuilder->expr()->eq($paragraphNumberFieldAlias, $paragraphNumberParameter),
                $queryBuilder->expr()->eq($subparagraphLetterFieldAlias, $subparagraphLetterParameter),
                $queryBuilder->expr()->eq($questionIsAdditionalFieldAlias, $questionIsAdditionalParameter)
            );
        }

        return (string) $queryBuilder->expr()->orX(...$orWhereClauses);
    }

    private function createChoices(): array
    {
        $questions = $this->questionRepository->findAll();

        usort($questions, function (Question $a, Question $b): int {
            $aProgramNumber = $this->romanToIntConverter->filter($a->getProgram()->getNumber());
            $bProgramNumber = $this->romanToIntConverter->filter($b->getProgram()->getNumber());

            if ($aProgramNumber === $bProgramNumber) {
                $aParagraph = $a->getParagraph();
                $bParagraph = $b->getParagraph();

                if (null === $aParagraph && null === $bParagraph) {
                    return 0;
                }

                if (null !== $aParagraph && null === $bParagraph) {
                    return 1;
                }

                if (null === $aParagraph && null !== $bParagraph) {
                    return -1;
                }

                $aParagraphNumber = $aParagraph->getNumber();
                $bParagraphNumber = $bParagraph->getNumber();

                if ($aParagraphNumber === $bParagraphNumber) {
                    $aSubparagraph = $a->getSubparagraph();
                    $bSubparagraph = $b->getSubparagraph();

                    if (null === $aSubparagraph && null === $bSubparagraph) {
                        return 0;
                    }

                    if (null !== $aSubparagraph && null === $bSubparagraph) {
                        return 1;
                    }

                    if (null === $aSubparagraph && null !== $bSubparagraph) {
                        return -1;
                    }

                    $aSubparagraphLetter = $aSubparagraph->getLetter();
                    $bSubparagraphLetter = $bSubparagraph->getLetter();

                    return strnatcmp($aSubparagraphLetter, $bSubparagraphLetter);
                }

                return $aParagraphNumber < $bParagraphNumber ? -1 : 1;
            }

            return $aProgramNumber < $bProgramNumber ? -1 : 1;
        });

        $formatQuestion = function (Question $question): string {
            return $this->questionNumberFormatter->formatQuestion($question);
        };

        $formattedQuestions = array_map($formatQuestion, $questions);

        return array_combine($formattedQuestions, $formattedQuestions);
    }
}
