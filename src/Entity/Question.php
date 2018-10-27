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

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\QuestionRepository")
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(name="question_of_program", columns={"program", "number"})})
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
     * @ORM\ManyToOne(targetEntity="App\Entity\Program", inversedBy="questions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $program;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $number;

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
     * @param Program|null $program
     *
     * @return Question
     */
    public function setProgram(?Program $program): self
    {
        $this->program = $program;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getNumber(): ?string
    {
        return $this->number;
    }

    /**
     * @param string $number
     *
     * @return Question
     */
    public function setNumber(string $number): self
    {
        $this->number = $number;

        return $this;
    }
}
