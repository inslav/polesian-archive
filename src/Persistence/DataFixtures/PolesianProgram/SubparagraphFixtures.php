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

use App\Persistence\Entity\PolesianProgram\Subparagraph;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class SubparagraphFixtures extends Fixture implements DependentFixtureInterface
{
    public const PROGRAM_I_PARAGRAPH_1_SUBPARAGRAPH_A = 'I.1a';

    public const PROGRAM_I_PARAGRAPH_1_SUBPARAGRAPH_B = 'I.1b';

    public const PROGRAM_II_PARAGRAPH_1_SUBPARAGRAPH_A = 'II.1a';

    public const PROGRAM_II_PARAGRAPH_1_SUBPARAGRAPH_B = 'II.1b';

    public const PROGRAM_II_PARAGRAPH_1_SUBPARAGRAPH_V = 'II.1v';

    public const PROGRAM_II_PARAGRAPH_1_SUBPARAGRAPH_G = 'II.1g';

    public const PROGRAM_II_PARAGRAPH_1_SUBPARAGRAPH_D = 'II.1d';

    public const PROGRAM_II_PARAGRAPH_2_SUBPARAGRAPH_A = 'II.2a';

    public const PROGRAM_II_PARAGRAPH_2_SUBPARAGRAPH_B = 'II.2b';

    public const PROGRAM_XV_PARAGRAPH_1_SUBPARAGRAPH_A = 'XV.1a';

    public const PROGRAM_XV_PARAGRAPH_1_SUBPARAGRAPH_B = 'XV.1b';

    public const PROGRAM_XV_PARAGRAPH_1_SUBPARAGRAPH_V = 'XV.1v';

    public const PROGRAM_XV_PARAGRAPH_1_SUBPARAGRAPH_G = 'XV.1g';

    public const PROGRAM_XV_PARAGRAPH_1_SUBPARAGRAPH_D = 'XV.1d';

    public const PROGRAM_XV_PARAGRAPH_1_SUBPARAGRAPH_E = 'XV.1e';

    public const PROGRAM_XV_PARAGRAPH_1_SUBPARAGRAPH_ZH = 'XV.1zh';

    public const PROGRAM_XV_PARAGRAPH_1_SUBPARAGRAPH_Z = 'XV.1z';

    public function load(ObjectManager $manager): void
    {
        $subparagraph = (new Subparagraph())
            ->setLetter('а')
            ->setText(<<<EOT
Как называлась предбрачная церемония в доме невесты, т.е. один или несколько дней перед собственно свадьбой, накануне или незадолго до дня приезда жениха за невестой и окончательным переездом ее к жениху (венки, девоцкие запоины, девич-вечер, коровай и т.д.)?
EOT
)
            ->setParagraph($this->getReference(ParagraphFixtures::PROGRAM_I_PARAGRAPH_1))
        ;
        $manager->persist($subparagraph);
        $this->addReference(self::PROGRAM_I_PARAGRAPH_1_SUBPARAGRAPH_A, $subparagraph);

        $subparagraph = (new Subparagraph())
            ->setLetter('б')
            ->setText(<<<EOT
Какие обряды совершались в это время: печение коровая; изготовление или украшение свадебного деревца; плетение венков; расплетание косы невесты; прощание невесты с подругами, родными, домом, с девичеством в кругу подруг и т.д.?
EOT
)
            ->setParagraph($this->getReference(ParagraphFixtures::PROGRAM_I_PARAGRAPH_1))
        ;
        $manager->persist($subparagraph);
        $this->addReference(self::PROGRAM_I_PARAGRAPH_1_SUBPARAGRAPH_B, $subparagraph);

        $subparagraph = (new Subparagraph())
            ->setLetter('а')
            ->setText(<<<EOT
беременная (череватая, толстая, тяжелая и т.д.);
EOT
)
            ->setParagraph($this->getReference(ParagraphFixtures::PROGRAM_II_PARAGRAPH_1))
        ;
        $manager->persist($subparagraph);
        $this->addReference(self::PROGRAM_II_PARAGRAPH_1_SUBPARAGRAPH_A, $subparagraph);

        $subparagraph = (new Subparagraph())
            ->setLetter('б')
            ->setText(<<<EOT
роженица (породуха, породилья, положница и т.д.);
EOT
)
            ->setParagraph($this->getReference(ParagraphFixtures::PROGRAM_II_PARAGRAPH_1))
        ;
        $manager->persist($subparagraph);
        $this->addReference(self::PROGRAM_II_PARAGRAPH_1_SUBPARAGRAPH_B, $subparagraph);

        $subparagraph = (new Subparagraph())
            ->setLetter('в')
            ->setText(<<<EOT
бесплодная (бездетуха, яловка, курак, курья, галановая и т.д.);
EOT
            )
            ->setParagraph($this->getReference(ParagraphFixtures::PROGRAM_II_PARAGRAPH_1))
        ;
        $manager->persist($subparagraph);
        $this->addReference(self::PROGRAM_II_PARAGRAPH_1_SUBPARAGRAPH_V, $subparagraph);

        $subparagraph = (new Subparagraph())
            ->setLetter('г')
            ->setText(<<<EOT
многодетная (королица, трусиха и т.д.);
EOT
            )
            ->setParagraph($this->getReference(ParagraphFixtures::PROGRAM_II_PARAGRAPH_1))
        ;
        $manager->persist($subparagraph);
        $this->addReference(self::PROGRAM_II_PARAGRAPH_1_SUBPARAGRAPH_G, $subparagraph);

        $subparagraph = (new Subparagraph())
            ->setLetter('д')
            ->setText(<<<EOT
родивпия вне брака (накрытка, покрытка, байстручница и т.д.)?
EOT
            )
            ->setParagraph($this->getReference(ParagraphFixtures::PROGRAM_II_PARAGRAPH_1))
        ;
        $manager->persist($subparagraph);
        $this->addReference(self::PROGRAM_II_PARAGRAPH_1_SUBPARAGRAPH_D, $subparagraph);

        $subparagraph = (new Subparagraph())
            ->setLetter('а')
            ->setText(<<<EOT
Как называются месячные периоды у женщин (сорочка, рубашка, на рубашке, рубашечно и т.д.)?
EOT
            )
            ->setParagraph($this->getReference(ParagraphFixtures::PROGRAM_II_PARAGRAPH_2))
        ;
        $manager->persist($subparagraph);
        $this->addReference(self::PROGRAM_II_PARAGRAPH_2_SUBPARAGRAPH_A, $subparagraph);

        $subparagraph = (new Subparagraph())
            ->setLetter('б')
            ->setText(<<<EOT
Какие запреты соблюдали женщины в это время: нельзя было заниматься определенными видами хозяйственных работ (сеять и сажать, собирать плоды, поливать огород и т.д.), домашних работ (вязать, прясть, топить печь, печь хлеб, рубить капусту, брать воду из реки, колодца и т.д.), участвовать в обрядах (посещать роженицу, быть кумой на крестинах, быть дружкой невесты на свадьбе, участвовать в похоронах или присутствовать при укладывании покойника в гроб и т.д.), ходить в церковь, на кладбище, совершать другие действия (напр., переступать через живое существо, залезать на дерево, прикасаться к плодовому дереву)?
EOT
            )
            ->setParagraph($this->getReference(ParagraphFixtures::PROGRAM_II_PARAGRAPH_2))
        ;
        $manager->persist($subparagraph);
        $this->addReference(self::PROGRAM_II_PARAGRAPH_2_SUBPARAGRAPH_B, $subparagraph);

        $subparagraph = (new Subparagraph())
            ->setLetter('а')
            ->setText(<<<EOT
обход села, полей;
EOT
            )
            ->setParagraph($this->getReference(ParagraphFixtures::PROGRAM_XV_PARAGRAPH_1))
        ;
        $manager->persist($subparagraph);
        $this->addReference(self::PROGRAM_XV_PARAGRAPH_1_SUBPARAGRAPH_A, $subparagraph);

        $subparagraph = (new Subparagraph())
            ->setLetter('б')
            ->setText(<<<EOT
тканье обыденного рушника;
EOT
            )
            ->setParagraph($this->getReference(ParagraphFixtures::PROGRAM_XV_PARAGRAPH_1))
        ;
        $manager->persist($subparagraph);
        $this->addReference(self::PROGRAM_XV_PARAGRAPH_1_SUBPARAGRAPH_B, $subparagraph);

        $subparagraph = (new Subparagraph())
            ->setLetter('в')
            ->setText(<<<EOT
магия у колодца, реки, источника;
EOT
            )
            ->setParagraph($this->getReference(ParagraphFixtures::PROGRAM_XV_PARAGRAPH_1))
        ;
        $manager->persist($subparagraph);
        $this->addReference(self::PROGRAM_XV_PARAGRAPH_1_SUBPARAGRAPH_V, $subparagraph);

        $subparagraph = (new Subparagraph())
            ->setLetter('г')
            ->setText(<<<EOT
поливание водой друг друга, беременной женщины, пастуха, попа и т.д., поливание полей, посевов, дерева, дороги, дома, могилы;
EOT
            )
            ->setParagraph($this->getReference(ParagraphFixtures::PROGRAM_XV_PARAGRAPH_1))
        ;
        $manager->persist($subparagraph);
        $this->addReference(self::PROGRAM_XV_PARAGRAPH_1_SUBPARAGRAPH_G, $subparagraph);

        $subparagraph = (new Subparagraph())
            ->setLetter('д')
            ->setText(<<<EOT
разрушение муравейника;
EOT
            )
            ->setParagraph($this->getReference(ParagraphFixtures::PROGRAM_XV_PARAGRAPH_1))
        ;
        $manager->persist($subparagraph);
        $this->addReference(self::PROGRAM_XV_PARAGRAPH_1_SUBPARAGRAPH_D, $subparagraph);

        $subparagraph = (new Subparagraph())
            ->setLetter('е')
            ->setText(<<<EOT
пахание высохшего русла реки, пахание дороги;
EOT
            )
            ->setParagraph($this->getReference(ParagraphFixtures::PROGRAM_XV_PARAGRAPH_1))
        ;
        $manager->persist($subparagraph);
        $this->addReference(self::PROGRAM_XV_PARAGRAPH_1_SUBPARAGRAPH_E, $subparagraph);

        $subparagraph = (new Subparagraph())
            ->setLetter('ж')
            ->setText(<<<EOT
убиение ужа, лягушки, рака и т.д.;
EOT
            )
            ->setParagraph($this->getReference(ParagraphFixtures::PROGRAM_XV_PARAGRAPH_1))
        ;
        $manager->persist($subparagraph);
        $this->addReference(self::PROGRAM_XV_PARAGRAPH_1_SUBPARAGRAPH_ZH, $subparagraph);

        $subparagraph = (new Subparagraph())
            ->setLetter('з')
            ->setText(<<<EOT
разрушение печи и т.д.?
EOT
            )
            ->setParagraph($this->getReference(ParagraphFixtures::PROGRAM_XV_PARAGRAPH_1))
        ;
        $manager->persist($subparagraph);
        $this->addReference(self::PROGRAM_XV_PARAGRAPH_1_SUBPARAGRAPH_Z, $subparagraph);

        $manager->flush();
    }

    /**
     * @return string[]
     */
    public function getDependencies(): array
    {
        return [
            ParagraphFixtures::class,
        ];
    }
}
