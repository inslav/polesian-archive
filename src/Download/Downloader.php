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

namespace App\Download;

use App\Download\File\DownloadFileInfo;
use App\Download\File\DownloadFileInfoInterface;
use App\Download\Formatter\FormatterInterface;
use App\Persistence\Entity\Card\Card;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class Downloader implements DownloaderInterface
{
    private $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function download(array $cardIds, FormatterInterface $formatter): DownloadFileInfoInterface
    {
        $cards = [];

        if (\count($cardIds) > 0) {
            $queryBuilder = $this->doctrine->getRepository(Card::class)->createQueryBuilder('card');

            $queryBuilder->where($queryBuilder->expr()->in('card.id', $cardIds));

            $cards = $queryBuilder->getQuery()->getResult();
        }

        return new DownloadFileInfo(
            $formatter->getFileName($cards),
            $formatter->format($cards),
            $formatter->getContentType()
        );
    }
}
