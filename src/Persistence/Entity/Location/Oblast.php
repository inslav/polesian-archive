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
 * @ORM\Entity(repositoryClass="App\Persistence\Repository\Location\OblastRepository")
 *
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
class Oblast
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
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $name;

    /**
     * @var Collection|Raion[]
     *
     * @ORM\OneToMany(targetEntity="App\Persistence\Entity\Location\Raion", mappedBy="oblast", orphanRemoval=true)
     */
    private $raions;

    public function __construct()
    {
        $this->raions = new ArrayCollection();
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
     * @return Oblast
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection|Raion[]
     */
    public function getRaions(): Collection
    {
        return $this->raions;
    }

    /**
     * @param Raion $raion
     *
     * @return Oblast
     */
    public function addRaion(Raion $raion): self
    {
        if (!$this->raions->contains($raion)) {
            $this->raions[] = $raion;
            $raion->setOblast($this);
        }

        return $this;
    }

    /**
     * @param Raion $raion
     *
     * @return Oblast
     */
    public function removeRaion(Raion $raion): self
    {
        if ($this->raions->contains($raion)) {
            $this->raions->removeElement($raion);
            if ($raion->getOblast() === $this) {
                $raion->setOblast(null);
            }
        }

        return $this;
    }
}
