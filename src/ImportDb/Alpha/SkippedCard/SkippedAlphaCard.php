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

namespace App\ImportDb\Alpha\SkippedCard;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class SkippedAlphaCard implements SkippedAlphaCardInterface
{
    /**
     * @var string
     */
    private $spvnkey;

    /**
     * @var string|null
     */
    private $selokey;

    /**
     * @var string
     */
    private $hutor;

    /**
     * @var string
     */
    private $god;

    /**
     * @var string|null
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
     * @param string      $spvnkey
     * @param string|null $selokey
     * @param string      $hutor
     * @param string      $god
     * @param string|null $sezon
     * @param string      $nprog
     * @param string      $nvopr
     * @param string|null $otv
     * @param string      $dtext
     * @param string      $optext
     * @param string      $num
     */
    public function __construct(
        string $spvnkey,
        ?string $selokey,
        string $hutor,
        string $god,
        ?string $sezon,
        string $nprog,
        string $nvopr,
        ?string $otv,
        string $dtext,
        string $optext,
        string $num
    ) {
        $this->spvnkey = $spvnkey;
        $this->selokey = $selokey;
        $this->hutor = $hutor;
        $this->god = $god;
        $this->sezon = $sezon;
        $this->nprog = $nprog;
        $this->nvopr = $nvopr;
        $this->otv = $otv;
        $this->dtext = $dtext;
        $this->optext = $optext;
        $this->num = $num;
    }

    /**
     * @return string
     */
    public function getSpvnkey(): string
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
     * @return string
     */
    public function getHutor(): string
    {
        return $this->hutor;
    }

    /**
     * @return string
     */
    public function getGod(): string
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
     * @return string
     */
    public function getNprog(): string
    {
        return $this->nprog;
    }

    /**
     * @return string
     */
    public function getNvopr(): string
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
     * @return string
     */
    public function getDtext(): string
    {
        return $this->dtext;
    }

    /**
     * @return string
     */
    public function getOptext(): string
    {
        return $this->optext;
    }

    /**
     * @return string
     */
    public function getNum(): string
    {
        return $this->num;
    }

    /**
     * @return mixed
     */
    public function jsonSerialize(): array
    {
        $array = [];

        foreach ($this as $property => $value) {
            $array[$property] = $value;
        }

        return $array;
    }
}
