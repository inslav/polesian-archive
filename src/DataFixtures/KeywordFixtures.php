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

use App\Entity\Card\Keyword;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class KeywordFixtures extends Fixture
{
    public const KEYWORD_NEVESTA = 'Невеста';

    public const KEYWORD_SAZHAT = 'Сажать';

    public const KEYWORD_SVADEBNYE_PESNI = 'Свадебные песни';

    public const KEYWORD_TSVESTI = 'Цвести';

    public const KEYWORD_TSVETY = 'Цветы';

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager): void
    {
        $keyword = (new Keyword())
            ->setName('Невеста')
        ;
        $manager->persist($keyword);
        $this->addReference(self::KEYWORD_NEVESTA, $keyword);

        $keyword = (new Keyword())
            ->setName('Сажать')
        ;
        $manager->persist($keyword);
        $this->addReference(self::KEYWORD_SAZHAT, $keyword);

        $keyword = (new Keyword())
            ->setName('Свадебные песни')
        ;
        $manager->persist($keyword);
        $this->addReference(self::KEYWORD_SVADEBNYE_PESNI, $keyword);

        $keyword = (new Keyword())
            ->setName('Цвести')
        ;
        $manager->persist($keyword);
        $this->addReference(self::KEYWORD_TSVESTI, $keyword);

        $keyword = (new Keyword())
            ->setName('Цветы')
        ;
        $manager->persist($keyword);
        $this->addReference(self::KEYWORD_TSVETY, $keyword);

        $manager->flush();
    }
}
