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
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\ORMException;
use LogicException;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 *
 * @method Raion|null find(int $id, ?int $lockMode = null, ?int $lockVersion = null)
 * @method Raion|null findOneBy(array $criteria, ?array $orderBy = null)
 * @method Raion[]    findAll()
 * @method Raion[]    findBy(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null)
 */
final class RaionRepository extends ServiceEntityRepository
{
    private $oblastRepository;

    public function __construct(ManagerRegistry $registry, OblastRepository $oblastRepository)
    {
        parent::__construct($registry, Raion::class);

        $this->oblastRepository = $oblastRepository;
    }

    /**
     * @throws ORMException
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
     * @throws ORMException
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
