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

use App\Entity\Card;
use App\Import\Program\Question\Number\Formatter\QuestionNumberFormatterInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/card")
 *
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class CardController extends Controller
{
    /**
     * @var QuestionNumberFormatterInterface
     */
    private $questionFormatter;

    /**
     * @param QuestionNumberFormatterInterface $questionFormatter
     */
    public function __construct(QuestionNumberFormatterInterface $questionFormatter)
    {
        $this->questionFormatter = $questionFormatter;
    }

    /**
     * @Route("/list", name="card__list")
     *
     * @Template("card/list.html.twig")
     *
     * @return array
     */
    public function list(): array
    {
        $filterableTable = $this->container->get('vyfony_filterable_table.table_interface');

        return [
            'controller' => 'card',
            'method' => 'list',
            'filterForm' => $filterableTable->getFormView(),
            'table' => $filterableTable->getTableMetadata(),
        ];
    }

    /**
     * @param int $id
     *
     * @Route("/show/{id}", name="card__show")
     *
     * @Template("card/show.html.twig")
     *
     * @return array
     */
    public function show(int $id): array
    {
        return [
            'controller' => 'card',
            'method' => 'show',
            'card' => $this->getDoctrine()->getRepository(Card::class)->find($id),
            'questionNumberFormatter' => $this->questionFormatter,
        ];
    }
}
