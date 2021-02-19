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

namespace App\Persistence\Repository\PolesianProgram;

use App\Persistence\Entity\PolesianProgram\Section;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\ORMException;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 *
 * @method Section|null find(int $id, ?int $lockMode = null, ?int $lockVersion = null)
 * @method Section|null findOneBy(array $criteria, ?array $orderBy = null)
 * @method Section[]    findAll()
 * @method Section[]    findBy(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null)
 */
final class SectionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Section::class);
    }

    /**
     * @throws ORMException
     */
    public function createSection(string $name): Section
    {
        $section = new Section();

        $section->setName($name);

        $this->getEntityManager()->persist($section);

        return $section;
    }

    /**
     * @return Section[]
     */
    public function findAllOrderedByDefault(): array
    {
        return $this->findBy([], ['id' => 'ASC']);
    }
}
