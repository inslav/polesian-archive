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

namespace App\Controller;

use App\Persistence\Repository\PolesianProgram\ParagraphRepository;
use App\Persistence\Repository\PolesianProgram\ProgramRepository;
use App\Persistence\Repository\PolesianProgram\SectionRepository;
use App\Persistence\Repository\PolesianProgram\SubparagraphRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class PolesianProgramController extends AbstractController
{
    /**
     * @var SectionRepository
     */
    private $sectionRepository;

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
        SectionRepository $sectionRepository,
        ProgramRepository $programRepository,
        ParagraphRepository $paragraphRepository,
        SubparagraphRepository $subparagraphRepository
    ) {
        $this->sectionRepository = $sectionRepository;
        $this->programRepository = $programRepository;
        $this->paragraphRepository = $paragraphRepository;
        $this->subparagraphRepository = $subparagraphRepository;
    }

    /**
     * @Route("/polesian-program", name="polesian_program__index")
     *
     * @Template("polesian_program/index.html.twig")
     */
    public function index(): array
    {
        return [
            'controller' => 'polesianProgram',
            'method' => 'index',
            'sections' => $this->sectionRepository->findAllOrderedByDefault(),
        ];
    }

    /**
     * @Route("/polesian-program/program/{number}", name="polesian_program__program")
     *
     * @Template("polesian_program/program.html.twig")
     */
    public function program(string $number): array
    {
        return [
            'controller' => 'polesianProgram',
            'method' => 'program',
            'program' => $this->programRepository->findOneByNumber($number),
        ];
    }

    /**
     * @Route("/polesian-program/program/{programNumber}/paragraph/{number}", name="polesian_program__paragraph")
     *
     * @Template("polesian_program/paragraph.html.twig")
     */
    public function paragraph(string $programNumber, int $number): array
    {
        return [
            'controller' => 'polesianProgram',
            'method' => 'paragraph',
            'paragraph' => $this->paragraphRepository->findOneByProgramNumberAndNumber($programNumber, $number),
        ];
    }

    /**
     * @Route("/polesian-program/program/{programNumber}/paragraph/{paragraphNumber}/subparagraph/{letter}", name="polesian_program__subparagraph")
     *
     * @Template("polesian_program/subparagraph.html.twig")
     */
    public function subparagraph(string $programNumber, int $paragraphNumber, string $letter): array
    {
        return [
            'controller' => 'polesianProgram',
            'method' => 'subparagraph',
            'subparagraph' => $this->subparagraphRepository->findOneByProgramNumberAndParagraphNumberAndLetter(
                $programNumber,
                $paragraphNumber,
                $letter
            ),
        ];
    }
}
