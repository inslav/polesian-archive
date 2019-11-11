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

use App\Import\Card\Formatter\QuestionNumber\Formatter\QuestionNumberFormatterInterface;
use App\Persistence\Repository\Card\CardRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Vyfony\Bundle\FilterableTableBundle\Table\TableInterface;

/**
 * @Route("/card")
 *
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class CardController extends AbstractController
{
    /**
     * @var TableInterface
     */
    private $filterableTable;

    /**
     * @var QuestionNumberFormatterInterface
     */
    private $questionFormatter;

    /**
     * @var CardRepository
     */
    private $cardRepository;

    public function __construct(
        TableInterface $filterableTable,
        QuestionNumberFormatterInterface $questionFormatter,
        CardRepository $cardRepository
    ) {
        $this->filterableTable = $filterableTable;
        $this->questionFormatter = $questionFormatter;
        $this->cardRepository = $cardRepository;
    }

    /**
     * @Route("/list", name="card__list")
     *
     * @Template("card/list.html.twig")
     */
    public function list(): array
    {
        return [
            'controller' => 'card',
            'method' => 'list',
            'filterForm' => $this->filterableTable->getFormView(),
            'table' => $this->filterableTable->getTableMetadata(),
        ];
    }

    /**
     * @Route("/show/{id}", name="card__show")
     *
     * @Template("card/show.html.twig")
     */
    public function show(int $id): array
    {
        return [
            'controller' => 'card',
            'method' => 'show',
            'card' => $this->cardRepository->find($id),
            'questionNumberFormatter' => $this->questionFormatter,
        ];
    }
}
