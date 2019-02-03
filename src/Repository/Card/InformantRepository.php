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

use App\Entity\Card\Informant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\ORMException;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class InformantRepository extends ServiceEntityRepository
{
    /**
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Informant::class);
    }

    /**
     * @param string $name
     *
     * @return Informant|null
     */
    public function findOneByName(string $name): ?Informant
    {
        return $this->findOneBy(['name' => $name]);
    }

    /**
     * @param string $name
     *
     * @throws ORMException
     *
     * @return Informant
     */
    public function createInformant(string $name): Informant
    {
        $informant = new Informant();

        $informant->setName($name);

        $this->getEntityManager()->persist($informant);

        return $informant;
    }

    /**
     * @param string $name
     *
     * @throws ORMException
     *
     * @return Informant
     */
    public function findOneByNameOrCreate(string $name): Informant
    {
        $informant = $this->findOneByName($name);

        if (null === $informant) {
            $informant = $this->createInformant($name);
        }

        return $informant;
    }
}
