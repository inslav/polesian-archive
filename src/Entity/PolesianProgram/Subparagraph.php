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

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PolesianProgram\SubparagraphRepository")
 * @ORM\Table(
 *     uniqueConstraints={@ORM\UniqueConstraint(name="subparagraph_of_paragraph", columns={"paragraph_id", "letter"})}
 * )
 *
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
class Subparagraph
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
     * @var Paragraph
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\PolesianProgram\Paragraph", inversedBy="subparagraphs")
     * @ORM\JoinColumn(nullable=false)
     */
    private $paragraph;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $letter;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     */
    private $text;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
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
     * @return Subparagraph
     */
    public function setParagraph(?Paragraph $paragraph): self
    {
        $this->paragraph = $paragraph;

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
     * @return Subparagraph
     */
    public function setLetter(string $letter): self
    {
        $this->letter = $letter;

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
     * @param string $text
     *
     * @return Subparagraph
     */
    public function setText(string $text): self
    {
        $this->text = $text;

        return $this;
    }
}
