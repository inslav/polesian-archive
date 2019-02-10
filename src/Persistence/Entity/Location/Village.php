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
    private $name;

    /**
     * @var Raion
     *
     * @ORM\ManyToOne(targetEntity="App\Persistence\Entity\Location\Raion", inversedBy="villages")
     * @ORM\JoinColumn(nullable=false)
     */
    private $raion;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $comment;

    /**
     * @var Collection|Card[]
     *
     * @ORM\OneToMany(targetEntity="App\Persistence\Entity\Card\Card", mappedBy="village", orphanRemoval=true)
     */
    private $cards;

    public function __construct()
    {
        $this->cards = new ArrayCollection();
    }

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
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return Village
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Raion|null
     */
    public function getRaion(): ?Raion
    {
        return $this->raion;
    }

    /**
     * @param Raion|null $raion
     *
     * @return Village
     */
    public function setRaion(?Raion $raion): self
    {
        $this->raion = $raion;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getComment(): ?string
    {
        return $this->comment;
    }

    /**
     * @param string|null $comment
     *
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
     * @param Card $card
     *
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

    /**
     * @param Card $card
     *
     * @return Village
     */
    public function removeCard(Card $card): self
    {
        if ($this->cards->contains($card)) {
            $this->cards->removeElement($card);
            if ($card->getVillage() === $this) {
                $card->setVillage(null);
            }
        }

        return $this;
    }
}
