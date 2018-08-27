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
 * @ORM\Entity(repositoryClass="App\Repository\CardRepository")
 *
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
class Card
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
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $village;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $raion;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $oblast;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $question;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $program;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $year;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     */
    private $text;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @var string[]
     *
     * @ORM\Column(type="array")
     */
    private $keywords;

    /**
     * @var string[]
     *
     * @ORM\Column(type="array")
     */
    private $terms;

    /**
     * @var string[]
     *
     * @ORM\Column(type="array")
     */
    private $collectors;

    /**
     * @var string[]
     *
     * @ORM\Column(type="array")
     */
    private $informers;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getVillage(): ?string
    {
        return $this->village;
    }

    /**
     * @param string $village
     *
     * @return Card
     */
    public function setVillage(string $village): self
    {
        $this->village = $village;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getRaion(): ?string
    {
        return $this->raion;
    }

    /**
     * @param string $raion
     *
     * @return Card
     */
    public function setRaion(string $raion): self
    {
        $this->raion = $raion;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getOblast(): ?string
    {
        return $this->oblast;
    }

    /**
     * @param string $oblast
     *
     * @return Card
     */
    public function setOblast(string $oblast): self
    {
        $this->oblast = $oblast;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getQuestion(): ?string
    {
        return $this->question;
    }

    /**
     * @param string $question
     *
     * @return Card
     */
    public function setQuestion(string $question): self
    {
        $this->question = $question;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getProgram(): ?string
    {
        return $this->program;
    }

    /**
     * @param string $program
     *
     * @return Card
     */
    public function setProgram(string $program): self
    {
        $this->program = $program;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getYear(): ?int
    {
        return $this->year;
    }

    /**
     * @param int $year
     *
     * @return Card
     */
    public function setYear(int $year): self
    {
        $this->year = $year;

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
     * @return Card
     */
    public function setText(string $text): self
    {
        $this->text = $text;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string $description
     *
     * @return Card
     */
    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return string[]|null
     */
    public function getKeywords(): ?array
    {
        return $this->keywords;
    }

    /**
     * @param string[] $keywords
     *
     * @return Card
     */
    public function setKeywords(array $keywords): self
    {
        $this->keywords = $keywords;

        return $this;
    }

    /**
     * @return string[]|null
     */
    public function getTerms(): ?array
    {
        return $this->terms;
    }

    /**
     * @param string[] $terms
     *
     * @return Card
     */
    public function setTerms(array $terms): self
    {
        $this->terms = $terms;

        return $this;
    }

    /**
     * @return string[]|null
     */
    public function getCollectors(): ?array
    {
        return $this->collectors;
    }

    /**
     * @param string[] $collectors
     *
     * @return Card
     */
    public function setCollectors(array $collectors): self
    {
        $this->collectors = $collectors;

        return $this;
    }

    /**
     * @return string[]|null
     */
    public function getInformers(): ?array
    {
        return $this->informers;
    }

    /**
     * @param string[] $informers
     *
     * @return Card
     */
    public function setInformers(array $informers): self
    {
        $this->informers = $informers;

        return $this;
    }
}
