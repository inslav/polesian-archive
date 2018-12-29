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

use App\Entity\Card\Card;
use Vyfony\Bundle\FilterableTableBundle\Table\Checkbox\CheckboxHandler;
use Vyfony\Bundle\FilterableTableBundle\Table\Configurator\AbstractTableConfigurator;
use Vyfony\Bundle\FilterableTableBundle\Table\Metadata\Column\ColumnMetadata;
use Vyfony\Bundle\FilterableTableBundle\Table\Metadata\Column\ColumnMetadataInterface;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class CardsTableConfigurator extends AbstractTableConfigurator
{
    /**
     * @return string
     */
    protected function getResultsCountText(): string
    {
        return 'controller.card.list.table.resultsCount';
    }

    /**
     * @return ColumnMetadataInterface[]
     */
    protected function createColumnMetadataCollection(): array
    {
        return [
            (new ColumnMetadata())
                ->setName('id')
                ->setIsIdentifier(true)
                ->setIsSortable(true)
                ->setLabel('controller.card.list.table.column.id'),
            (new ColumnMetadata())
                ->setName('village')
                ->setValueExtractor(function (Card $card): string {
                    return $card->getVillage()->getName();
                })
                ->setLabel('controller.card.list.table.column.village'),
        ];
    }

    /**
     * @return array
     */
    protected function createCheckboxHandlers(): array
    {
        return [
            new CheckboxHandler(
                'filterable_table__download_txt',
                'controller.card.list.download.txt.label',
                'controller.card.list.download.txt.emptySelectionError'
            ),
        ];
    }
}
