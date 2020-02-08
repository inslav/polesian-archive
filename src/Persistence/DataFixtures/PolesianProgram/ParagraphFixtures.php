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

use App\Persistence\Entity\PolesianProgram\Paragraph;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class ParagraphFixtures extends Fixture implements DependentFixtureInterface
{
    public const PROGRAM_I_PARAGRAPH_1 = 'I.1';

    public const PROGRAM_I_PARAGRAPH_2 = 'I.2';

    public const PROGRAM_II_PARAGRAPH_1 = 'II.1';

    public const PROGRAM_II_PARAGRAPH_2 = 'II.2';

    public const PROGRAM_XIV_PARAGRAPH_1 = 'XIV.1';

    public const PROGRAM_XIV_PARAGRAPH_2 = 'XIV.2';

    public const PROGRAM_XV_PARAGRAPH_1 = 'XV.1';

    public const PROGRAM_XV_PARAGRAPH_2 = 'XV.2';

    public function load(ObjectManager $manager): void
    {
        $paragraph = (new Paragraph())
            ->setNumber(1)
            ->setTitle('Канун свадьбы')
            ->setProgram($this->getReference(ProgramFixtures::PROGRAM_I))
        ;
        $manager->persist($paragraph);
        $this->addReference(self::PROGRAM_I_PARAGRAPH_1, $paragraph);

        $paragraph = (new Paragraph())
            ->setProgram($this->getReference(ProgramFixtures::PROGRAM_I))
            ->setNumber(2)
            ->setTitle('Коровай')
            ->setText(<<<EOT
Как выглядел свадебный коровай? Зарисуйте его схематически сверху и и обозначьте стрелками каждую деталь. Укажите названия всех его деталей и украшений, а именно: подошвы; обода; фигурок из теста (птички, цветочки, шишки, завитушки, месяц, солнце, звезды и т.д.); узоров (крест, елочка, растительный орнамент, защипы на тесте и т.д.); предметов, которые запекаются в него (деньги, яйца и т.д.); кладутся сверху (венок, фигурки не из теста и т.д.); втыкаются в него (растительность, деревце, ветки, палки и т.д.).
EOT
)
        ;
        $manager->persist($paragraph);
        $this->addReference(self::PROGRAM_I_PARAGRAPH_2, $paragraph);

        $paragraph = (new Paragraph())
            ->setNumber(1)
            ->setText(<<<EOT
Как называется женщина:
EOT
)
            ->setProgram($this->getReference(ProgramFixtures::PROGRAM_II))
        ;
        $manager->persist($paragraph);
        $this->addReference(self::PROGRAM_II_PARAGRAPH_1, $paragraph);

        $paragraph = (new Paragraph())
            ->setNumber(2)
            ->setTitle('Месячные')
            ->setProgram($this->getReference(ProgramFixtures::PROGRAM_II))
        ;
        $manager->persist($paragraph);
        $this->addReference(self::PROGRAM_II_PARAGRAPH_2, $paragraph);

        $paragraph = (new Paragraph())
            ->setNumber(1)
            ->setText(<<<EOT
Как называются Плеяды (копы, бабки, курки, восожары и т.д.)?
EOT
)
            ->setProgram($this->getReference(ProgramFixtures::PROGRAM_XIV))
        ;
        $manager->persist($paragraph);
        $this->addReference(self::PROGRAM_XIV_PARAGRAPH_1, $paragraph);

        $paragraph = (new Paragraph())
            ->setNumber(2)
            ->setText(<<<EOT
Как называется созвездие Орион (косари, косы, чепиги и т.д.)?
EOT
            )
            ->setProgram($this->getReference(ProgramFixtures::PROGRAM_XIV))
        ;
        $manager->persist($paragraph);
        $this->addReference(self::PROGRAM_XIV_PARAGRAPH_2, $paragraph);

        $paragraph = (new Paragraph())
            ->setNumber(1)
            ->setText(<<<EOT
Какие действия совершались прежде во время засухи (или в другое время) с целью вызвать дождь или предупредить засуху:
EOT
            )
            ->setProgram($this->getReference(ProgramFixtures::PROGRAM_XV))
        ;
        $manager->persist($paragraph);
        $this->addReference(self::PROGRAM_XV_PARAGRAPH_1, $paragraph);

        $paragraph = (new Paragraph())
            ->setNumber(2)
            ->setText(<<<EOT
Совершались ли во время засухи или в другое время (когда?) какие-либо действия у колодца: бросали в колодец мак, чеснок, хлеб и т.п., горшки, горшок с борщом, черепки, кирпичи; колотили воду палками; голосили по утопленнику и т.д.?
EOT
            )
            ->setProgram($this->getReference(ProgramFixtures::PROGRAM_XV))
        ;
        $manager->persist($paragraph);
        $this->addReference(self::PROGRAM_XV_PARAGRAPH_2, $paragraph);

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
