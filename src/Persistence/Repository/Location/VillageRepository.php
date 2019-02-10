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

use App\Import\Card\Formatter\VillageFullName\VillageFullNameInterface;
use App\Persistence\Entity\Location\Village;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\ORMException;
use LogicException;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class VillageRepository extends ServiceEntityRepository
{
    /**
     * @var RaionRepository
     */
    private $raionRepository;

    /**
     * @param RegistryInterface $registry
     * @param RaionRepository   $raionRepository
     */
    public function __construct(RegistryInterface $registry, RaionRepository $raionRepository)
    {
        parent::__construct($registry, Village::class);

        $this->raionRepository = $raionRepository;
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
        $village = $this->findOneByNameAndRaionNameAndOblastName(
            $villageFullName->getName(),
            $villageFullName->getRaion(),
            $villageFullName->getOblast()
        );

        if (null === $village) {
            $village = $this->createVillage($villageFullName);
        }

        return $village;
    }

    /**
     * @param string $name
     * @param string $raionName
     * @param string $oblastName
     *
     * @return Village|null
     */
    private function findOneByNameAndRaionNameAndOblastName(
        string $name,
        string $raionName,
        string $oblastName
    ): ?Village {
        $queryBuilder = $this->createQueryBuilder('village');

        $query = $queryBuilder
            ->innerJoin('village.raion', 'raion')
            ->innerJoin('raion.oblast', 'oblast')
            ->setParameter('name', $name)
            ->setParameter('raionName', $raionName)
            ->setParameter('oblastName', $oblastName)
            ->select('village')
            ->andWhere($queryBuilder->expr()->eq('village.name', ':name'))
            ->andWhere($queryBuilder->expr()->eq('raion.name', ':raionName'))
            ->andWhere($queryBuilder->expr()->eq('oblast.name', ':oblastName'))
            ->getQuery()
        ;

        try {
            return $query->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            throw new LogicException(sprintf('The built query "%s" returns non-unique result', $query->getDQL()));
        }
    }

    /**
     * @param VillageFullNameInterface $villageFullName
     *
     * @throws ORMException
     *
     * @return Village
     */
    private function createVillage(VillageFullNameInterface $villageFullName): Village
    {
        $village = new Village();

        $village->setName($villageFullName->getName());
        $village->setRaion(
            $this->raionRepository->findOneByNameAndOblastNameOrCreate(
                $villageFullName->getRaion(),
                $villageFullName->getOblast()
            )
        );

        $this->getEntityManager()->persist($village);

        return $village;
    }
}
