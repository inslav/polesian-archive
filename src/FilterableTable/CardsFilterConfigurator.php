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

namespace App\FilterableTable;

use App\Entity\Card;
use App\Entity\Collector;
use App\Entity\Keyword;
use App\Entity\Program;
use App\Entity\Question;
use App\Entity\Term;
use App\Entity\Village;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Vyfony\Bundle\FilterableTableBundle\Filter\Configurator\AbstractFilterConfigurator;
use Vyfony\Bundle\FilterableTableBundle\Filter\Configurator\Parameter\CustomChoiceParameter;
use Vyfony\Bundle\FilterableTableBundle\Filter\Configurator\Parameter\EntityChoice\EntityChoiceParameter;
use Vyfony\Bundle\FilterableTableBundle\Filter\Configurator\Parameter\EntityChoice\JoinedEntityChoiceParameter;
use Vyfony\Bundle\FilterableTableBundle\Filter\Configurator\Parameter\FilterParameterInterface;
use Vyfony\Bundle\FilterableTableBundle\Filter\Configurator\Parameter\IntegerChoiceParameter;
use Vyfony\Bundle\FilterableTableBundle\Filter\Configurator\Parameter\Table\RadioColumnChoiceTableParameter;
use Vyfony\Bundle\FilterableTableBundle\Filter\Configurator\Parameter\Table\RadioOption\RadioOption;
use Vyfony\Bundle\FilterableTableBundle\Filter\Configurator\Parameter\Table\TableParameterInterface;
use Vyfony\Bundle\FilterableTableBundle\Filter\Configurator\Restriction\FilterRestrictionInterface;
use Vyfony\Bundle\FilterableTableBundle\Table\Metadata\Column\ColumnMetadata;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class CardsFilterConfigurator extends AbstractFilterConfigurator
{
    /**
     * @return array
     */
    public function createDefaults(): array
    {
        return [
            'label_attr' => ['class' => ''],
            'translation_domain' => 'messages',
            'attr' => ['class' => 'row'],
            'method' => 'GET',
            'csrf_protection' => false,
            'required' => false,
        ];
    }

    /**
     * @return array
     */
    public function createSubmitButtonOptions(): array
    {
        return [
            'attr' => ['class' => 'btn btn-default'],
            'label' => 'controller.card.list.filter.submitButton',
        ];
    }

    /**
     * @return array
     */
    public function createResetButtonOptions(): array
    {
        return [
            'attr' => ['class' => 'btn btn-default'],
            'label' => 'controller.card.list.filter.resetButton',
        ];
    }

    /**
     * @return FilterRestrictionInterface[]
     */
    protected function createFilterRestrictions(): array
    {
        return [
        ];
    }

    /**
     * @return FilterParameterInterface[]
     */
    protected function createFilterParameters(): array
    {
        return [
            (new CustomChoiceParameter())
                ->setChoicesFactory(function (string $queryParameterName, EntityManager $entityManager): array {
                    $entityCollection = $entityManager->getRepository(Program::class)->findAll();

                    usort($entityCollection, function (Program $a, Program $b): int {
                        return strnatcmp($a->getNumber(), $b->getNumber());
                    });

                    $choiceValueFactory = function (Program $program): int {
                        return $program->getId();
                    };

                    $choiceLabelFactory = function (Program $program): string {
                        return $program->getNumber();
                    };

                    $values = array_map($choiceValueFactory, $entityCollection);
                    $labels = array_map($choiceLabelFactory, $entityCollection);

                    return array_combine($labels, $values);
                })
                ->setQueryFactory(
                    function (
                        string $queryParameterName,
                        QueryBuilder $queryBuilder,
                        array $formData,
                        string $entityAlias
                    ): ?string {
                        if (0 === \count($formData[$queryParameterName])) {
                            return null;
                        }

                        $questionAlias = 'question';
                        $programAlias = 'program';

                        $queryBuilder
                            ->innerJoin($entityAlias.'.questions', $questionAlias)
                            ->innerJoin($questionAlias.'.program', $programAlias)
                        ;

                        return (string) $queryBuilder->expr()->in($programAlias.'.id', $formData[$queryParameterName]);
                    }
                )
                ->setQueryParameterName('program')
                ->setLabel('controller.card.list.filter.program'),
            (new JoinedEntityChoiceParameter())
                ->setClass(Question::class)
                ->setIsExpanded(false)
                ->setChoiceLabelFactory(function (Question $question): string {
                    return $question->getProgram()->getNumber().'.'.$question->getNumber();
                })
                ->setSortValuesCallback(function (EntityRepository $repository): QueryBuilder {
                    $questionAlias = 'question';
                    $programAlias = 'program';

                    return $repository
                        ->createQueryBuilder($questionAlias)
                        ->join($questionAlias.'.program', $programAlias)
                        ->orderBy($programAlias.'.number', 'ASC')
                        ->addOrderBy($questionAlias.'.number', 'ASC');
                })
                ->setQueryParameterName('questions')
                ->setLabel('controller.card.list.filter.question'),
            (new EntityChoiceParameter())
                ->setClass(Village::class)
                ->setIsExpanded(false)
                ->setChoiceLabel('name')
                ->sortValues('name')
                ->setQueryParameterName('village')
                ->setLabel('controller.card.list.filter.village'),
            (new JoinedEntityChoiceParameter())
                ->setClass(Keyword::class)
                ->setIsExpanded(false)
                ->setChoiceLabel('name')
                ->sortValues('name')
                ->setQueryParameterName('keywords')
                ->setLabel('controller.card.list.filter.keywords'),
            (new JoinedEntityChoiceParameter())
                ->setClass(Term::class)
                ->setIsExpanded(false)
                ->setChoiceLabel('name')
                ->sortValues('name')
                ->setQueryParameterName('terms')
                ->setLabel('controller.card.list.filter.terms'),
            (new JoinedEntityChoiceParameter())
                ->setClass(Collector::class)
                ->setIsExpanded(false)
                ->setChoiceLabel('name')
                ->sortValues('name')
                ->setQueryParameterName('collectors')
                ->setLabel('controller.card.list.filter.collectors'),
            (new IntegerChoiceParameter())
                ->setClass(Card::class)
                ->setLabel('controller.card.list.filter.year')
                ->setQueryParameterName('year'),
        ];
    }

    /**
     * @return TableParameterInterface[]
     */
    protected function createTableParameters(): array
    {
        return [
            (new RadioColumnChoiceTableParameter())
                ->addRadioOption(
                    (new RadioOption())
                        ->setName('description')
                        ->setLabel('controller.card.list.filter.dataColumn.option.description')
                        ->setColumnMetadata(
                            (new ColumnMetadata())
                                ->setName('description')
                                ->setLabel('controller.card.list.table.description')
                        )
                )
                ->addRadioOption(
                    (new RadioOption())
                        ->setName('text')
                        ->setLabel('controller.card.list.filter.dataColumn.option.text')
                        ->setColumnMetadata(
                            (new ColumnMetadata())
                                ->setName('text')
                                ->setLabel('controller.card.list.table.text')
                        )
                )
                ->setQueryParameterName('dataColumn')
                ->setLabel('controller.card.list.filter.dataColumn.label'),
        ];
    }
}
