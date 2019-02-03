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

namespace App\Import\Card\Importer\Registry;

use App\Import\Card\Importer\CardImporterInterface;
use App\Import\Card\Importer\XlsxCardImporter;
use InvalidArgumentException;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class CardImporterRegistry implements CardImporterRegistryInterface
{
    /**
     * @var XlsxCardImporter
     */
    private $xlsxCardImporter;

    /**
     * @param XlsxCardImporter $xlsxCardImporter
     */
    public function __construct(XlsxCardImporter $xlsxCardImporter)
    {
        $this->xlsxCardImporter = $xlsxCardImporter;
    }

    /**
     * @param string $importFormat
     *
     * @return CardImporterInterface
     */
    public function getImporter(string $importFormat): CardImporterInterface
    {
        $availableImporters = $this->getAvailableImporters();

        if (!array_key_exists($importFormat, $availableImporters)) {
            throw new InvalidArgumentException(sprintf('Unknown import format "%s"', $importFormat));
        }

        return $availableImporters[$importFormat];
    }

    /**
     * @return CardImporterInterface[]
     */
    private function getAvailableImporters(): array
    {
        return [
            'xlsx' => $this->xlsxCardImporter,
        ];
    }
}