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

namespace App\Entity;

use App\Entity\Program\Paragraph;
use App\Entity\Program\Program;
use App\Entity\Program\Subparagraph;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\QuestionRepository")
 *
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
class Question
{
    /**
     * @var int
     *
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var Program
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Program\Program")
     * @ORM\JoinColumn(nullable=false)
     */
    private $program;

    /**
     * @var Paragraph|null
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Program\Paragraph")
     */
    private $paragraph;

    /**
     * @var Subparagraph|null
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Program\Subparagraph")
     */
    private $subparagraph;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", options={"default": 0})
     */
    private $isAdditional;

    public function __construct()
    {
        $this->isAdditional = false;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Program|null
     */
    public function getProgram(): ?Program
    {
        return $this->program;
    }

    /**
     * @param Program $program
     *
     * @return Question
     */
    public function setProgram(Program $program): self
    {
        $this->program = $program;

        return $this;
    }

    /**
     * @return Paragraph|null
     */
    public function getParagraph(): ?Paragraph
    {
        return $this->paragraph;
    }

    /**
     * @param Paragraph|null $paragraph
     *
     * @return Question
     */
    public function setParagraph(?Paragraph $paragraph): self
    {
        $this->paragraph = $paragraph;

        return $this;
    }

    /**
     * @return Subparagraph|null
     */
    public function getSubparagraph(): ?Subparagraph
    {
        return $this->subparagraph;
    }

    /**
     * @param Subparagraph|null $subparagraph
     *
     * @return Question
     */
    public function setSubparagraph(?Subparagraph $subparagraph): self
    {
        $this->subparagraph = $subparagraph;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getIsAdditional(): bool
    {
        return $this->isAdditional;
    }

    /**
     * @param bool $isAdditional
     *
     * @return Question
     */
    public function setIsAdditional(bool $isAdditional): self
    {
        $this->isAdditional = $isAdditional;

        return $this;
    }
}
