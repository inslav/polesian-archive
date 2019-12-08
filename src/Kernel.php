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

namespace App;

use Exception;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Exception\LoaderLoadException;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\RouteCollectionBuilder;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    private const CONFIG_EXTENSION = '.yaml';

    public function registerBundles(): iterable
    {
        $bundles = require $this->getBundlesDeclarationFile();

        foreach ($bundles as $class => $environments) {
            if ($environments[$this->environment] ?? $environments['all'] ?? false) {
                yield new $class();
            }
        }
    }

    public function getProjectDir(): string
    {
        return \dirname(__DIR__);
    }

    /**
     * @throws Exception
     */
    protected function configureContainer(ContainerBuilder $container, LoaderInterface $loader): void
    {
        $container->addResource(new FileResource($this->getBundlesDeclarationFile()));

        $container->setParameter(
            'container.dumper.inline_class_loader',
            \PHP_VERSION_ID < 70400 || !ini_get('opcache.preload')
        );
        $container->setParameter('container.dumper.inline_factories', true);

        $configDir = $this->getConfigDir();

        $loader->load($configDir.'/{packages}/*'.self::CONFIG_EXTENSION, 'glob');
        $loader->load($configDir.'/{packages}/'.$this->environment.'/*'.self::CONFIG_EXTENSION, 'glob');
        $loader->load($configDir.'/{services}'.self::CONFIG_EXTENSION, 'glob');
        $loader->load($configDir.'/{services}_'.$this->environment.self::CONFIG_EXTENSION, 'glob');
    }

    /**
     * @throws LoaderLoadException
     */
    protected function configureRoutes(RouteCollectionBuilder $routes): void
    {
        $routesDir = $this->getConfigDir().'/{routes}';

        $routes->import($routesDir.'/'.$this->environment.'/*'.self::CONFIG_EXTENSION, '/', 'glob');
        $routes->import($routesDir.'/*'.self::CONFIG_EXTENSION, '/', 'glob');
        $routes->import($routesDir.self::CONFIG_EXTENSION, '/', 'glob');
    }

    private function getConfigDir(): string
    {
        return $this->getProjectDir().'/config';
    }

    private function getBundlesDeclarationFile(): string
    {
        return $this->getConfigDir().'/bundles.php';
    }
}
