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

use App\Entity\Card\Informer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\ORMException;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class InformerRepository extends ServiceEntityRepository
{
    /**
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Informer::class);
    }

    /**
     * @param string $name
     *
     * @return Informer|null
     */
    public function findOneByName(string $name): ?Informer
    {
        return $this->findOneBy(['name' => $name]);
    }

    /**
     * @param string $name
     *
     * @throws ORMException
     *
     * @return Informer
     */
    public function createInformer(string $name): Informer
    {
        $informer = new Informer();

        $informer->setName($name);

        $this->getEntityManager()->persist($informer);

        return $informer;
    }

    /**
     * @param string $name
     *
     * @throws ORMException
     *
     * @return Informer
     */
    public function findOneByNameOrCreate(string $name): Informer
    {
        $informer = $this->findOneByName($name);

        if (null === $informer) {
            $informer = $this->createInformer($name);
        }

        return $informer;
    }
}
