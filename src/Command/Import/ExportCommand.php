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

namespace App\Command\Import;

use App\Import\Card\Exporter\Registry\CardExporterRegistryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class ExportCommand extends Command
{
    /**
     * @var CardExporterRegistryInterface
     */
    private $cardExporterRegistry;

    /**
     * @param CardExporterRegistryInterface $cardExporterRegistry
     */
    public function __construct(CardExporterRegistryInterface $cardExporterRegistry)
    {
        parent::__construct();
        $this->cardExporterRegistry = $cardExporterRegistry;
    }

    protected function configure(): void
    {
        $this
            ->setName('app:export')
            ->setDescription('Export data from database to human-readable format')
            ->addArgument('export-file', InputArgument::REQUIRED, 'Path to export file')
            ->addArgument('export-format', InputArgument::REQUIRED, 'Export format')
            ->addArgument('bunch-size', InputArgument::OPTIONAL, 'Bunch size')
        ;
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $bunchSizeArgument = $input->getArgument('bunch-size');

        $this
            ->cardExporterRegistry
            ->getExporter($input->getArgument('export-format'))
            ->export($input->getArgument('export-file'), null === $bunchSizeArgument ? null : (int) $bunchSizeArgument);

        return 0;
    }
}
