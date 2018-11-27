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

namespace App\DataFixtures;

use App\DataFixtures\Program\ProgramFixtures;
use App\Entity\Program\Paragraph;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class ParagraphFixtures extends Fixture implements DependentFixtureInterface
{
    public const PROGRAM_I_PARAGRAPH_10 = 'I.10';

    public const PROGRAM_XI_PARAGRAPH_6 = 'XI.6';

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager): void
    {
        $paragraph = (new Paragraph())
            ->setNumber(10)
            ->setTitle('Снег')
            ->setText('У природы нет плохой погоды?')
            ->setProgram($this->getReference(ProgramFixtures::PROGRAM_I))
        ;
        $manager->persist($paragraph);
        $this->addReference(self::PROGRAM_I_PARAGRAPH_10, $paragraph);

        $paragraph = (new Paragraph())
            ->setProgram($this->getReference(ProgramFixtures::PROGRAM_XI))
            ->setNumber(6)
            ->setTitle('Зной')
            ->setText('Как назывался зной:')
        ;
        $manager->persist($paragraph);
        $this->addReference(self::PROGRAM_XI_PARAGRAPH_6, $paragraph);

        $manager->flush();
    }

    /**
     * @return string[]
     */
    public function getDependencies(): array
    {
        return [
            ProgramFixtures::class,
        ];
    }
}
