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

namespace App\Persistence\Repository\Card;

use App\Persistence\Entity\Card\Collector;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\ORMException;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class CollectorRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Collector::class);
    }

    public function findOneByName(string $name): ?Collector
    {
        return $this->findOneBy(['name' => $name]);
    }

    /**
     * @throws ORMException
     */
    public function createCollector(string $name): Collector
    {
        $collector = new Collector();

        $collector->setName($name);

        $this->getEntityManager()->persist($collector);

        return $collector;
    }

    /**
     * @throws ORMException
     */
    public function findOneByNameOrCreate(string $name): Collector
    {
        $collector = $this->findOneByName($name);

        if (null === $collector) {
            $collector = $this->createCollector($name);
        }

        return $collector;
    }
}
