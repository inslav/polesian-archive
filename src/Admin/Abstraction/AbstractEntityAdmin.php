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

namespace App\Admin\Abstraction;

use ReflectionClass;
use Sonata\AdminBundle\Admin\AbstractAdmin;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
abstract class AbstractEntityAdmin extends AbstractAdmin
{
    protected $translationDomain = 'admin';

    public function getLabel(): string
    {
        return 'menu.paragraphs.'.$this->getEntityKey().'.label';
    }

    protected function getEntityKey(): string
    {
        return lcfirst((new ReflectionClass($this->getClass()))->getShortName());
    }

    protected function getFormKeyForFieldName(string $fieldName): string
    {
        return 'form.'.$this->getEntityKey().'.fields.'.$fieldName;
    }

    protected function getListKeyForFieldName(string $fieldName): string
    {
        return 'list.'.$this->getEntityKey().'.fields.'.$fieldName;
    }

    protected function createLabeledListOptions(string $fieldName, array $options = []): array
    {
        return array_merge(
            $options,
            [
                'label' => $this->getListKeyForFieldName($fieldName),
            ]
        );
    }

    protected function createLabeledFormOptions(string $fieldName, array $options = []): array
    {
        return array_merge(
            $options,
            [
                'label' => $this->getFormKeyForFieldName($fieldName),
            ]
        );
    }

    protected function createLabeledManyToManyFormOptions(string $fieldName, array $options = [])
    {
        return $this->createLabeledFormOptions(
            $fieldName,
            array_merge(
                $options,
                ['required' => false, 'multiple' => true]
            )
        );
    }
}
