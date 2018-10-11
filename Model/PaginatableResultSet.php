<?php
declare(strict_types=1);

/*
 * This file is part of the Stinger Entity Search package.
 *
 * (c) Oliver Kotte <oliver.kotte@stinger-soft.net>
 * (c) Florian Meyer <florian.meyer@stinger-soft.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace StingerSoft\EntitySearchBundle\Model;

use Knp\Component\Pager\Pagination\PaginationInterface;

interface PaginatableResultSet {

	/**
	 * @param int $page
	 * @param int $limit
	 * @param array $options
	 * @return PaginationInterface
	 */
	public function paginate(int $page = 1, int $limit = 10, array $options = array()) : PaginationInterface;
}