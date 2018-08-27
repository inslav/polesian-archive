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

use App\Entity\Card;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class CardFixtures extends Fixture
{
    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager): void
    {
        $card = new Card();
        $card
            ->setVillage('Барбаров')
            ->setRaion('Мозырский')
            ->setOblast('Гомельская')
            ->setQuestion('10а')
            ->setProgram('I')
            ->setYear(1983)
            ->setText(<<<EOT
[Поют] бáбу, як бая́ра иду́ть:
Насе́ю чэрнабри́ўцаў цэ́лую граду́,
Расьти́те, чэрнобри́ўцы, не буя́йте,
Як приду́ к мáмцы ў го́сьти, зацвитáйте. (ГИК), (ЕИБ), (МСВ)
EOT
            )
            ->setDescription('Свадебная песня о сеянии невестой цветов-чернобривцев.')
            ->setKeywords(['Невеста', 'Сажать', 'Свадебные песни', 'Цвести', 'Цветы'])
            ->setTerms([])
            ->setCollectors(['Гура А. В.'])
            ->setInformers(['МСВ'])
        ;
        $manager->persist($card);

        $card = new Card();
        $card
            ->setVillage('Щедрогор')
            ->setRaion('Ратновский')
            ->setOblast('Волынская')
            ->setQuestion('6')
            ->setProgram('XI')
            ->setYear(1979)
            ->setText(<<<EOT
Кáжуть, што колосо́к був таке́й: скриз от поче́тку до верхá были колоски́, мобу́ть и де́сять колоски́в. Як люди прогреши́ли, то Госпо́дь так хоти́в, шоб вже не було́ людям ния́к жи́та. То Госпо́дь став брáти от спо́да, от зе́мни, так ссуне от низи́ до ве́рха. И стоя́в собáка и каже: "Гав, Го́споди, мени́ став [оставь]". А кот кáже: "Няв, а Го́споди, и мени́ став". И Госпо́дь стáви (аорист - С.Н.) по колоско́ви. И пшени́ца зро́ду ро́дит по одному́ колоску́. Стáры люди кáжуть, што грих би́ти собáку и котá, бо вони́ хле́ба вы́просили у Бога. (САВ)
EOT
            )
            ->setDescription('Легенда о том, что раньше колос начинался от самой земли.')
            ->setKeywords([])
            ->setTerms([])
            ->setCollectors([])
            ->setInformers(['САВ'])
        ;
        $manager->persist($card);

        $card = new Card();
        $card
            ->setVillage('Выступовичи')
            ->setRaion('Овручский')
            ->setOblast('Житомирская')
            ->setQuestion('16б')
            ->setProgram('IV')
            ->setYear(1981)
            ->setText(<<<EOT
Де́ти [коледовщики] хо́дят по хáте и пою́т:
У нáшэй тётки зáйчик.
Дáйти хли́ба крáйчик!"
Им и дают пирогá.
(ШАИ)
EOT
            )
            ->setDescription('Дети колядут, им дают пирога.')
            ->setKeywords([])
            ->setTerms([])
            ->setCollectors([])
            ->setInformers(['ШАИ'])
        ;
        $manager->persist($card);

        $manager->flush();
    }
}
