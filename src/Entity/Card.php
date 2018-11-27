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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
     * @var Village
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Village", inversedBy="cards")
     * @ORM\JoinColumn(nullable=false)
     */
    private $village;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $khutor;

    /**
     * @var Collection|Question[]
     *
     * @ORM\ManyToMany(targetEntity="App\Entity\Question")
     */
    private $questions;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $year;

    /**
     * @var Season
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Season", inversedBy="cards")
     */
    private $season;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", options={"default": 0})
     */
    private $hasPositiveAnswer;

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
     * @var Collection|Keyword[]
     *
     * @ORM\ManyToMany(targetEntity="App\Entity\Keyword", inversedBy="cards")
     */
    private $keywords;

    /**
     * @var Collection|Term[]
     *
     * @ORM\ManyToMany(targetEntity="App\Entity\Term", inversedBy="cards")
     */
    private $terms;

    /**
     * @var Collection|Informer[]
     *
     * @ORM\ManyToMany(targetEntity="App\Entity\Informer", inversedBy="cards")
     */
    private $informers;

    /**
     * @var Collection|Collector[]
     *
     * @ORM\ManyToMany(targetEntity="App\Entity\Collector", inversedBy="cards")
     */
    private $collectors;

    public function __construct()
    {
        $this->questions = new ArrayCollection();
        $this->hasPositiveAnswer = false;
        $this->keywords = new ArrayCollection();
        $this->terms = new ArrayCollection();
        $this->informers = new ArrayCollection();
        $this->collectors = new ArrayCollection();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Village|null
     */
    public function getVillage(): ?Village
    {
        return $this->village;
    }

    /**
     * @param Village|null $village
     *
     * @return Card
     */
    public function setVillage(?Village $village): self
    {
        $this->village = $village;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getKhutor(): ?string
    {
        return $this->khutor;
    }

    /**
     * @param string|null $khutor
     *
     * @return Card
     */
    public function setKhutor(?string $khutor): self
    {
        $this->khutor = $khutor;

        return $this;
    }

    /**
     * @return Collection|Question[]
     */
    public function getQuestions(): Collection
    {
        return $this->questions;
    }

    /**
     * @param iterable|Question[] $questions
     *
     * @return Card
     */
    public function setQuestions(iterable $questions): self
    {
        $this->questions = new ArrayCollection();

        foreach ($questions as $question) {
            $this->addQuestion($question);
        }

        return $this;
    }

    /**
     * @param Question $question
     *
     * @return Card
     */
    public function addQuestion(Question $question): self
    {
        $this->questions[] = $question;

        if (!$this->questions->contains($question)) {
            $this->questions[] = $question;
        }

        return $this;
    }

    /**
     * @param Question $question
     *
     * @return Card
     */
    public function removeQuestion(Question $question): self
    {
        if ($this->questions->contains($question)) {
            $this->questions->removeElement($question);
        }

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
     * @return Season|null
     */
    public function getSeason(): ?Season
    {
        return $this->season;
    }

    /**
     * @param Season|null $season
     *
     * @return Card
     */
    public function setSeason(?Season $season): self
    {
        $this->season = $season;

        return $this;
    }

    /**
     * @return bool
     */
    public function getHasPositiveAnswer(): bool
    {
        return $this->hasPositiveAnswer;
    }

    /**
     * @param bool $hasPositiveAnswer
     *
     * @return Card
     */
    public function setHasPositiveAnswer(bool $hasPositiveAnswer): self
    {
        $this->hasPositiveAnswer = $hasPositiveAnswer;

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
     * @return Collection|Keyword[]
     */
    public function getKeywords(): Collection
    {
        return $this->keywords;
    }

    /**
     * @param iterable|Keyword[] $keywords
     *
     * @return Card
     */
    public function setKeywords(iterable $keywords): self
    {
        $this->keywords = new ArrayCollection();

        foreach ($keywords as $keyword) {
            $this->addKeyword($keyword);
        }

        return $this;
    }

    /**
     * @param Keyword $newKeyword
     *
     * @return Card
     */
    public function addKeyword(Keyword $newKeyword): self
    {
        if (!$this->keywords->contains($newKeyword)) {
            $this->keywords[] = $newKeyword;
            $newKeyword->addCard($this);
        }

        return $this;
    }

    /**
     * @param Keyword $newKeyword
     *
     * @return Card
     */
    public function removeKeyword(Keyword $newKeyword): self
    {
        if ($this->keywords->contains($newKeyword)) {
            $this->keywords->removeElement($newKeyword);
            $newKeyword->removeCard($this);
        }

        return $this;
    }

    /**
     * @return Collection|Term[]
     */
    public function getTerms(): Collection
    {
        return $this->terms;
    }

    /**
     * @param iterable|Term[] $terms
     *
     * @return Card
     */
    public function setTerms(iterable $terms): self
    {
        $this->terms = new ArrayCollection();

        foreach ($terms as $term) {
            $this->addTerm($term);
        }

        return $this;
    }

    /**
     * @param Term $term
     *
     * @return Card
     */
    public function addTerm(Term $term): self
    {
        if (!$this->terms->contains($term)) {
            $this->terms[] = $term;
        }

        return $this;
    }

    /**
     * @param Term $term
     *
     * @return Card
     */
    public function removeTerm(Term $term): self
    {
        if ($this->terms->contains($term)) {
            $this->terms->removeElement($term);
        }

        return $this;
    }

    /**
     * @return Collection|Informer[]
     */
    public function getInformers(): Collection
    {
        return $this->informers;
    }

    /**
     * @param iterable|Informer[] $informers
     *
     * @return Card
     */
    public function setInformers(iterable $informers): self
    {
        $this->informers = new ArrayCollection();

        foreach ($informers as $informer) {
            $this->addInformer($informer);
        }

        return $this;
    }

    /**
     * @param Informer $informer
     *
     * @return Card
     */
    public function addInformer(Informer $informer): self
    {
        if (!$this->informers->contains($informer)) {
            $this->informers[] = $informer;
        }

        return $this;
    }

    /**
     * @param Informer $informer
     *
     * @return Card
     */
    public function removeInformer(Informer $informer): self
    {
        if ($this->informers->contains($informer)) {
            $this->informers->removeElement($informer);
        }

        return $this;
    }

    /**
     * @return Collection|Collector[]
     */
    public function getCollectors(): Collection
    {
        return $this->collectors;
    }

    /**
     * @param iterable|Collector[] $collectors
     *
     * @return Card
     */
    public function setCollectors(iterable $collectors): self
    {
        $this->collectors = new ArrayCollection();

        foreach ($collectors as $collector) {
            $this->addCollector($collector);
        }

        return $this;
    }

    /**
     * @param Collector $collector
     *
     * @return Card
     */
    public function addCollector(Collector $collector): self
    {
        if (!$this->collectors->contains($collector)) {
            $this->collectors[] = $collector;
        }

        return $this;
    }

    /**
     * @param Collector $collector
     *
     * @return Card
     */
    public function removeCollector(Collector $collector): self
    {
        if ($this->collectors->contains($collector)) {
            $this->collectors->removeElement($collector);
        }

        return $this;
    }
}
