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

namespace App\Persistence\DataFixtures\PolesianProgram;

use App\Persistence\Entity\PolesianProgram\Section;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class SectionFixtures extends Fixture
{
    public const SECTION_FAMILY = 'family';

    public const SECTION_NATURE = 'nature';

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager): void
    {
        $section = (new Section())
            ->setName('СЕМЕЙНАЯ ОБРЯДНОСТЬ')
        ;
        $manager->persist($section);
        $this->addReference(self::SECTION_FAMILY, $section);

        $section = (new Section())
            ->setName('ПРЕДСТАВЛЕНИЯ О ПРИРОДЕ')
        ;
        $manager->persist($section);
        $this->addReference(self::SECTION_NATURE, $section);

        $manager->flush();
    }
}