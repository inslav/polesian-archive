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

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Vyfony\Bundle\FilterableTableBundle\Filter\Configurator\Parameter\ExpressionBuilderInterface;
use Vyfony\Bundle\FilterableTableBundle\Filter\Configurator\Parameter\FilterParameterInterface;
use Vyfony\Bundle\FilterableTableBundle\Persistence\QueryBuilder\Parameter\ParameterFactoryInterface;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class TextOrDescriptionFilterParameter implements FilterParameterInterface, ExpressionBuilderInterface
{
    private $parameterFactory;

    public function __construct(ParameterFactoryInterface $parameterFactory)
    {
        $this->parameterFactory = $parameterFactory;
    }

    public function getQueryParameterName(): string
    {
        return 'text';
    }

    public function getType(): string
    {
        return TextType::class;
    }

    public function getOptions(EntityManager $entityManager): array
    {
        return [
            'label' => 'controller.card.list.filter.textOrDescription',
            'attr' => [
                'class' => '',
                'data-vyfony-filterable-table-filter-parameter' => true,
            ],
        ];
    }

    /**
     * @param mixed $formData
     */
    public function buildWhereExpression(QueryBuilder $queryBuilder, $formData, string $entityAlias): ?string
    {
        $filterValue = $formData;

        if (null === $filterValue) {
            return null;
        }

        $queryBuilder->setParameter(
            $parameterName = $this->parameterFactory->createParameter(
                $entityAlias.'_text_or_description',
                0
            ),
            '%'.mb_strtolower($filterValue).'%'
        );

        $fieldNameToExpressionConverter = function (
            string $fieldName
        ) use (
            $queryBuilder,
            $entityAlias,
            $parameterName
        ): string {
            return (string) $queryBuilder->expr()->like(
                'LOWER('.$entityAlias.'.'.$fieldName.')',
                $parameterName
            );
        };

        $whereArguments = array_map($fieldNameToExpressionConverter, ['text', 'description']);

        return (string) $queryBuilder->expr()->orX(...$whereArguments);
    }
}
