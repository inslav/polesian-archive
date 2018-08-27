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

use Vyfony\Bundle\FilterableTableBundle\Table\Configurator\AbstractTableConfigurator;
use Vyfony\Bundle\FilterableTableBundle\Table\Metadata\Column\ColumnMetadata;
use Vyfony\Bundle\FilterableTableBundle\Table\Metadata\Column\ColumnMetadataInterface;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class CardsTableConfigurator extends AbstractTableConfigurator
{
    /**
     * @return ColumnMetadataInterface[]
     */
    protected function factoryColumnMetadataCollection(): array
    {
        return [
            (new ColumnMetadata())
                ->setName('id')
                ->setIsIdentifier(true)
                ->setLabel('controller.card.list.table.id'),
            (new ColumnMetadata())
                ->setName('description')
                ->setLabel('controller.card.list.table.description'),
            (new ColumnMetadata())
                ->setName('village')
                ->setLabel('controller.card.list.table.village'),
            (new ColumnMetadata())
                ->setName('question')
                ->setLabel('controller.card.list.table.question'),
            (new ColumnMetadata())
                ->setName('program')
                ->setLabel('controller.card.list.table.program'),
        ];
    }

    /**
     * @return string
     */
    protected function getListRoute(): string
    {
        return 'card__list';
    }

    /**
     * @return string
     */
    protected function getShowRoute(): string
    {
        return 'card__show';
    }

    /**
     * @return string
     */
    protected function getDefaultSortBy(): string
    {
        return 'id';
    }

    /**
     * @return string
     */
    protected function getDefaultSortOrder(): string
    {
        return 'asc';
    }

    /**
     * @return string[]
     */
    protected function getShowRouteParameters(): array
    {
        return ['id' => 'id'];
    }
}
