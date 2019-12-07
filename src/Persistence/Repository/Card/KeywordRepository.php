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

use App\Persistence\Entity\Card\Keyword;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\ORMException;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class KeywordRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Keyword::class);
    }

    public function findOneByName(string $name): ?Keyword
    {
        return $this->findOneBy(['name' => $name]);
    }

    /**
     * @throws ORMException
     */
    public function createKeyword(string $name): Keyword
    {
        $keyword = new Keyword();

        $keyword->setName($name);

        $this->getEntityManager()->persist($keyword);

        return $keyword;
    }

    /**
     * @throws ORMException
     */
    public function findOneByNameOrCreate(string $name): Keyword
    {
        $keyword = $this->findOneByName($name);

        if (null === $keyword) {
            $keyword = $this->createKeyword($name);
        }

        return $keyword;
    }
}
