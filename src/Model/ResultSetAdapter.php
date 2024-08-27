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

use StingerSoft\EntitySearchBundle\Model\Result\FacetSet;

class ResultSetAdapter implements ResultSet {

	/**
	 *
	 * @var FacetSet
	 */
	protected ?FacetSet $facets = null;

	/**
	 *
	 * @var Document[]
	 */
	protected array $results = array();

	/**
	 *
	 * {@inheritdoc}
	 *
	 * @see \StingerSoft\EntitySearchBundle\Model\ResultSet::getFacets()
	 */
	public function getFacets(): FacetSet {
		return $this->facets;
	}

	public function setFacets(FacetSet $facets): void {
		$this->facets = $facets;
	}

	/**
	 *
	 * {@inheritdoc}
	 *
	 * @see \StingerSoft\EntitySearchBundle\Model\ResultSet::getResults()
	 */
	public function getResults(int $offset = 0, ?int $limit = null): array {
		return array_slice($this->results, $offset, $limit);
	}

	/**
	 *
	 * @param Document[] $results
	 */
	public function setResults(array $results) : void {
		$this->results = $results;
	}

	/**
	 *
	 * {@inheritdoc}
	 *
	 * @see \StingerSoft\EntitySearchBundle\Model\ResultSet::getExcerpt()
	 */
	public function getExcerpt(Document $document) : ?string {
		return null;
	}

	/**
	 *
	 * {@inheritdoc}
	 *
	 * @see \StingerSoft\EntitySearchBundle\Model\ResultSet::getCorrections()
	 */
	public function getCorrections() : ?array {
		return null;
	}
}
