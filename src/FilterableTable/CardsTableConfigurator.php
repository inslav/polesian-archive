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

use App\Formatter\QuestionNumber\Formatter\QuestionNumberFormatterInterface;
use App\Formatter\VillageFullName\Formatter\VillageFullNameFormatterInterface;
use App\Persistence\Entity\Card\Card;
use App\Persistence\Entity\Card\Question;
use Symfony\Component\Routing\RouterInterface;
use Vyfony\Bundle\FilterableTableBundle\Filter\Configurator\FilterConfiguratorInterface;
use Vyfony\Bundle\FilterableTableBundle\Table\Checkbox\CheckboxHandler;
use Vyfony\Bundle\FilterableTableBundle\Table\Configurator\AbstractTableConfigurator;
use Vyfony\Bundle\FilterableTableBundle\Table\Metadata\Column\ColumnMetadata;
use Vyfony\Bundle\FilterableTableBundle\Table\Metadata\Column\ColumnMetadataInterface;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class CardsTableConfigurator extends AbstractTableConfigurator
{
    private $villageFullNameFormatter;

    private $questionNumberFormatter;

    public function __construct(
        RouterInterface $router,
        FilterConfiguratorInterface $filterConfigurator,
        string $defaultSortBy,
        string $defaultSortOrder,
        string $listRoute,
        string $showRoute,
        array $showRouteParameters,
        int $pageSize,
        int $paginatorTailLength,
        VillageFullNameFormatterInterface $villageFullNameFormatter,
        QuestionNumberFormatterInterface $questionNumberFormatter
    ) {
        parent::__construct(
            $router,
            $filterConfigurator,
            $defaultSortBy,
            $defaultSortOrder,
            $listRoute,
            $showRoute,
            $showRouteParameters,
            $pageSize,
            $paginatorTailLength
        );

        $this->villageFullNameFormatter = $villageFullNameFormatter;
        $this->questionNumberFormatter = $questionNumberFormatter;
    }

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
                ->setLabel('controller.card.list.table.column.id')
                ->setAttributes(['target' => '_blank']),
            (new ColumnMetadata())
                ->setName('questions')
                ->setValueExtractor(function (Card $card): string {
                    $formatQuestion = function (Question $question): string {
                        return $this->questionNumberFormatter->formatQuestion($question);
                    };

                    $formattedQuestionNumbersArray = $card->getQuestions()->map($formatQuestion)->toArray();

                    return implode(', ', $formattedQuestionNumbersArray);
                })
                ->setLabel('controller.card.list.table.column.questions'),
            (new ColumnMetadata())
                ->setName('village')
                ->setValueExtractor(function (Card $card): string {
                    return $this->villageFullNameFormatter->formatVillage($card->getVillage());
                })
                ->setLabel('controller.card.list.table.column.village'),
        ];
    }

    protected function createCheckboxHandlers(): array
    {
        $classes = ['btn', 'btn-secondary', 'pa-download-button'];

        return [
            new CheckboxHandler(
                'filterable_table__download_txt',
                'controller.card.list.download.txt.label',
                'controller.card.list.download.txt.emptySelectionError',
                $classes
            ),
            new CheckboxHandler(
                'filterable_table__download_xlsx',
                'controller.card.list.download.xlsx.label',
                'controller.card.list.download.xlsx.emptySelectionError',
                $classes
            ),
        ];
    }
}
