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

namespace App\Import\Alpha\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="polun")
 * @ORM\Entity()
 *
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
class AlphaCard
{
    /**
     * @var string
     *
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(name="spvnkey", type="string", length=50, options={"fixed"=true})
     */
    private $spvnkey;

    /**
     * @var string|null
     *
     * @ORM\Column(name="selokey", type="string", nullable=true)
     */
    private $selokey;

    /**
     * @var string
     *
     * @ORM\Column(name="hutor", type="string", length=25, options={"fixed"=true})
     */
    private $hutor;

    /**
     * @var string
     *
     * @ORM\Column(name="god", type="string")
     */
    private $god;

    /**
     * @var string|null
     *
     * @ORM\Column(name="sezon", type="string", length=5, nullable=true, options={"fixed"=true})
     */
    private $sezon;

    /**
     * @var string
     *
     * @ORM\Column(name="nprog", type="string", length=7, options={"fixed"=true})
     */
    private $nprog;

    /**
     * @var string
     *
     * @ORM\Column(name="nvopr", type="string", length=5, options={"fixed"=true})
     */
    private $nvopr;

    /**
     * @var string|null
     *
     * @ORM\Column(name="otv", type="string", length=3, nullable=true, options={"fixed"=true})
     */
    private $otv;

    /**
     * @var string
     *
     * @ORM\Column(name="dtext", type="string")
     */
    private $dtext;

    /**
     * @var string
     *
     * @ORM\Column(name="optext", type="string")
     */
    private $optext;

    /**
     * @var string
     *
     * @ORM\Column(name="num", type="decimal", precision=18, scale=0)
     */
    private $num;

    /**
     * @return string|null
     */
    public function getSpvnkey(): ?string
    {
        return $this->spvnkey;
    }

    /**
     * @return string|null
     */
    public function getSelokey(): ?string
    {
        return $this->selokey;
    }

    /**
     * @return string|null
     */
    public function getHutor(): ?string
    {
        return $this->hutor;
    }

    /**
     * @return string|null
     */
    public function getGod(): ?string
    {
        return $this->god;
    }

    /**
     * @return string|null
     */
    public function getSezon(): ?string
    {
        return $this->sezon;
    }

    /**
     * @return string|null
     */
    public function getNprog(): ?string
    {
        return $this->nprog;
    }

    /**
     * @return string|null
     */
    public function getNvopr(): ?string
    {
        return $this->nvopr;
    }

    /**
     * @return string|null
     */
    public function getOtv(): ?string
    {
        return $this->otv;
    }

    /**
     * @return string|null
     */
    public function getDtext(): ?string
    {
        return $this->dtext;
    }

    /**
     * @return string|null
     */
    public function getOptext(): ?string
    {
        return $this->optext;
    }

    /**
     * @return string|null
     */
    public function getNum(): ?string
    {
        return $this->num;
    }
}
