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

use App\Import\Card\Importer\Registry\CardImporterRegistryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class ImportCommand extends Command
{
    /**
     * @var CardImporterRegistryInterface
     */
    private $cardImporterRegistry;

    /**
     * @param CardImporterRegistryInterface $cardImporterRegistry
     */
    public function __construct(CardImporterRegistryInterface $cardImporterRegistry)
    {
        parent::__construct();
        $this->cardImporterRegistry = $cardImporterRegistry;
    }

    protected function configure(): void
    {
        $this
            ->setName('app:import')
            ->setDescription('Import data from human-readable format to database')
            ->addArgument('import-file', InputArgument::REQUIRED, 'Path to import file')
            ->addArgument('import-format', InputArgument::REQUIRED, 'Import format')
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
        $this
            ->cardImporterRegistry
            ->getImporter($input->getArgument('import-format'))
            ->import($input->getArgument('import-file'));

        (new SymfonyStyle($input, $output))->success('Import has been successfully finished');

        return 0;
    }
}
