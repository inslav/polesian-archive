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

namespace App\Persistence\Entity\PolesianProgram;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Persistence\Repository\PolesianProgram\ProgramRepository")
 *
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
class Program
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
     * @var Section
     *
     * @ORM\ManyToOne(targetEntity="App\Persistence\Entity\PolesianProgram\Section", inversedBy="programs")
     * @ORM\JoinColumn(nullable=false)
     */
    private $section;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $number;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $name;

    /**
     * @var Collection|Paragraph[]
     *
     * @ORM\OneToMany(
     *     targetEntity="App\Persistence\Entity\PolesianProgram\Paragraph",
     *     mappedBy="program",
     *     orphanRemoval=true
     * )
     */
    private $paragraphs;

    public function __construct()
    {
        $this->paragraphs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSection(): ?Section
    {
        return $this->section;
    }

    /**
     * @return Program
     */
    public function setSection(Section $section): self
    {
        $this->section = $section;

        return $this;
    }

    public function getNumber(): ?string
    {
        return $this->number;
    }

    /**
     * @return Program
     */
    public function setNumber(string $number): self
    {
        $this->number = $number;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @return Program
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection|Paragraph[]
     */
    public function getParagraphs(): Collection
    {
        return $this->paragraphs;
    }

    /**
     * @return Program
     */
    public function addParagraph(Paragraph $paragraph): self
    {
        if (!$this->paragraphs->contains($paragraph)) {
            $this->paragraphs[] = $paragraph;
            $paragraph->setProgram($this);
        }

        return $this;
    }
}
