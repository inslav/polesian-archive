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

namespace App\Persistence\Entity\Location;

use App\Persistence\Entity\Card\Card;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Persistence\Repository\Location\VillageRepository")
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(name="village_of_raion", columns={"name", "raion_id"})})
 *
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
class Village
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(name="id", type="integer")
     */
    private $id;

    /**
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(name="number_in_atlas", type="string", length=255, nullable=true)
     */
    private $numberInAtlas;

    /**
     * @ORM\JoinColumn(name="raion_id", nullable=false)
     * @ORM\ManyToOne(targetEntity="App\Persistence\Entity\Location\Raion", inversedBy="villages")
     */
    private $raion;

    /**
     * @ORM\Column(name="comment", type="text", nullable=true)
     */
    private $comment;

    /**
     * @ORM\OneToMany(targetEntity="App\Persistence\Entity\Card\Card", mappedBy="village", orphanRemoval=true)
     */
    private $cards;

    public function __construct()
    {
        $this->cards = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @return Village
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getNumberInAtlas(): ?string
    {
        return $this->numberInAtlas;
    }

    /**
     * @return Village
     */
    public function setNumberInAtlas(?string $numberInAtlas): self
    {
        $this->numberInAtlas = $numberInAtlas;

        return $this;
    }

    public function getRaion(): ?Raion
    {
        return $this->raion;
    }

    /**
     * @return Village
     */
    public function setRaion(Raion $raion): self
    {
        $this->raion = $raion;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    /**
     * @return Village
     */
    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * @return Collection|Card[]
     */
    public function getCards(): Collection
    {
        return $this->cards;
    }

    /**
     * @param iterable|Card[] $cards
     *
     * @return Village
     */
    public function setCards(iterable $cards): self
    {
        $this->cards = new ArrayCollection();

        foreach ($cards as $card) {
            $this->addCard($card);
        }

        return $this;
    }

    /**
     * @return Village
     */
    public function addCard(Card $card): self
    {
        if (!$this->cards->contains($card)) {
            $this->cards[] = $card;
            $card->setVillage($this);
        }

        return $this;
    }
}
