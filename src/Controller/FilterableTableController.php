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

namespace App\Controller;

use App\Download\DownloaderInterface;
use App\Download\Formatter\FormatterInterface;
use App\Download\Formatter\Txt\TxtFormatter;
use App\Download\Formatter\Xlsx\XlsxFormatter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/filterable-table")
 *
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class FilterableTableController extends AbstractController
{
    private $downloader;

    private $txtFormatter;

    private $xlsxFormatter;

    public function __construct(
        DownloaderInterface $downloader,
        TxtFormatter $txtFormatter,
        XlsxFormatter $xlsxFormatter
    ) {
        $this->downloader = $downloader;
        $this->txtFormatter = $txtFormatter;
        $this->xlsxFormatter = $xlsxFormatter;
    }

    /**
     * @Route("/download/txt", name="filterable_table__download_txt")
     */
    public function downloadTxt(): Response
    {
        return $this->download($this->txtFormatter);
    }

    /**
     * @Route("/download/xlsx", name="filterable_table__download_xlsx")
     */
    public function downloadXlsx(): Response
    {
        return $this->download($this->xlsxFormatter);
    }

    private function download(FormatterInterface $formatter): Response
    {
        $rowIdentifiers = json_decode(
            $this->get('request_stack')->getCurrentRequest()->query->get('rowIdentifiers'),
            true
        );

        $cardIds = array_map(function (array $rowIdentifier): int {
            return $rowIdentifier['id'];
        }, $rowIdentifiers);

        $downloadFile = $this->downloader->download($cardIds, $formatter);

        return $this->memoryFile(
            $downloadFile->getContent(),
            $downloadFile->getName(),
            $downloadFile->getContentType()
        );
    }

    private function memoryFile(string $content, string $fileName, string $contentType): Response
    {
        $response = new Response($content);

        $response->headers->set('Content-type', $contentType);

        $response->headers->addCacheControlDirective('no-cache');

        $response->headers->set(
            'Content-Disposition',
            $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $fileName)
        );

        return $response;
    }
}
