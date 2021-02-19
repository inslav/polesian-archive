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
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(name="id", type="integer")
     */
    private $id;

    /**
     * @ORM\Column(name="name", type="string", length=255, unique=true)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity="App\Persistence\Entity\Location\Raion", mappedBy="oblast", orphanRemoval=true)
     */
    private $raions;

    public function __construct()
    {
        $this->raions = new ArrayCollection();
    }

    public function __toString(): string
    {
        return sprintf(
            '%s',
            (string) $this->name
        );
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
}
