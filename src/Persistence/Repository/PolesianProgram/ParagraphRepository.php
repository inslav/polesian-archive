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

use App\Persistence\Entity\PolesianProgram\Paragraph;
use App\Persistence\Entity\PolesianProgram\Program;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\ORMException;
use LogicException;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class ParagraphRepository extends ServiceEntityRepository
{
    /**
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Paragraph::class);
    }

    /**
     * @param int         $number
     * @param string|null $title
     * @param string|null $text
     * @param Program     $program
     *
     * @throws ORMException
     *
     * @return Paragraph
     */
    public function createParagraph(int $number, ?string $title, ?string $text, Program $program): Paragraph
    {
        $paragraph = new Paragraph();

        $paragraph->setNumber($number);
        $paragraph->setTitle($title);
        $paragraph->setText($text);
        $paragraph->setProgram($program);

        $this->getEntityManager()->persist($paragraph);

        return $paragraph;
    }

    /**
     * @param string $programNumber
     * @param int    $number
     *
     * @return Paragraph|null
     */
    public function findOneByProgramNumberAndNumber(string $programNumber, int $number): ?Paragraph
    {
        $queryBuilder = $this->createQueryBuilder('paragraph');

        $query = $queryBuilder
            ->innerJoin('paragraph.program', 'program')
            ->setParameter('programNumber', $programNumber)
            ->setParameter('number', $number)
            ->select('paragraph')
            ->andWhere($queryBuilder->expr()->eq('program.number', ':programNumber'))
            ->andWhere($queryBuilder->expr()->eq('paragraph.number', ':number'))
            ->getQuery()
        ;

        try {
            return $query->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            throw new LogicException(sprintf('The built query "%s" returns non-unique result', $query->getDQL()));
        }
    }
}
