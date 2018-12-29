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

namespace App\Import\Card\Exporter\Registry;

use App\Import\Card\Exporter\CardExporterInterface;
use App\Import\Card\Exporter\XlsxCardExporter;
use InvalidArgumentException;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class CardExporterRegistry implements CardExporterRegistryInterface
{
    /**
     * @var XlsxCardExporter
     */
    private $xlsxCardExporter;

    /**
     * @param XlsxCardExporter $xlsxCardExporter
     */
    public function __construct(XlsxCardExporter $xlsxCardExporter)
    {
        $this->xlsxCardExporter = $xlsxCardExporter;
    }

    /**
     * @param string $exportFormat
     *
     * @return CardExporterInterface
     */
    public function getExporter(string $exportFormat): CardExporterInterface
    {
        $availableExporters = $this->getAvailableExporters();

        if (!array_key_exists($exportFormat, $availableExporters)) {
            throw new InvalidArgumentException(sprintf('Unknown export format "%s"', $exportFormat));
        }

        return $availableExporters[$exportFormat];
    }

    /**
     * @return CardExporterInterface[]
     */
    private function getAvailableExporters(): array
    {
        return [
            'xlsx' => $this->xlsxCardExporter,
        ];
    }
}
