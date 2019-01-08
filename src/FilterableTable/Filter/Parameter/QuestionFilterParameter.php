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

use App\Entity\Card\Question;
use App\Import\Card\Formatter\QuestionNumber\Formatter\QuestionNumberFormatterInterface;
use App\Import\Card\Formatter\QuestionNumber\Parser\QuestionNumberParserInterface;
use App\Import\Card\Formatter\QuestionNumber\QuestionNumberInterface;
use App\Repository\Card\QuestionRepository;
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
     * @param QuestionNumberFormatterInterface $questionNumberFormatter
     * @param QuestionNumberParserInterface    $questionNumberParser
     * @param QuestionRepository               $questionRepository
     */
    public function __construct(
        QuestionNumberFormatterInterface $questionNumberFormatter,
        QuestionNumberParserInterface $questionNumberParser,
        QuestionRepository $questionRepository
    ) {
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
     * @param array        $formData
     * @param string       $entityAlias
     *
     * @return string|null
     */
    public function buildWhereExpression(QueryBuilder $queryBuilder, array $formData, string $entityAlias): ?string
    {
        $formattedQuestionNumbers = $formData[$this->getQueryParameterName()];

        if (0 === \count($formattedQuestionNumbers)) {
            return null;
        }

        $parseQuestionNumber = function (string $formattedQuestionNumber): QuestionNumberInterface {
            return $this->questionNumberParser->parseQuestionNumber($formattedQuestionNumber);
        };

        $questionNumbers = array_map($parseQuestionNumber, $formattedQuestionNumbers);

        $queryBuilder
            ->innerJoin(
                $entityAlias.'.questions',
                $questionAlias = $this->createAlias(static::class, 'question')
            )
            ->innerJoin(
                $questionAlias.'.program',
                $programAlias = $this->createAlias(static::class, 'program')
            )
            ->innerJoin(
                $questionAlias.'.paragraph',
                $paragraphAlias = $this->createAlias(static::class, 'paragraph')
            )
            ->innerJoin(
                $questionAlias.'.subparagraph',
                $subparagraphAlias = $this->createAlias(static::class, 'subparagraph')
            )
        ;

        $orWhereClauses = [];

        foreach ($questionNumbers as $index => $questionNumber) {
            $queryBuilder->setParameter(
                $numberNumberParameter = $this->createParameter(
                    $programNumberFieldAlias = $programAlias.'.number',
                    $index
                ),
                $questionNumber->getProgramNumber()
            );

            $queryBuilder->setParameter(
                $paragraphNumberParameter = $this->createParameter(
                    $paragraphNumberFieldAlias = $paragraphAlias.'.number',
                    $index
                ),
                $questionNumber->getParagraphNumber()
            );

            $queryBuilder->setParameter(
                $subparagraphLetterParameter = $this->createParameter(
                    $subparagraphLetterFieldAlias = $subparagraphAlias.'.letter',
                    $index
                ),
                $questionNumber->getSubparagraphLetter()
            );

            $queryBuilder->setParameter(
                $questionIsAdditionalParameter = $this->createParameter(
                    $questionIsAdditionalFieldAlias = $questionAlias.'.isAdditional',
                    $index
                ),
                $questionNumber->getIsAdditional()
            );

            $orWhereClauses[] = (string) $queryBuilder->expr()->andX(
                $queryBuilder->expr()->eq($programNumberFieldAlias, $numberNumberParameter),
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

    /**
     * @param string $className
     * @param string $alias
     *
     * @return string
     */
    private function createAlias(string $className, string $alias): string
    {
        $classNameParts = explode('\\', $className);

        $classShortName = array_pop($classNameParts);

        return strtolower($classShortName.'_'.$alias);
    }

    /**
     * @param string $fieldAlias
     * @param int    $index
     *
     * @return string
     */
    private function createParameter(string $fieldAlias, int $index): string
    {
        return ':'.str_replace('.', '_', $fieldAlias).'_'.$index;
    }
}
