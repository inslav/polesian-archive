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

use App\Persistence\QueryBuilder\Alias\AliasFactoryInterface;
use App\Persistence\QueryBuilder\Parameter\ParameterFactoryInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Vyfony\Bundle\FilterableTableBundle\Filter\Configurator\Parameter\ExpressionBuilderInterface;
use Vyfony\Bundle\FilterableTableBundle\Filter\Configurator\Parameter\FilterParameterInterface;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class TextOrDescriptionFilterParameter implements FilterParameterInterface, ExpressionBuilderInterface
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
     * @param AliasFactoryInterface     $aliasFactory
     * @param ParameterFactoryInterface $parameterFactory
     */
    public function __construct(
        AliasFactoryInterface $aliasFactory,
        ParameterFactoryInterface $parameterFactory
    ) {
        $this->aliasFactory = $aliasFactory;
        $this->parameterFactory = $parameterFactory;
    }

    /**
     * @return string
     */
    public function getQueryParameterName(): string
    {
        return 'text';
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return TextType::class;
    }

    /**
     * @param EntityManager $entityManager
     *
     * @return array
     */
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
     * @param QueryBuilder $queryBuilder
     * @param mixed        $formData
     * @param string       $entityAlias
     *
     * @return string|null
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
                ':'.$parameterName
            );
        };

        $whereArguments = array_map($fieldNameToExpressionConverter, ['text', 'description']);

        return (string) $queryBuilder->expr()->orX(...$whereArguments);
    }
}
