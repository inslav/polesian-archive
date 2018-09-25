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

use App\Entity\Question;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class QuestionFixtures extends Fixture implements DependentFixtureInterface
{
    public const PROGRAM_I_QUESTION_10A = 'I-10а';

    public const PROGRAM_XI_QUESTION_6 = 'XI-6';

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager): void
    {
        $question = (new Question())
            ->setNumber(10)
            ->setProgram($this->getReference(ProgramFixtures::PROGRAM_I))
            ->setLetter('а')
            ->setIsAdditional(false)
        ;
        $manager->persist($question);
        $this->addReference(self::PROGRAM_I_QUESTION_10A, $question);

        $question = (new Question())
            ->setNumber(6)
            ->setProgram($this->getReference(ProgramFixtures::PROGRAM_XI))
            ->setIsAdditional(false)
        ;
        $manager->persist($question);
        $this->addReference(self::PROGRAM_XI_QUESTION_6, $question);

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
