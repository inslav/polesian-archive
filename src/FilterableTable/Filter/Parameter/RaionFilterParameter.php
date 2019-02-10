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

use App\Persistence\Entity\Location\Raion;
use App\Persistence\QueryBuilder\Alias\AliasFactoryInterface;
use App\Persistence\QueryBuilder\Parameter\ParameterFactoryInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Vyfony\Bundle\FilterableTableBundle\Filter\Configurator\Parameter\ExpressionBuilderInterface;
use Vyfony\Bundle\FilterableTableBundle\Filter\Configurator\Parameter\FilterParameterInterface;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class RaionFilterParameter implements FilterParameterInterface, ExpressionBuilderInterface
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
        return 'raion';
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
            'label' => 'controller.card.list.filter.raion',
            'attr' => [
                'class' => '',
                'data-vyfony-filterable-table-filter-parameter' => true,
            ],
            'class' => Raion::class,
            'choice_label' => $this->createLabelBuilder(),
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
        $raions = $formData[$this->getQueryParameterName()];

        if (0 === \count($raions)) {
            return null;
        }

        $queryBuilder
            ->innerJoin(
                $entityAlias.'.village',
                $villageAlias = $this->aliasFactory->createAlias(static::class, 'village')
            )
        ;

        $orWhereClauses = [];

        foreach ($raions as $index => $raion) {
            $queryBuilder->setParameter(
                $raionIdParameter = $this->parameterFactory->createParameter(
                    $raionFieldAlias = $villageAlias.'.raion',
                    $index
                ),
                $raion->getId()
            );

            $orWhereClauses[] = $queryBuilder->expr()->eq($raionFieldAlias, $raionIdParameter);
        }

        return (string) $queryBuilder->expr()->orX(...$orWhereClauses);
    }

    /**
     * @return callable
     */
    private function createQueryBuilder(): callable
    {
        return function (EntityRepository $repository): QueryBuilder {
            $entityAlias = 'raion';

            return $repository
                ->createQueryBuilder($entityAlias)
                ->orderBy($entityAlias.'.name', 'ASC');
        };
    }

    /**
     * @return callable
     */
    private function createLabelBuilder(): callable
    {
        return function (Raion $raion) {
            return sprintf(
                '%s (%s)',
                $raion->getName(),
                $raion->getOblast()->getName()
            );
        };
    }
}
