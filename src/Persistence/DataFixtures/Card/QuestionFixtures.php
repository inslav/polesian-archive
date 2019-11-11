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

namespace App\Persistence\DataFixtures\Card;

use App\Persistence\DataFixtures\PolesianProgram\ParagraphFixtures;
use App\Persistence\DataFixtures\PolesianProgram\ProgramFixtures;
use App\Persistence\DataFixtures\PolesianProgram\SubparagraphFixtures;
use App\Persistence\Entity\Card\Question;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class QuestionFixtures extends Fixture implements DependentFixtureInterface
{
    public const QUESTION_1 = 'question-1';

    public const QUESTION_2 = 'question-2';

    public const QUESTION_3 = 'question-3';

    public const QUESTION_4 = 'question-4';

    public const QUESTION_5 = 'question-5';

    public const QUESTION_6 = 'question-6';

    public const QUESTION_7 = 'question-7';

    public const QUESTION_8 = 'question-8';

    public const QUESTION_9 = 'question-9';

    public function load(ObjectManager $manager): void
    {
        $question = (new Question())
            ->setProgram($this->getReference(ProgramFixtures::PROGRAM_I))
            ->setIsAdditional(true)
        ;
        $manager->persist($question);
        $this->addReference(self::QUESTION_1, $question);

        $question = (new Question())
            ->setProgram($this->getReference(ProgramFixtures::PROGRAM_I))
            ->setParagraph($this->getReference(ParagraphFixtures::PROGRAM_I_PARAGRAPH_1))
            ->setIsAdditional(false)
        ;
        $manager->persist($question);
        $this->addReference(self::QUESTION_2, $question);

        $question = (new Question())
            ->setProgram($this->getReference(ProgramFixtures::PROGRAM_I))
            ->setParagraph($this->getReference(ParagraphFixtures::PROGRAM_I_PARAGRAPH_1))
            ->setSubparagraph($this->getReference(SubparagraphFixtures::PROGRAM_I_PARAGRAPH_1_SUBPARAGRAPH_A))
            ->setIsAdditional(false)
        ;
        $manager->persist($question);
        $this->addReference(self::QUESTION_3, $question);

        $question = (new Question())
            ->setProgram($this->getReference(ProgramFixtures::PROGRAM_I))
            ->setParagraph($this->getReference(ParagraphFixtures::PROGRAM_I_PARAGRAPH_1))
            ->setSubparagraph($this->getReference(SubparagraphFixtures::PROGRAM_I_PARAGRAPH_1_SUBPARAGRAPH_B))
            ->setIsAdditional(false)
        ;
        $manager->persist($question);
        $this->addReference(self::QUESTION_4, $question);

        $question = (new Question())
            ->setProgram($this->getReference(ProgramFixtures::PROGRAM_II))
            ->setParagraph($this->getReference(ParagraphFixtures::PROGRAM_II_PARAGRAPH_1))
            ->setSubparagraph($this->getReference(SubparagraphFixtures::PROGRAM_II_PARAGRAPH_1_SUBPARAGRAPH_A))
            ->setIsAdditional(true)
        ;
        $manager->persist($question);
        $this->addReference(self::QUESTION_5, $question);

        $question = (new Question())
            ->setProgram($this->getReference(ProgramFixtures::PROGRAM_II))
            ->setParagraph($this->getReference(ParagraphFixtures::PROGRAM_II_PARAGRAPH_2))
            ->setSubparagraph($this->getReference(SubparagraphFixtures::PROGRAM_II_PARAGRAPH_2_SUBPARAGRAPH_B))
            ->setIsAdditional(false)
        ;
        $manager->persist($question);
        $this->addReference(self::QUESTION_6, $question);

        $question = (new Question())
            ->setProgram($this->getReference(ProgramFixtures::PROGRAM_XIV))
            ->setParagraph($this->getReference(ParagraphFixtures::PROGRAM_XIV_PARAGRAPH_1))
            ->setSubparagraph(null)
            ->setIsAdditional(false)
        ;
        $manager->persist($question);
        $this->addReference(self::QUESTION_7, $question);

        $question = (new Question())
            ->setProgram($this->getReference(ProgramFixtures::PROGRAM_XV))
            ->setParagraph($this->getReference(ParagraphFixtures::PROGRAM_XV_PARAGRAPH_1))
            ->setSubparagraph($this->getReference(SubparagraphFixtures::PROGRAM_XV_PARAGRAPH_1_SUBPARAGRAPH_Z))
            ->setIsAdditional(false)
        ;
        $manager->persist($question);
        $this->addReference(self::QUESTION_8, $question);

        $question = (new Question())
            ->setProgram($this->getReference(ProgramFixtures::PROGRAM_XV))
            ->setParagraph($this->getReference(ParagraphFixtures::PROGRAM_XV_PARAGRAPH_2))
            ->setSubparagraph(null)
            ->setIsAdditional(true)
        ;
        $manager->persist($question);
        $this->addReference(self::QUESTION_9, $question);

        $manager->flush();
    }

    /**
     * @return string[]
     */
    public function getDependencies(): array
    {
        return [
            ProgramFixtures::class,
            ParagraphFixtures::class,
            SubparagraphFixtures::class,
        ];
    }
}
