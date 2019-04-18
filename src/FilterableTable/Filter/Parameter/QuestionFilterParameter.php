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

use App\Import\Card\Formatter\QuestionNumber\Formatter\QuestionNumberFormatterInterface;
use App\Import\Card\Formatter\QuestionNumber\Parser\QuestionNumberParserInterface;
use App\Import\Card\Formatter\QuestionNumber\QuestionNumberInterface;
use App\Persistence\Entity\Card\Question;
use App\Persistence\QueryBuilder\Alias\AliasFactoryInterface;
use App\Persistence\QueryBuilder\Parameter\ParameterFactoryInterface;
use App\Persistence\Repository\Card\QuestionRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Vyfony\Bundle\FilterableTableBundle\Filter\Configurator\Parameter\ExpressionBuilderInterface;
use Vyfony\Bundle\FilterableTableBundle\Filter\Configurator\Parameter\FilterParameterInterface;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class QuestionFilterParameter implements FilterParameterInterface, ExpressionBuilderInterface
{
    /**
     * @var AliasFactoryInterface
     */
    private $aliasFactory;

    /**
     * @var ParameterFactoryInterface
     */
    private $parameterFactory;

    /**
     * @var QuestionNumberFormatterInterface
     */
    private $questionNumberFormatter;

    /**
     * @var QuestionNumberParserInterface
     */
    private $questionNumberParser;

    /**
     * @var QuestionRepository
     */
    private $questionRepository;

    /**
     * @param AliasFactoryInterface            $aliasFactory
     * @param ParameterFactoryInterface        $parameterFactory
     * @param QuestionNumberFormatterInterface $questionNumberFormatter
     * @param QuestionNumberParserInterface    $questionNumberParser
     * @param QuestionRepository               $questionRepository
     */
    public function __construct(
        AliasFactoryInterface $aliasFactory,
        ParameterFactoryInterface $parameterFactory,
        QuestionNumberFormatterInterface $questionNumberFormatter,
        QuestionNumberParserInterface $questionNumberParser,
        QuestionRepository $questionRepository
    ) {
        $this->aliasFactory = $aliasFactory;
        $this->parameterFactory = $parameterFactory;
        $this->questionNumberFormatter = $questionNumberFormatter;
        $this->questionNumberParser = $questionNumberParser;
        $this->questionRepository = $questionRepository;
    }

    /**
     * @return string
     */
    public function getQueryParameterName(): string
    {
        return 'questions';
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return ChoiceType::class;
    }

    /**
     * @param EntityManager $entityManager
     *
     * @return array
     */
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
     * @param QueryBuilder $queryBuilder
     * @param mixed        $formData
     * @param string       $entityAlias
     *
     * @return string|null
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

    /**
     * @return array
     */
    private function createChoices(): array
    {
        $formatQuestion = function (Question $question): string {
            return $this->questionNumberFormatter->formatQuestion($question);
        };

        $formattedQuestions = array_map($formatQuestion, $this->questionRepository->findAll());

        usort($formattedQuestions, function (string $a, string $b): int {
            return strnatcmp($a, $b);
        });

        return array_combine($formattedQuestions, $formattedQuestions);
    }
}
