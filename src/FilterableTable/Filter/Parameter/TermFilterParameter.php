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

use App\Entity\Card\Term;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Vyfony\Bundle\FilterableTableBundle\Filter\Configurator\Parameter\ExpressionBuilderInterface;
use Vyfony\Bundle\FilterableTableBundle\Filter\Configurator\Parameter\FilterParameterInterface;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class TermFilterParameter implements FilterParameterInterface, ExpressionBuilderInterface
{
    /**
     * @return string
     */
    public function getQueryParameterName(): string
    {
        return 'term';
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return EntityType::class;
    }

    /**
     * @param EntityManager $entityManager
     *
     * @return array
     */
    public function getOptions(EntityManager $entityManager): array
    {
        return [
            'label' => 'controller.card.list.filter.terms',
            'attr' => [
                'class' => '',
                'data-vyfony-filterable-table-filter-parameter' => true,
            ],
            'class' => Term::class,
            'choice_label' => 'name',
            'expanded' => false,
            'multiple' => true,
            'query_builder' => $this->createQueryBuilder(),
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
        $terms = $formData[$this->getQueryParameterName()];

        if (0 === \count($terms)) {
            return null;
        }

        $ids = [];

        foreach ($terms as $term) {
            $ids[] = $term->getId();
        }

        $termAlias = 'term';

        $queryBuilder
            ->innerJoin($entityAlias.'.terms', $termAlias)
        ;

        return (string) $queryBuilder->expr()->in($termAlias.'.id', $ids);
    }

    /**
     * @return callable
     */
    private function createQueryBuilder(): callable
    {
        return function (EntityRepository $repository): QueryBuilder {
            $entityAlias = 'term';

            return $repository
                ->createQueryBuilder($entityAlias)
                ->orderBy($entityAlias.'.name', 'ASC');
        };
    }
}