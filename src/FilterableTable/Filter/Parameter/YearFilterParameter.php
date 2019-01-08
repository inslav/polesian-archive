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

use App\Repository\Card\CardRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Vyfony\Bundle\FilterableTableBundle\Filter\Configurator\Parameter\ExpressionBuilderInterface;
use Vyfony\Bundle\FilterableTableBundle\Filter\Configurator\Parameter\FilterParameterInterface;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class YearFilterParameter implements FilterParameterInterface, ExpressionBuilderInterface
{
    /**
     * @var CardRepository
     */
    private $cardRepository;

    /**
     * @param CardRepository $cardRepository
     */
    public function __construct(CardRepository $cardRepository)
    {
        $this->cardRepository = $cardRepository;
    }

    /**
     * @return string
     */
    public function getQueryParameterName(): string
    {
        return 'year';
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
            'label' => 'controller.card.list.filter.year',
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
        $formattedYears = $formData[$this->getQueryParameterName()];

        if (0 === \count($formattedYears)) {
            return null;
        }

        return (string) $queryBuilder->expr()->in($entityAlias.'.'.$this->getQueryParameterName(), $formattedYears);
    }

    /**
     * @return array
     */
    private function createChoices(): array
    {
        $cardAlias = 'card';

        $entityCollection = $this->cardRepository
            ->createQueryBuilder($cardAlias)
            ->select($cardAlias.'.'.$this->getQueryParameterName())
            ->distinct()
            ->orderBy($cardAlias.'.'.$this->getQueryParameterName(), 'ASC')
            ->getQuery()
            ->getResult();

        $convertEntityToPropertyValue = function (array $entity): int {
            return $entity[$this->getQueryParameterName()];
        };

        $properties = array_map($convertEntityToPropertyValue, $entityCollection);

        return array_combine($properties, $properties);
    }
}
