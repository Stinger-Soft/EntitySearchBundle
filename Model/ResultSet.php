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

use StingerSoft\EntitySearchBundle\Model\Result\Correction;
use StingerSoft\EntitySearchBundle\Model\Result\FacetSet;

interface ResultSet {

	/**
	 * Returns a set of all available facets
	 *
	 * @return FacetSet
	 */
	public function getFacets(): FacetSet;

	/**
	 *
	 * @param int $offset
	 * @param null|int $limit
	 * @return Document[]
	 */
	public function getResults(int $offset = 0, ?int $limit = null): array;

	/**
	 * Returns an excerpt for the given document
	 *
	 * @param Document $document
	 */
	public function getExcerpt(Document $document): ?string;

	/**
	 * @return Correction[]
	 */
	public function getCorrections(): ?array;
}