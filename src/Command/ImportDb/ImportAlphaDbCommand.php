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

namespace App\Command\ImportDb;

use App\ImportDb\Alpha\AlphaImporterInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class ImportAlphaDbCommand extends Command
{
    /**
     * @var AlphaImporterInterface
     */
    private $importer;

    /**
     * @param AlphaImporterInterface $importer
     */
    public function __construct(AlphaImporterInterface $importer)
    {
        parent::__construct();
        $this->importer = $importer;
    }

    protected function configure(): void
    {
        $this
            ->setName('app:import-db:alpha')
            ->setDescription('Import data from DB of version alpha')
        ;
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null
     */
    protected function execute(InputInterface $input, OutputInterface $output): ?int
    {
        $this->importer->import();

        (new SymfonyStyle($input, $output))->success('DB import has been successfully completed');

        return 0;
    }
}
