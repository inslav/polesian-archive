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

namespace App\ImportDb\Alpha\Storage\ManyToMany;

use App\ImportDb\Alpha\Entity\AlphaTerm;
use App\Persistence\Entity\Card\Term;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class TermStorage extends AbstractManyToManyEntityStorage
{
    /**
     * @return string
     */
    protected function getAlphaEntityClass(): string
    {
        return AlphaTerm::class;
    }

    /**
     * @param object|AlphaTerm $alphaEntity
     *
     * @return string
     */
    protected function getAlphaEntityKey(object $alphaEntity): string
    {
        return $this->getFixedTermName(mb_strtolower($this->valueConverter->getTrimmed($alphaEntity->getTerm())));
    }

    /**
     * @param object|AlphaTerm $alphaEntity
     *
     * @return string|null
     */
    protected function getAlphaCardKey(object $alphaEntity): ?string
    {
        return $alphaEntity->getSpvnkey();
    }

    /**
     * @param object|AlphaTerm $alphaEntity
     *
     * @return object|Term
     */
    protected function createEntity(object $alphaEntity): object
    {
        return (new Term())
            ->setName($this->getAlphaEntityKey($alphaEntity))
        ;
    }

    /**
     * @param string $termName
     *
     * @return string
     */
    private function getFixedTermName(string $termName): string
    {
        $correctTermNameByTermName = [
            'закосрнки' => 'закося́нки',
            'закосрне' => 'закося́не',
            'колрды' => 'коля́ды',
            'козу́ гулрли' => 'козу́ гуля́ли',
            'колрдная' => 'коля́дная',
            'моро́з благославлрли' => 'моро́з благославля́ли',
            'обжи́нка зврже' => 'обжи́нка звя́же',
            'пртница лякáе' => 'пя́тница лякáе',
            'срэдохрсна сэ́рэда' => 'срэдохрэ́сна сэ́рэда',
            'мррец' => 'мерец',
            'колрдники' => 'коля́дники',
            'сврто рожэство́' => 'свя́то рожэство́',
            'сврты вэ́чор' => 'свя́ты вэ́чор',
            'дрдько' => 'дя́дько',
            'веснрнки' => 'весня́нки',
            'виснрночка-краснрночка' => 'висня́ночка-красня́ночка',
            'переврсло' => 'перевя́сло',
            'роко́во сврто' => 'роко́во свя́то',
            'сврто' => 'свя́то',
            'роко́вэ сврто' => 'роко́вэ свя́то',
            'овсрник' => 'овся́ник',
            'близнрта' => 'близня́та',
            'свинрчы дошч' => 'свиня́чы дошч',
            'шулрк' => 'шуля́к',
            'смэрдрчий го́дуд' => 'смэрдя́чий го́дуд',
            'з лрку' => 'з ля́ку',
            'вэснрнки' => 'вэсня́нки',
            'колрда' => 'коля́да',
            'магары́ч оправлрть' => 'магары́ч оправля́ть',
            'закосрнка' => 'закося́нка',
            'мертврк' => 'мертвя́к',
            'колрдныкы' => 'коля́дныкы',
            'ходы́ты з калрдою' => 'ходы́ты з каля́дою',
            'дуплрнка' => 'дупля́нка',
            'вэснрнкы' => 'вэсня́нкы',
            'колрдки' => 'коля́дки',
            'мэ́трвый ро́дич' => 'мэ́ртвый ро́дич',
            'мэртврк' => 'мэртвя́к',
            'свртки' => 'свя́тки',
            'куде́ль нидапррдена' => 'куде́ль нидапря́дена',
            'нидапррдак' => 'нидапря́дак',
            'мрка паляни́ца' => 'мя́ка паляни́ца',
            'мрка паляница' => 'мя́ка паляница',
            'пшенрна каша' => 'пшеня́на каша',
            'шулрчче' => 'шуля́чче',
            'преснрк' => 'пресня́к',
            'колрдныки' => 'коля́дныки',
            'выганрть зиму́' => 'выганя́ть зиму́',
            'одроблрты' => 'одробля́ты',
            'подроблрты' => 'подробля́ты',
            'мак-видрк' => 'мак-видя́к',
            'кулрдни сврта' => 'куля́дни свя́та',
            'роздврны день' => 'роздвя́ны день',
            'пáлют кулрду' => 'пáлют куля́ду',
            'кулрдныкы' => 'куля́дныкы',
            'кулрдки' => 'куля́дки',
            'навро́чат (хазрйке)' => 'навро́чат (хазя́йке)',
            'блызнртка' => 'блызня́тка',
            'роковэ́ сврто' => 'роковэ́ свя́то',
            'росшчинрлы хлеб' => 'росшчиня́лы хлеб',
            'свынрчы дошч' => 'свыня́чы дошч',
            'свынрчый дошч' => 'свыня́чый дошч',
            'двойнртка' => 'двойня́тка',
            'двойнрта' => 'двойня́та',
            'блызьнрта' => 'блызьня́та',
            'замовлрт' => 'замовля́т',
            'перелрк' => 'переля́к',
            'умовлрты' => 'умовля́ты',
            'бороднрк' => 'бородня́к',
            'веснрнкы' => 'весня́нкы',
            'кршэные я́йца' => 'крáшэные я́йца',
            'трусанрт наво́дыла' => 'трусаня́т наво́дыла',
            'вэчэрá свртныя' => 'вэчэрá свя́тныя',
            'десртыха' => 'деся́тыха',
            'гультрй' => 'гультя́й',
            'блызнрта' => 'блызня́та',
            'буснрва лáпка' => 'бусня́ва лáпка',
            'лрки' => 'ля́ки',
            'одроблрть' => 'одробля́ть',
            'пэрэлрк' => 'пэрэля́к',
            'тройнрта' => 'тройня́та',
            'богáта кутр' => 'богáта кутя́',
            'бе́дна кутр' => 'бе́дна кутя́',
            'пе́рша кутр' => 'пе́рша кутя́',
            'пе́рва кутр' => 'пе́рва кутя́',
            'голо́дна кутр' => 'голо́дна кутя́',
            'богáта куттр' => 'богáта куття́',
            'голоўнр' => 'голоўня́',
            'кутр' => 'кутя́',
            'багáта кутр' => 'багáта кутя́',
            'дру́га кутр' => 'дру́га кутя́',
            'водянá кутр' => 'водянá кутя́',
            'пэ́рша кутр' => 'пэ́рша кутя́',
            'водянáя кутр' => 'водянáя кутя́',
            'по́сна кутр' => 'по́сна кутя́',
            'крáшены рйца' => 'крáшены я́йца',
            'свячэ́ны рйца' => 'свячэ́ны я́йца',
            'сбро́сить дитр' => 'сбро́сить дитя́',
            'ски́нуть дитр' => 'ски́нуть дитя́',
            'скрути́ть дитр' => 'скрути́ть дитя́',
            'гало́нна куттр' => 'гало́нна куття́',
            'пэ́рша куттр' => 'пэ́рша куття́',
            'щёдрая кутр' => 'щёдрая кутя́',
            'щёдра кутр' => 'щёдра кутя́',
            'по́сна куттр' => 'по́сна куття́',
            'качáть рйца' => 'качáть я́йца',
            'жировы́е рйца' => 'жировы́е я́йца',
            'на ркбáхэ но́сить' => 'на рубáхэ но́сить',
            'по́сная кутр' => 'по́сная кутя́',
            'волочо́лны рйца' => 'волочо́лны я́йца',
            'крáсины рйца' => 'крáсины я́йца',
            'крáсны рйца' => 'крáсны я́йца',
            'ркашна неде́ля' => 'я́ркашна неде́ля',
            'гуло́дна кутр' => 'гуло́дна кутя́',
            'дитр некры́шчанае' => 'дитя́ некры́шчанае',
            'нишчы́мна кутр' => 'нишчы́мна кутя́',
            'байсетр' => 'байстер',
        ];

        if (\array_key_exists($termName, $correctTermNameByTermName)) {
            return $correctTermNameByTermName[$termName];
        }

        return $termName;
    }
}
