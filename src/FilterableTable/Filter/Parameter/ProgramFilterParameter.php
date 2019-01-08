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

use App\Entity\PolesianProgram\Program;
use App\Repository\PolesianProgram\ProgramRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Vyfony\Bundle\FilterableTableBundle\Filter\Configurator\Parameter\ExpressionBuilderInterface;
use Vyfony\Bundle\FilterableTableBundle\Filter\Configurator\Parameter\FilterParameterInterface;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class ProgramFilterParameter implements FilterParameterInterface, ExpressionBuilderInterface
{
    /**
     * @var ProgramRepository
     */
    private $programRepository;

    /**
     * @param ProgramRepository $programRepository
     */
    public function __construct(ProgramRepository $programRepository)
    {
        $this->programRepository = $programRepository;
    }

    /**
     * @return string
     */
    public function getQueryParameterName(): string
    {
        return 'program';
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
            'label' => 'controller.card.list.filter.program',
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
        $programIds = $formData[$this->getQueryParameterName()];

        if (0 === \count($programIds)) {
            return null;
        }

        $questionAlias = 'question';
        $programAlias = 'program';

        $queryBuilder
            ->innerJoin($entityAlias.'.questions', $questionAlias)
            ->innerJoin($questionAlias.'.program', $programAlias)
        ;

        return (string) $queryBuilder->expr()->in($programAlias.'.id', $programIds);
    }

    /**
     * @return array
     */
    private function createChoices(): array
    {
        $entityCollection = $this->programRepository->findAll();

        usort($entityCollection, function (Program $a, Program $b): int {
            return strnatcmp($a->getNumber(), $b->getNumber());
        });

        $choiceValueFactory = function (Program $program): int {
            return $program->getId();
        };

        $choiceLabelFactory = function (Program $program): string {
            return sprintf('%s. %s', $program->getNumber(), $program->getName());
        };

        $values = array_map($choiceValueFactory, $entityCollection);
        $labels = array_map($choiceLabelFactory, $entityCollection);

        return array_combine($labels, $values);
    }
}
