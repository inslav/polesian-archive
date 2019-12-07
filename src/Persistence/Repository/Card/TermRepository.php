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

use App\Persistence\Entity\Card\Term;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\ORMException;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class TermRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Term::class);
    }

    public function findOneByName(string $name): ?Term
    {
        return $this->findOneBy(['name' => $name]);
    }

    /**
     * @throws ORMException
     */
    public function createTerm(string $name): Term
    {
        $term = new Term();

        $term->setName($name);

        $this->getEntityManager()->persist($term);

        return $term;
    }

    /**
     * @throws ORMException
     */
    public function findOneByNameOrCreate(string $name): Term
    {
        $term = $this->findOneByName($name);

        if (null === $term) {
            $term = $this->createTerm($name);
        }

        return $term;
    }
}
