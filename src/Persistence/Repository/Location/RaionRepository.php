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

use App\Persistence\Entity\Location\Raion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\ORMException;
use LogicException;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class RaionRepository extends ServiceEntityRepository
{
    /**
     * @var OblastRepository
     */
    private $oblastRepository;

    /**
     * @param RegistryInterface $registry
     * @param OblastRepository  $oblastRepository
     */
    public function __construct(RegistryInterface $registry, OblastRepository $oblastRepository)
    {
        parent::__construct($registry, Raion::class);

        $this->oblastRepository = $oblastRepository;
    }

    /**
     * @param string $name
     * @param string $oblastName
     *
     * @throws ORMException
     *
     * @return Raion
     */
    public function findOneByNameAndOblastNameOrCreate(string $name, string $oblastName): Raion
    {
        $raion = $this->findOneByNameAndOblastName(
            $name,
            $oblastName
        );

        if (null === $raion) {
            $raion = $this->createRaion($name, $oblastName);
        }

        return $raion;
    }

    /**
     * @param string $name
     * @param string $oblastName
     *
     * @return Raion|null
     */
    private function findOneByNameAndOblastName(string $name, string $oblastName): ?Raion
    {
        $queryBuilder = $this->createQueryBuilder('raion');

        $query = $queryBuilder
            ->innerJoin('raion.oblast', 'oblast')
            ->setParameter('name', $name)
            ->setParameter('oblastName', $oblastName)
            ->select('raion')
            ->andWhere($queryBuilder->expr()->eq('raion.name', ':name'))
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
     * @param string $name
     * @param string $oblastName
     *
     * @throws ORMException
     *
     * @return Raion
     */
    private function createRaion(string $name, string $oblastName): Raion
    {
        $raion = new Raion();

        $raion->setName($name);
        $raion->setOblast(
            $this->oblastRepository->findOneByNameOrCreate($oblastName)
        );

        $this->getEntityManager()->persist($raion);

        return $raion;
    }
}
