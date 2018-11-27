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

namespace App\Repository\Program;

use App\Entity\Program\Paragraph;
use App\Entity\Program\Program;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\ORMException;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Paragraph|null find($id, $lockMode = null, $lockVersion = null)
 * @method Paragraph|null findOneBy(array $criteria, array $orderBy = null)
 * @method Paragraph[]    findAll()
 * @method Paragraph[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 *
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
}
