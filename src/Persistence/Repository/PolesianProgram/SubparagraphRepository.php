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
use App\Persistence\Entity\PolesianProgram\Subparagraph;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\ORMException;
use LogicException;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class SubparagraphRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Subparagraph::class);
    }

    /**
     * @throws ORMException
     */
    public function createSubparagraph(string $letter, string $text, Paragraph $paragraph): Subparagraph
    {
        $subparagraph = new Subparagraph();

        $subparagraph->setLetter($letter);
        $subparagraph->setText($text);
        $subparagraph->setParagraph($paragraph);

        $this->getEntityManager()->persist($subparagraph);

        return $subparagraph;
    }

    /**
     * @throws LogicException
     */
    public function findOneByProgramNumberAndParagraphNumberAndLetter(
        string $programNumber,
        int $paragraphNumber,
        string $letter
    ): ?Subparagraph {
        $queryBuilder = $this->createQueryBuilder('subparagraph');

        $query = $queryBuilder
            ->innerJoin('subparagraph.paragraph', 'paragraph')
            ->innerJoin('paragraph.program', 'program')
            ->setParameter('programNumber', $programNumber)
            ->setParameter('paragraphNumber', $paragraphNumber)
            ->setParameter('letter', $letter)
            ->select('subparagraph')
            ->andWhere($queryBuilder->expr()->eq('program.number', ':programNumber'))
            ->andWhere($queryBuilder->expr()->eq('paragraph.number', ':paragraphNumber'))
            ->andWhere($queryBuilder->expr()->eq('subparagraph.letter', ':letter'))
            ->getQuery()
        ;

        try {
            return $query->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            throw new LogicException(sprintf('The built query "%s" returns non-unique result', $query->getDQL()));
        }
    }
}
