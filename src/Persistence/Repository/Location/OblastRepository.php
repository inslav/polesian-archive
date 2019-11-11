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

namespace App\Persistence\Repository\Location;

use App\Persistence\Entity\Location\Oblast;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\ORMException;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class OblastRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Oblast::class);
    }

    /**
     * @throws ORMException
     */
    public function findOneByNameOrCreate(string $name): Oblast
    {
        $oblast = $this->findOneByName($name);

        if (null === $oblast) {
            $oblast = $this->createOblast($name);
        }

        return $oblast;
    }

    private function findOneByName(string $name): ?Oblast
    {
        return $this->findOneBy(
            [
                'name' => $name,
            ]
        );
    }

    /**
     * @throws ORMException
     */
    private function createOblast(string $name): Oblast
    {
        $oblast = new Oblast();

        $oblast->setName($name);

        $this->getEntityManager()->persist($oblast);

        return $oblast;
    }
}
