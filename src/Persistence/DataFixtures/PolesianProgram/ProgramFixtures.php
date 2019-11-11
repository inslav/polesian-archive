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

use App\Persistence\Entity\PolesianProgram\Program;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class ProgramFixtures extends Fixture implements DependentFixtureInterface
{
    public const PROGRAM_I = 'I';

    public const PROGRAM_II = 'II';

    public const PROGRAM_XIV = 'XIV';

    public const PROGRAM_XV = 'XV';

    public function load(ObjectManager $manager): void
    {
        $program = (new Program())
            ->setNumber('I')
            ->setName('Свадьба')
            ->setSection($this->getReference(SectionFixtures::SECTION_FAMILY))
        ;
        $manager->persist($program);
        $this->addReference(self::PROGRAM_I, $program);

        $program = (new Program())
            ->setNumber('II')
            ->setName('Родины')
            ->setSection($this->getReference(SectionFixtures::SECTION_FAMILY))
        ;
        $manager->persist($program);
        $this->addReference(self::PROGRAM_II, $program);

        $program = (new Program())
            ->setNumber('XIV')
            ->setName('Астрономия. Метеорология. Время')
            ->setSection($this->getReference(SectionFixtures::SECTION_NATURE))
        ;
        $manager->persist($program);
        $this->addReference(self::PROGRAM_XIV, $program);

        $program = (new Program())
            ->setNumber('XV')
            ->setName('Дождь. Гром. Град')
            ->setSection($this->getReference(SectionFixtures::SECTION_NATURE))
        ;
        $manager->persist($program);
        $this->addReference(self::PROGRAM_XV, $program);

        $manager->flush();
    }

    /**
     * @return string[]
     */
    public function getDependencies(): array
    {
        return [
            SectionFixtures::class,
        ];
    }
}
