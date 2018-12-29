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

namespace App\Repository\Card;

use App\Entity\Card\Village;
use App\Import\Card\Formatter\VillageFullName\VillageFullNameInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\ORMException;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class VillageRepository extends ServiceEntityRepository
{
    /**
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Village::class);
    }

    /**
     * @param string $name
     * @param string $raion
     * @param string $oblast
     *
     * @return Village|null
     */
    public function findOneByNameAndRaionAndOblast(string $name, string $raion, string $oblast): ?Village
    {
        return $this->findOneBy(
            [
                'name' => $name,
                'raion' => $raion,
                'oblast' => $oblast,
            ]
        );
    }

    /**
     * @param VillageFullNameInterface $villageFullName
     *
     * @throws ORMException
     *
     * @return Village
     */
    public function createVillage(VillageFullNameInterface $villageFullName): Village
    {
        $village = new Village();

        $village->setName($villageFullName->getName());
        $village->setRaion($villageFullName->getRaion());
        $village->setOblast($villageFullName->getOblast());

        $this->getEntityManager()->persist($village);

        return $village;
    }

    /**
     * @param VillageFullNameInterface $villageFullName
     *
     * @throws ORMException
     *
     * @return Village
     */
    public function findOneByNameAndRaionAndOblastOrCreate(VillageFullNameInterface $villageFullName): Village
    {
        $village = $this->findOneByNameAndRaionAndOblast(
            $villageFullName->getName(),
            $villageFullName->getRaion(),
            $villageFullName->getOblast()
        );

        if (null === $village) {
            $village = $this->createVillage($villageFullName);
        }

        return $village;
    }
}
