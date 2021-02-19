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
 * @ORM\Entity(repositoryClass="App\Persistence\Repository\PolesianProgram\ParagraphRepository")
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(name="paragraph_of_program", columns={"program_id", "number"})})
 *
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
class Paragraph
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(name="id", type="integer")
     */
    private $id;

    /**
     * @ORM\JoinColumn(name="program_id", nullable=false)
     * @ORM\ManyToOne(targetEntity="App\Persistence\Entity\PolesianProgram\Program", inversedBy="paragraphs")
     */
    private $program;

    /**
     * @ORM\Column(name="number", type="integer")
     */
    private $number;

    /**
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
     */
    private $title;

    /**
     * @ORM\Column(name="text", type="text", nullable=true)
     */
    private $text;

    /**
     * @ORM\OneToMany(
     *     targetEntity="App\Persistence\Entity\PolesianProgram\Subparagraph",
     *     mappedBy="paragraph",
     *     orphanRemoval=true
     * )
     */
    private $subparagraphs;

    public function __construct()
    {
        $this->subparagraphs = new ArrayCollection();
    }

    public function __toString(): string
    {
        return sprintf(
            '%s %s',
            (string) $this->program,
            (string) $this->number
        );
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProgram(): ?Program
    {
        return $this->program;
    }

    /**
     * @return Paragraph
     */
    public function setProgram(Program $program): self
    {
        $this->program = $program;

        return $this;
    }

    public function getNumber(): ?int
    {
        return $this->number;
    }

    /**
     * @return Paragraph
     */
    public function setNumber(int $number): self
    {
        $this->number = $number;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @return Paragraph
     */
    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    /**
     * @return Paragraph
     */
    public function setText(?string $text): self
    {
        $this->text = $text;

        return $this;
    }

    /**
     * @return Collection|Subparagraph[]
     */
    public function getSubparagraphs(): Collection
    {
        return $this->subparagraphs;
    }

    /**
     * @return Paragraph
     */
    public function addSubparagraph(Subparagraph $subparagraph): self
    {
        if (!$this->subparagraphs->contains($subparagraph)) {
            $this->subparagraphs[] = $subparagraph;
            $subparagraph->setParagraph($this);
        }

        return $this;
    }
}
