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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Persistence\Repository\Location\RaionRepository")
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(name="raion_of_oblast", columns={"name", "oblast_id"})})
 *
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
class Raion
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
     * @var Oblast
     *
     * @ORM\ManyToOne(targetEntity="App\Persistence\Entity\Location\Oblast", inversedBy="raions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $oblast;

    /**
     * @var Collection|Village[]
     *
     * @ORM\OneToMany(targetEntity="App\Persistence\Entity\Location\Village", mappedBy="raion", orphanRemoval=true)
     */
    private $villages;

    public function __construct()
    {
        $this->villages = new ArrayCollection();
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
     * @return Raion
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getOblast(): ?Oblast
    {
        return $this->oblast;
    }

    /**
     * @return Raion
     */
    public function setOblast(Oblast $oblast): self
    {
        $this->oblast = $oblast;

        return $this;
    }

    /**
     * @return Collection|Village[]
     */
    public function getVillages(): Collection
    {
        return $this->villages;
    }

    /**
     * @return Raion
     */
    public function addVillage(Village $village): self
    {
        if (!$this->villages->contains($village)) {
            $this->villages[] = $village;
            $village->setRaion($this);
        }

        return $this;
    }
}
