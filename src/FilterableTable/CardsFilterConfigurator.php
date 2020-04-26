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

use App\FilterableTable\Filter\Parameter\CollectorFilterParameter;
use App\FilterableTable\Filter\Parameter\KeywordFilterParameter;
use App\FilterableTable\Filter\Parameter\OblastFilterParameter;
use App\FilterableTable\Filter\Parameter\ProgramFilterParameter;
use App\FilterableTable\Filter\Parameter\QuestionFilterParameter;
use App\FilterableTable\Filter\Parameter\RaionFilterParameter;
use App\FilterableTable\Filter\Parameter\TermFilterParameter;
use App\FilterableTable\Filter\Parameter\TextOrDescriptionFilterParameter;
use App\FilterableTable\Filter\Parameter\VillageFilterParameter;
use App\FilterableTable\Filter\Parameter\YearFilterParameter;
use App\Persistence\Entity\Card\Card;
use InvalidArgumentException;
use Vyfony\Bundle\FilterableTableBundle\Filter\Configurator\AbstractFilterConfigurator;
use Vyfony\Bundle\FilterableTableBundle\Filter\Configurator\Parameter\FilterParameterInterface;
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
    private $programFilterParameter;

    private $questionFilterParameter;

    private $yearFilterParameter;

    private $oblastFilterParameter;

    private $raionFilterParameter;

    private $villageFilterParameter;

    private $keywordFilterParameter;

    private $termFilterParameter;

    private $collectorFilterParameter;

    private $textOrDescriptionFilterParameter;

    public function __construct(
        ProgramFilterParameter $programFilterParameter,
        QuestionFilterParameter $questionFilterParameter,
        YearFilterParameter $yearFilterParameter,
        OblastFilterParameter $oblastFilterParameter,
        RaionFilterParameter $raionFilterParameter,
        VillageFilterParameter $villageFilterParameter,
        KeywordFilterParameter $keywordFilterParameter,
        TermFilterParameter $termFilterParameter,
        CollectorFilterParameter $collectorFilterParameter,
        TextOrDescriptionFilterParameter $textOrDescriptionFilterParameter
    ) {
        $this->programFilterParameter = $programFilterParameter;
        $this->questionFilterParameter = $questionFilterParameter;
        $this->yearFilterParameter = $yearFilterParameter;
        $this->oblastFilterParameter = $oblastFilterParameter;
        $this->raionFilterParameter = $raionFilterParameter;
        $this->villageFilterParameter = $villageFilterParameter;
        $this->keywordFilterParameter = $keywordFilterParameter;
        $this->termFilterParameter = $termFilterParameter;
        $this->collectorFilterParameter = $collectorFilterParameter;
        $this->yearFilterParameter = $yearFilterParameter;
        $this->textOrDescriptionFilterParameter = $textOrDescriptionFilterParameter;
    }

    public function createDefaults(): array
    {
        return [
            'label_attr' => ['class' => ''],
            'translation_domain' => 'messages',
            'attr' => ['class' => 'row', 'target' => '_blank'],
            'method' => 'GET',
            'csrf_protection' => false,
            'required' => false,
        ];
    }

    public function createSubmitButtonOptions(): array
    {
        return [
            'attr' => ['class' => 'btn btn-primary'],
            'label' => 'controller.card.list.filter.submitButton',
        ];
    }

    public function createResetButtonOptions(): array
    {
        return [
            'attr' => ['class' => 'btn btn-secondary'],
            'label' => 'controller.card.list.filter.resetButton',
        ];
    }

    public function createSearchInFoundButtonOptions(): array
    {
        return [
            'attr' => ['class' => 'btn btn-warning'],
            'label' => 'controller.card.list.filter.searchInFoundButton',
        ];
    }

    public function getDisablePaginationLabel(): string
    {
        return 'controller.card.list.filter.disablePaginator';
    }

    /**
     * @param mixed $entity
     *
     * @return mixed
     */
    public function getEntityId($entity)
    {
        if (!$entity instanceof Card) {
            $message = sprintf('Expected entity of type "%s", "%s" given', Card::class, $entity);

            throw new InvalidArgumentException($message);
        }

        return $entity->getId();
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
            $this->programFilterParameter,
            $this->questionFilterParameter,
            $this->yearFilterParameter,
            $this->oblastFilterParameter,
            $this->raionFilterParameter,
            $this->villageFilterParameter,
            $this->keywordFilterParameter,
            $this->termFilterParameter,
            $this->collectorFilterParameter,
            $this->yearFilterParameter,
            $this->textOrDescriptionFilterParameter,
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
                                ->setLabel('controller.card.list.table.column.description')
                        )
                )
                ->addRadioOption(
                    (new RadioOption())
                        ->setName('text')
                        ->setLabel('controller.card.list.filter.dataColumn.option.text')
                        ->setColumnMetadata(
                            (new ColumnMetadata())
                                ->setName('text')
                                ->setLabel('controller.card.list.table.column.text')
                        )
                )
                ->setQueryParameterName('dataColumn')
                ->setLabel('controller.card.list.filter.dataColumn.label'),
        ];
    }
}
