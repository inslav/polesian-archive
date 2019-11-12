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

use App\Formatter\VillageFullName\Formatter\VillageFullNameFormatterInterface;
use App\Persistence\Entity\Location\Village;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Vyfony\Bundle\FilterableTableBundle\Filter\Configurator\Parameter\ExpressionBuilderInterface;
use Vyfony\Bundle\FilterableTableBundle\Filter\Configurator\Parameter\FilterParameterInterface;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class VillageFilterParameter implements FilterParameterInterface, ExpressionBuilderInterface
{
    /**
     * @var VillageFullNameFormatterInterface
     */
    private $villageFullNameFormatter;

    public function __construct(VillageFullNameFormatterInterface $villageFullNameFormatter)
    {
        $this->villageFullNameFormatter = $villageFullNameFormatter;
    }

    public function getQueryParameterName(): string
    {
        return 'village';
    }

    public function getType(): string
    {
        return EntityType::class;
    }

    public function getOptions(EntityManager $entityManager): array
    {
        return [
            'label' => 'controller.card.list.filter.village',
            'attr' => [
                'class' => '',
                'data-vyfony-filterable-table-filter-parameter' => true,
            ],
            'class' => Village::class,
            'choice_label' => $this->createLabelBuilder(),
            'expanded' => false,
            'multiple' => true,
            'query_builder' => $this->createQueryBuilder(),
        ];
    }

    /**
     * @param mixed $formData
     */
    public function buildWhereExpression(QueryBuilder $queryBuilder, $formData, string $entityAlias): ?string
    {
        $villages = $formData;

        if (0 === \count($villages)) {
            return null;
        }

        $ids = [];

        foreach ($villages as $village) {
            $ids[] = $village->getId();
        }

        return (string) $queryBuilder->expr()->in($entityAlias.'.village', $ids);
    }

    private function createQueryBuilder(): callable
    {
        return function (EntityRepository $repository): QueryBuilder {
            $entityAlias = 'village';

            return $repository
                ->createQueryBuilder($entityAlias)
                ->orderBy($entityAlias.'.name', 'ASC');
        };
    }

    private function createLabelBuilder(): callable
    {
        return function (Village $village) {
            return $this->villageFullNameFormatter->formatVillage($village);
        };
    }
}
