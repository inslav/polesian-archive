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
use Knp\Menu\ItemInterface;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class CardAdmin extends AbstractEntityAdmin
{
    /**
     * @var string
     */
    protected $baseRouteName = 'polesian_archive_card';

    /**
     * @var string
     */
    protected $baseRoutePattern = 'polesian-archive/card';

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->addIdentifier('id', null, $this->createLabeledListOptions('id'))
            ->add('village', null, $this->createLabeledListOptions('village'))
            ->add('year', null, $this->createLabeledListOptions('year'))
        ;
    }

    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->add(
                'village',
                ModelType::class,
                $this->createLabeledFormOptions('village', ['required' => true])
            )
            ->add(
                'khutor',
                TextType::class,
                $this->createLabeledFormOptions('khutor', ['required' => false])
            )
            ->add(
                'questions',
                ModelType::class,
                $this->createLabeledManyToManyFormOptions('questions', ['btn_add' => false])
            )
            ->add(
                'year',
                IntegerType::class,
                $this->createLabeledFormOptions('year', ['required' => true])
            )
            ->add(
                'season',
                ModelType::class,
                $this->createLabeledFormOptions('season', ['required' => true])
            )
            ->add(
                'hasPositiveAnswer',
                CheckboxType::class,
                $this->createLabeledFormOptions('hasPositiveAnswer', ['required' => false])
            )
            ->add(
                'text',
                TextareaType::class,
                $this->createLabeledFormOptions('text', ['required' => true])
            )
            ->add(
                'description',
                TextareaType::class,
                $this->createLabeledFormOptions('description', ['required' => true])
            )
            ->add(
                'comment',
                TextareaType::class,
                $this->createLabeledFormOptions('comment', ['required' => false])
            )
            ->add(
                'keywords',
                ModelType::class,
                $this->createLabeledManyToManyFormOptions('keywords')
            )
            ->add(
                'terms',
                ModelType::class,
                $this->createLabeledManyToManyFormOptions('terms')
            )
            ->add(
                'informants',
                ModelType::class,
                $this->createLabeledManyToManyFormOptions('informants')
            )
            ->add(
                'collectors',
                ModelType::class,
                $this->createLabeledManyToManyFormOptions('collectors')
            )
//            ->tab('form.card.tab.identification.label')
//                ->with('form.card.section.identification.label')
//                ->end()
//            ->end()
        ;
    }

    /**
     * @param string $action
     */
    protected function configureTabMenu(ItemInterface $menu, $action, AdminInterface $childAdmin = null): void
    {
        if ('edit' === $action || null !== $childAdmin) {
            $admin = $this->isChild() ? $this->getParent() : $this;

            if ((null !== $inscription = $this->getSubject()) && (null !== ($inscriptionId = $inscription->getId()))) {
                $menu->addChild('tabMenu.siteView', [
                    'uri' => $admin->getRouteGenerator()->generate('card__show', [
                        'id' => $inscriptionId,
                    ]),
                ]);
            }
        }
    }
}
