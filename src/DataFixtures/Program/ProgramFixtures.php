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

namespace App\DataFixtures\Program;

use App\DataFixtures\SectionFixtures;
use App\Entity\Program\Program;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class ProgramFixtures extends Fixture implements DependentFixtureInterface
{
    public const PROGRAM_I = 'I';

    public const PROGRAM_XI = 'XI';

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager): void
    {
        $program = (new Program())
            ->setNumber('I')
            ->setName('Представления о погоде')
            ->setSection($this->getReference(SectionFixtures::SECTION_NATURE))
        ;
        $manager->persist($program);
        $this->addReference(self::PROGRAM_I, $program);

        $program = (new Program())
            ->setNumber('XI')
            ->setName('Представления о животных')
            ->setSection($this->getReference(SectionFixtures::SECTION_NATURE))
        ;
        $manager->persist($program);
        $this->addReference(self::PROGRAM_XI, $program);

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
