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

namespace App\Admin;

use App\Admin\Abstraction\AbstractEntityAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class RaionAdmin extends AbstractEntityAdmin
{
    /**
     * @var string
     */
    protected $baseRouteName = 'polesian_archive_raion';

    /**
     * @var string
     */
    protected $baseRoutePattern = 'polesian-archive/raion';

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->addIdentifier('id', null, $this->createLabeledListOptions('id'))
            ->add('name', null, $this->createLabeledListOptions('name'))
        ;
    }

    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->with('form.raion.section.default.label')
                ->add(
                    'name',
                    TextType::class,
                    $this->createLabeledFormOptions('name', ['required' => true])
                )
                ->add(
                    'oblast',
                    ModelType::class,
                    $this->createLabeledFormOptions('oblast', ['required' => true])
                )
            ->end()
        ;
    }
}
