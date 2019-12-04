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

use Symfony\Component\Dotenv\Dotenv;

require dirname(__DIR__).'/vendor/autoload.php';

if (!array_key_exists('APP_ENV', $_SERVER)) {
    $_SERVER['APP_ENV'] = $_ENV['APP_ENV'] ?? null;
}

if ('prod' !== $_SERVER['APP_ENV']) {
    if (!class_exists(Dotenv::class)) {
        $message =
            'The "APP_ENV" environment variable is not set to "prod". '.
            'Please run "composer require symfony/dotenv" to load the ".env" files configuring the application.';

        throw new RuntimeException($message);
    }

    (new Dotenv())->loadEnv(dirname(__DIR__).'/.env');
}

$_SERVER['APP_ENV'] = $_ENV['APP_ENV'] = $_SERVER['APP_ENV'] ?: $_ENV['APP_ENV'] ?: 'dev';
$_SERVER['APP_DEBUG'] = $_SERVER['APP_DEBUG'] ?? $_ENV['APP_DEBUG'] ?? 'prod' !== $_SERVER['APP_ENV'];
$_SERVER['APP_DEBUG'] = $_ENV['APP_DEBUG'] =
    (int) $_SERVER['APP_DEBUG'] || filter_var($_SERVER['APP_DEBUG'], FILTER_VALIDATE_BOOLEAN) ? '1' : '0';
