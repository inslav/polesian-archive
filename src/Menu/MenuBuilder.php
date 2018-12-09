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

namespace App\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class MenuBuilder
{
    /**
     * @var FactoryInterface
     */
    private $factory;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @param FactoryInterface $factory
     * @param RequestStack     $requestStack
     */
    public function __construct(FactoryInterface $factory, RequestStack $requestStack)
    {
        $this->factory = $factory;
        $this->requestStack = $requestStack;
    }

    /**
     * @param array $options
     *
     * @return ItemInterface
     */
    public function createMainMenu(array $options): ItemInterface
    {
        $currentRoute = $this->requestStack->getCurrentRequest()->get('_route');

        $menu = $this->factory->createItem('root')
            ->setChildrenAttribute('class', 'nav flex-column pa-nav')
        ;

        $menu
            ->addChild('page.menu.index', ['route' => 'index'])
        ;

        $menu
            ->addChild('page.menu.polesianProgram', ['route' => 'polesian_program__index'])
            ->setCurrent(
                \in_array(
                    $currentRoute,
                    [
                        'polesian_program__index',
                        'polesian_program__program',
                        'polesian_program__paragraph',
                        'polesian_program__subparagraph',
                    ],
                    true
                )
            )
        ;

        $menu
            ->addChild('page.menu.dataBase', ['route' => 'card__list'])
            ->setCurrent(\in_array($currentRoute, ['card__list', 'card__show'], true))
        ;

        foreach ($menu->getChildren() as $child) {
            $child
                ->setAttribute('class', 'nav-item pa-nav-item')
                ->setLinkAttribute('class', 'nav-link pa-nav-link')
            ;
        }

        return $menu;
    }
}
