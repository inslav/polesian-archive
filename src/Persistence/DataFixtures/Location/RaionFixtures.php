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

namespace App\Persistence\DataFixtures\Location;

use App\Persistence\Entity\Location\Raion;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class RaionFixtures extends Fixture implements DependentFixtureInterface
{
    const RAION_MOZYRSKIY = 'Мозырский';

    const RAION_RATNOVSKIY = 'Ратновский';

    public function load(ObjectManager $manager): void
    {
        $raion = (new Raion())
            ->setName('Мозырский')
            ->setOblast($this->getReference(OblastFixtures::OBLAST_GOMELSKAYA))
        ;
        $manager->persist($raion);
        $this->addReference(self::RAION_MOZYRSKIY, $raion);

        $raion = (new Raion())
            ->setName('Ратновский')
            ->setOblast($this->getReference(OblastFixtures::OBLAST_VOLYNSKAYA))
        ;
        $manager->persist($raion);
        $this->addReference(self::RAION_RATNOVSKIY, $raion);

        $manager->flush();
    }

    /**
     * @return string[]
     */
    public function getDependencies(): array
    {
        return [
            OblastFixtures::class,
        ];
    }
}
