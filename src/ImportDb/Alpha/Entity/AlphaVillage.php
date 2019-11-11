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

namespace App\ImportDb\Alpha\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="selodictfull")
 * @ORM\Entity()
 *
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
class AlphaVillage
{
    /**
     * @var string
     *
     * @ORM\Column(name="selo", type="string")
     */
    private $selo;

    /**
     * @var string
     *
     * @ORM\Column(name="district", type="string")
     */
    private $district;

    /**
     * @var string
     *
     * @ORM\Column(name="region", type="string")
     */
    private $region;

    /**
     * @var string|null
     *
     * @ORM\Column(name="ind", type="string", length=4, nullable=true, options={"fixed"=true})
     */
    private $ind;

    /**
     * @var string|null
     *
     * @ORM\Column(name="atlas", type="string", length=5, nullable=true, options={"fixed"=true})
     */
    private $atlas;

    /**
     * @var string
     *
     * @ORM\Id()
     * @ORM\Column(name="selokey", type="string")
     */
    private $selokey;

    /**
     * @var string|null
     *
     * @ORM\Column(name="opselo", type="string", nullable=true)
     */
    private $opselo;

    public function getSelo(): string
    {
        return $this->selo;
    }

    public function getDistrict(): string
    {
        return $this->district;
    }

    public function getRegion(): string
    {
        return $this->region;
    }

    public function getInd(): ?string
    {
        return $this->ind;
    }

    public function getAtlas(): ?string
    {
        return $this->atlas;
    }

    public function getSelokey(): string
    {
        return $this->selokey;
    }

    public function getOpselo(): ?string
    {
        return $this->opselo;
    }
}
