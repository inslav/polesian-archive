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

namespace App\Import\Alpha\Storage\ManyToOne;

use App\Import\Alpha\Entity\AlphaCard;
use App\Import\Alpha\ValueTrimmer\AlphaValueConverterInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
abstract class AbstractManyToOneEntityStorage
{
    /**
     * @var ObjectManager
     */
    protected $alphaObjectManager;

    /**
     * @var ObjectManager
     */
    protected $defaultObjectManager;

    /**
     * @var AlphaValueConverterInterface
     */
    protected $valueConverter;

    /**
     * @var array|object[]
     */
    private $entityByAlphaEntityKeyCache = [];

    /**
     * @param RegistryInterface            $doctrine
     * @param AlphaValueConverterInterface $valueConverter
     */
    public function __construct(RegistryInterface $doctrine, AlphaValueConverterInterface $valueConverter)
    {
        $this->alphaObjectManager = $doctrine->getManager('alpha');
        $this->defaultObjectManager = $doctrine->getManager('default');
        $this->valueConverter = $valueConverter;
    }

    /**
     * @param AlphaCard $alphaCard
     *
     * @return object|null
     */
    public function getEntity(AlphaCard $alphaCard): ?object
    {
        $alphaEntityKey = $this->getAlphaEntityKey($alphaCard);

        if (null === $alphaEntityKey) {
            return null;
        }

        if (!array_key_exists($alphaEntityKey, $this->entityByAlphaEntityKeyCache)) {
            $entity = $this->createEntity($alphaCard);

            if (null !== $entity) {
                $this->defaultObjectManager->persist($entity);
            }

            $this->entityByAlphaEntityKeyCache[$alphaEntityKey] = $entity;
        }

        return $this->entityByAlphaEntityKeyCache[$alphaEntityKey];
    }

    /**
     * @param AlphaCard $alphaCard
     *
     * @return string|null
     */
    abstract protected function getAlphaEntityKey(AlphaCard $alphaCard): ?string;

    /**
     * @param AlphaCard $alphaCard
     *
     * @return object|null
     */
    abstract protected function createEntity(AlphaCard $alphaCard): ?object;
}
