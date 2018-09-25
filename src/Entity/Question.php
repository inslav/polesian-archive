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
     * @var int|null
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $number;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", nullable=true, length=255)
     */
    private $letter;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", options={"default": false})
     */
    private $isAdditional;

    /**
     * @var Program
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Program", inversedBy="questions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $program;

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
     * @return int|null
     */
    public function getNumber(): ?int
    {
        return $this->number;
    }

    /**
     * @param int $number
     *
     * @return Question
     */
    public function setNumber(int $number): self
    {
        $this->number = $number;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getLetter(): ?string
    {
        return $this->letter;
    }

    /**
     * @param string $letter
     *
     * @return Question
     */
    public function setLetter(string $letter): self
    {
        $this->letter = $letter;

        return $this;
    }

    /**
     * @return bool
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
}
