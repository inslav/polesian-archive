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

use App\Entity\Program\Subparagraph;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class SubparagraphFixtures extends Fixture implements DependentFixtureInterface
{
    public const PROGRAM_XI_PARAGRAPH_6_SUBPARAGRAPH_A = 'XI.6а';

    public const PROGRAM_XI_PARAGRAPH_6_SUBPARAGRAPH_B = 'XI.6б';

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager): void
    {
        $subparagraph = (new Subparagraph())
            ->setLetter('а')
            ->setText('зной;')
            ->setParagraph($this->getReference(ParagraphFixtures::PROGRAM_XI_PARAGRAPH_6))
        ;
        $manager->persist($subparagraph);
        $this->addReference(self::PROGRAM_XI_PARAGRAPH_6_SUBPARAGRAPH_A, $subparagraph);

        $subparagraph = (new Subparagraph())
            ->setLetter('б')
            ->setText('жара;')
            ->setParagraph($this->getReference(ParagraphFixtures::PROGRAM_XI_PARAGRAPH_6))
        ;
        $manager->persist($subparagraph);
        $this->addReference(self::PROGRAM_XI_PARAGRAPH_6_SUBPARAGRAPH_B, $subparagraph);

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
