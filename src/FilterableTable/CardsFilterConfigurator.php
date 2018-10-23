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

use App\Entity\Collector;
use App\Entity\Keyword;
use App\Entity\Term;
use App\Entity\Village;
use Vyfony\Bundle\FilterableTableBundle\Filter\Configurator\AbstractFilterConfigurator;
use Vyfony\Bundle\FilterableTableBundle\Filter\Configurator\Parameter\EntityChoiceParameter;
use Vyfony\Bundle\FilterableTableBundle\Filter\Configurator\Parameter\FilterParameterInterface;
use Vyfony\Bundle\FilterableTableBundle\Filter\Configurator\Parameter\JoinedEntityChoiceParameter;
use Vyfony\Bundle\FilterableTableBundle\Filter\Configurator\Parameter\TableParameter\TableParameterInterface;
use Vyfony\Bundle\FilterableTableBundle\Filter\Configurator\Restriction\FilterRestrictionInterface;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class CardsFilterConfigurator extends AbstractFilterConfigurator
{
    /**
     * @return array
     */
    public function factoryDefaultOptions(): array
    {
        return [
            'label_attr' => ['class' => ''],
            'translation_domain' => 'messages',
            'attr' => ['class' => ''],
            'method' => 'GET',
            'csrf_protection' => false,
            'required' => false,
        ];
    }

    /**
     * @return array
     */
    public function factorySubmitButtonOptions(): array
    {
        return [
            'attr' => ['class' => 'btn btn-default'],
            'label' => 'controller.card.list.filter.submitButton',
        ];
    }

    /**
     * @return array
     */
    public function factoryResetButtonOptions(): array
    {
        return [
            'attr' => ['class' => 'btn btn-default'],
            'label' => 'controller.card.list.filter.resetButton',
        ];
    }

    /**
     * @return FilterRestrictionInterface[]
     */
    protected function factoryFilterRestrictions(): array
    {
        return [
        ];
    }

    /**
     * @return FilterParameterInterface[]
     */
    protected function factoryFilterParameters(): array
    {
        return [
            (new EntityChoiceParameter())
                ->setClass(Village::class)
                ->setIsExpanded(false)
                ->setChoiceLabel('name')
                ->setPropertyName('village')
                ->setLabel('controller.card.list.filter.village'),
            (new JoinedEntityChoiceParameter())
                ->setClass(Keyword::class)
                ->setIsExpanded(false)
                ->setChoiceLabel('name')
                ->setPropertyName('keywords')
                ->setLabel('controller.card.list.filter.keywords'),
            (new JoinedEntityChoiceParameter())
                ->setClass(Term::class)
                ->setIsExpanded(false)
                ->setChoiceLabel('name')
                ->setPropertyName('terms')
                ->setLabel('controller.card.list.filter.terms'),
            (new JoinedEntityChoiceParameter())
                ->setClass(Collector::class)
                ->setIsExpanded(false)
                ->setChoiceLabel('name')
                ->setPropertyName('collectors')
                ->setLabel('controller.card.list.filter.collectors'),
        ];
    }

    /**
     * @return TableParameterInterface[]
     */
    protected function factoryTableParameters(): array
    {
        return [
        ];
    }

    /**
     * @return array
     */
    protected function factoryCommonFilterParameterOptions(): array
    {
        return [
            'label_attr' => ['class' => ''],
            'required' => false,
        ];
    }

    /**
     * @return array
     */
    protected function factoryCommonTableParameterOptions(): array
    {
        return [
            'label_attr' => ['class' => ''],
            'required' => true,
        ];
    }
}
