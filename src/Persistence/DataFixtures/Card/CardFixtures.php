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

use App\Persistence\DataFixtures\Location\VillageFixtures;
use App\Persistence\Entity\Card\Card;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class CardFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $manager->persist($this->getCard1());

        $manager->persist($this->getCard2());

        $manager->flush();
    }

    /**
     * @return string[]
     */
    public function getDependencies(): array
    {
        return [
            VillageFixtures::class,
            QuestionFixtures::class,
            SeasonFixtures::class,
            KeywordFixtures::class,
            TermFixtures::class,
            CollectorFixtures::class,
            InformantFixtures::class,
        ];
    }

    private function getCard1(): Card
    {
        return (new Card())
            ->setVillage($this->getReference(VillageFixtures::VILLAGE_BARBAROV))
            ->addQuestion($this->getReference(QuestionFixtures::QUESTION_1))
            ->addQuestion($this->getReference(QuestionFixtures::QUESTION_5))
            ->setYear(1983)
            ->setSeason($this->getReference(SeasonFixtures::SEASON_SUMMER))
            ->setHasPositiveAnswer(true)
            ->setText(<<<EOT
[Поют] бáбу, як бая́ра иду́ть:
Насе́ю чэрнабри́ўцаў цэ́лую граду́,
Расьти́те, чэрнобри́ўцы, не буя́йте,
Як приду́ к мáмцы ў го́сьти, зацвитáйте. (ГИК), (ЕИБ), (МСВ)
EOT
            )
            ->setDescription('Свадебная песня о сеянии невестой цветов-чернобривцев.')
            ->setComment('Это комментарий')
            ->setKeywords([
                $this->getReference(KeywordFixtures::KEYWORD_NEVESTA),
                $this->getReference(KeywordFixtures::KEYWORD_SAZHAT),
                $this->getReference(KeywordFixtures::KEYWORD_SVADEBNYE_PESNI),
                $this->getReference(KeywordFixtures::KEYWORD_TSVESTI),
                $this->getReference(KeywordFixtures::KEYWORD_TSVETY),
            ])
            ->setTerms([
                $this->getReference(TermFixtures::TERM_1),
                $this->getReference(TermFixtures::TERM_2),
            ])
            ->setCollectors([
                $this->getReference(CollectorFixtures::COLLECTOR_GURA_A_V),
            ])
            ->setInformants([
                $this->getReference(InformantFixtures::INFORMANT_MSV),
            ])
        ;
    }

    private function getCard2(): Card
    {
        return (new Card())
            ->setVillage($this->getReference(VillageFixtures::VILLAGE_SHCHEDROGOR))
            ->addQuestion($this->getReference(QuestionFixtures::QUESTION_2))
            ->addQuestion($this->getReference(QuestionFixtures::QUESTION_3))
            ->addQuestion($this->getReference(QuestionFixtures::QUESTION_4))
            ->setYear(1979)
            ->setSeason($this->getReference(SeasonFixtures::SEASON_WINTER))
            ->setHasPositiveAnswer(false)
            ->setText(<<<EOT
Кáжуть, што колосо́к був таке́й: скриз от поче́тку до верхá были колоски́, мобу́ть и де́сять колоски́в. Як люди прогреши́ли, то Госпо́дь так хоти́в, шоб вже не було́ людям ния́к жи́та. То Госпо́дь став брáти от спо́да, от зе́мни, так ссуне от низи́ до ве́рха. И стоя́в собáка и каже: "Гав, Го́споди, мени́ став [оставь]". А кот кáже: "Няв, а Го́споди, и мени́ став". И Госпо́дь стáви (аорист - С.Н.) по колоско́ви. И пшени́ца зро́ду ро́дит по одному́ колоску́. Стáры люди кáжуть, што грих би́ти собáку и котá, бо вони́ хле́ба вы́просили у Бога. (САВ)
EOT
            )
            ->setDescription('Легенда о том, что раньше колос начинался от самой земли.')
            ->setKeywords([])
            ->setTerms([])
            ->setCollectors([])
            ->setInformants([])
        ;
    }
}
