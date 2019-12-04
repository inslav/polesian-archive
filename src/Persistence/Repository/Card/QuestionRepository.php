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

use App\Formatter\QuestionNumber\QuestionNumberInterface;
use App\Persistence\Entity\Card\Question;
use App\Persistence\Repository\PolesianProgram\ParagraphRepository;
use App\Persistence\Repository\PolesianProgram\ProgramRepository;
use App\Persistence\Repository\PolesianProgram\SubparagraphRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\ORMException;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class QuestionRepository extends ServiceEntityRepository
{
    /**
     * @var ProgramRepository
     */
    private $programRepository;

    /**
     * @var ParagraphRepository
     */
    private $paragraphRepository;

    /**
     * @var SubparagraphRepository
     */
    private $subparagraphRepository;

    public function __construct(
        ManagerRegistry $registry,
        ProgramRepository $programRepository,
        ParagraphRepository $paragraphRepository,
        SubparagraphRepository $subparagraphRepository
    ) {
        parent::__construct($registry, Question::class);
        $this->programRepository = $programRepository;
        $this->paragraphRepository = $paragraphRepository;
        $this->subparagraphRepository = $subparagraphRepository;
    }

    /**
     * @throws ORMException
     */
    public function createQuestion(QuestionNumberInterface $questionNumber): Question
    {
        $question = new Question();

        $question->setProgram($this->programRepository->findOneByNumber($questionNumber->getProgramNumber()));

        if (null !== $questionNumber->getParagraphNumber()) {
            $question->setParagraph(
                $this->paragraphRepository->findOneByProgramNumberAndNumber(
                    $questionNumber->getProgramNumber(),
                    $questionNumber->getParagraphNumber()
                )
            );
        }

        if (null !== $questionNumber->getSubparagraphLetter()) {
            $question->setSubparagraph(
                $this->subparagraphRepository->findOneByProgramNumberAndParagraphNumberAndLetter(
                    $questionNumber->getProgramNumber(),
                    $questionNumber->getParagraphNumber(),
                    $questionNumber->getSubparagraphLetter()
                )
            );
        }

        $question->setIsAdditional($questionNumber->getIsAdditional());

        $this->getEntityManager()->persist($question);

        return $question;
    }
}
