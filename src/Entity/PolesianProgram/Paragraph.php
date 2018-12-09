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

namespace App\Entity\PolesianProgram;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PolesianProgram\ParagraphRepository")
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(name="paragraph_of_program", columns={"program_id", "number"})})
 *
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
class Paragraph
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
     * @ORM\ManyToOne(targetEntity="App\Entity\PolesianProgram\Program", inversedBy="paragraphs")
     * @ORM\JoinColumn(nullable=false)
     */
    private $program;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $number;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $title;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $text;

    /**
     * @var Collection|Subparagraph[]
     *
     * @ORM\OneToMany(targetEntity="App\Entity\PolesianProgram\Subparagraph", mappedBy="paragraph", orphanRemoval=true)
     */
    private $subparagraphs;

    public function __construct()
    {
        $this->subparagraphs = new ArrayCollection();
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
     * @param Program|null $program
     *
     * @return Paragraph
     */
    public function setProgram(?Program $program): self
    {
        $this->program = $program;

        return $this;
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
     * @return Paragraph
     */
    public function setNumber(int $number): self
    {
        $this->number = $number;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string|null $title
     *
     * @return Paragraph
     */
    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getText(): ?string
    {
        return $this->text;
    }

    /**
     * @param string|null $text
     *
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
     * @param Subparagraph $subparagraph
     *
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

    /**
     * @param Subparagraph $subparagraph
     *
     * @return Paragraph
     */
    public function removeSubparagraph(Subparagraph $subparagraph): self
    {
        if ($this->subparagraphs->contains($subparagraph)) {
            $this->subparagraphs->removeElement($subparagraph);
            if ($subparagraph->getParagraph() === $this) {
                $subparagraph->setParagraph(null);
            }
        }

        return $this;
    }
}
