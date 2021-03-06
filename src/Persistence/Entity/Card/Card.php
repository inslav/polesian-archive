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

namespace App\Persistence\Entity\Card;

use App\Formatter\QuestionNumber\Converter\QuestionToQuestionNumberConverter;
use App\Formatter\QuestionNumber\Formatter\QuestionNumberFormatter;
use App\Persistence\Entity\Location\Village;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Persistence\Repository\Card\CardRepository")
 * @ORM\Table(
 *     indexes={
 *         @ORM\Index(columns={"text"}, flags={"fulltext"}),
 *         @ORM\Index(columns={"description"}, flags={"fulltext"}),
 *         @ORM\Index(columns={"year"})
 *     }
 * )
 *
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
class Card
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(name="id", type="integer")
     */
    private $id;

    /**
     * @ORM\JoinColumn(name="village_id", nullable=false)
     * @ORM\ManyToOne(targetEntity="App\Persistence\Entity\Location\Village", inversedBy="cards")
     */
    private $village;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $khutor;

    /**
     * @ORM\ManyToMany(targetEntity="App\Persistence\Entity\Card\Question")
     */
    private $questions;

    /**
     * @ORM\Column(name="year", type="integer")
     */
    private $year;

    /**
     * @ORM\JoinColumn(name="season_id", nullable=true)
     * @ORM\ManyToOne(targetEntity="App\Persistence\Entity\Card\Season", inversedBy="cards")
     */
    private $season;

    /**
     * @ORM\Column(name="has_positive_answer", type="boolean", options={"default": 0})
     */
    private $hasPositiveAnswer;

    /**
     * @ORM\Column(name="text", type="text")
     */
    private $text;

    /**
     * @ORM\Column(name="description", type="text")
     */
    private $description;

    /**
     * @ORM\Column(name="comment", type="text", nullable=true)
     */
    private $comment;

    /**
     * @ORM\ManyToMany(targetEntity="App\Persistence\Entity\Card\Keyword", inversedBy="cards")
     */
    private $keywords;

    /**
     * @ORM\ManyToMany(targetEntity="App\Persistence\Entity\Card\Term", inversedBy="cards")
     */
    private $terms;

    /**
     * @ORM\ManyToMany(targetEntity="App\Persistence\Entity\Card\Informant", inversedBy="cards")
     */
    private $informants;

    /**
     * @ORM\ManyToMany(targetEntity="App\Persistence\Entity\Card\Collector", inversedBy="cards")
     */
    private $collectors;

    public function __construct()
    {
        $this->questions = new ArrayCollection();
        $this->hasPositiveAnswer = false;
        $this->keywords = new ArrayCollection();
        $this->terms = new ArrayCollection();
        $this->informants = new ArrayCollection();
        $this->collectors = new ArrayCollection();
    }

    public function __toString(): string
    {
        return sprintf(
            '%s (%s)',
            (string) $this->id,
            implode(
                ', ',
                $this
                    ->questions
                    ->map(function (Question $question): string {
                        return (new QuestionNumberFormatter(new QuestionToQuestionNumberConverter()))
                            ->formatQuestion($question);
                    })
                    ->toArray()
            )
        );
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getVillage(): ?Village
    {
        return $this->village;
    }

    /**
     * @return Card
     */
    public function setVillage(Village $village): self
    {
        $this->village = $village;

        return $this;
    }

    public function getKhutor(): ?string
    {
        return $this->khutor;
    }

    /**
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
     * @return Card
     */
    public function removeQuestion(Question $question): self
    {
        if ($this->questions->contains($question)) {
            $this->questions->removeElement($question);
        }

        return $this;
    }

    public function getYear(): ?int
    {
        return $this->year;
    }

    /**
     * @return Card
     */
    public function setYear(int $year): self
    {
        $this->year = $year;

        return $this;
    }

    public function getSeason(): ?Season
    {
        return $this->season;
    }

    /**
     * @return Card
     */
    public function setSeason(?Season $season): self
    {
        $this->season = $season;

        return $this;
    }

    public function getHasPositiveAnswer(): bool
    {
        return $this->hasPositiveAnswer;
    }

    /**
     * @return Card
     */
    public function setHasPositiveAnswer(bool $hasPositiveAnswer): self
    {
        $this->hasPositiveAnswer = $hasPositiveAnswer;

        return $this;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    /**
     * @return Card
     */
    public function setText(string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @return Card
     */
    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    /**
     * @return Card
     */
    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

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
     * @return Collection|Informant[]
     */
    public function getInformants(): Collection
    {
        return $this->informants;
    }

    /**
     * @param iterable|Informant[] $informants
     *
     * @return Card
     */
    public function setInformants(iterable $informants): self
    {
        $this->informants = new ArrayCollection();

        foreach ($informants as $informant) {
            $this->addInformant($informant);
        }

        return $this;
    }

    /**
     * @return Card
     */
    public function addInformant(Informant $informant): self
    {
        if (!$this->informants->contains($informant)) {
            $this->informants[] = $informant;
        }

        return $this;
    }

    /**
     * @return Card
     */
    public function removeInformant(Informant $informant): self
    {
        if ($this->informants->contains($informant)) {
            $this->informants->removeElement($informant);
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
