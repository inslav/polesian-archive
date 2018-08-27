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

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class CardControllerTest extends WebTestCase
{
    public function testList(): void
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/card/list');

        $this->assertSame(200, $client->getResponse()->getStatusCode(), 'Response status code');
        $this->assertSame(
            'Список карточек – База данных Полесского архива',
            trim($crawler->filter('head title')->text()),
            'Page title'
        );

        $this->assertSame(1, $crawler->filter('.pa-header')->count(), 'Header area');
        $this->assertSame(1, $crawler->filter('.pa-sidebar')->count(), 'Sidebar area');
        $this->assertSame(1, $crawler->filter('.pa-main')->count(), 'Main area');
        $this->assertSame(1, $crawler->filter('.pa-footer')->count(), 'Footer area');
    }

    public function testShow(): void
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/card/show/1');

        $this->assertSame(200, $client->getResponse()->getStatusCode(), 'Response status code');
        $this->assertSame(
            'Карточка №1 – База данных Полесского архива',
            trim($crawler->filter('head title')->text()),
            'Page title'
        );

        $this->assertSame(1, $crawler->filter('.pa-header')->count(), 'Header area');
        $this->assertSame(1, $crawler->filter('.pa-sidebar')->count(), 'Sidebar area');
        $this->assertSame(1, $crawler->filter('.pa-main')->count(), 'Main area');
        $this->assertSame(1, $crawler->filter('.pa-footer')->count(), 'Footer area');
    }
}
