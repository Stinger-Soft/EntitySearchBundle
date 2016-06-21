<?php

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
	protected $facets = null;
	
	/**
	 * @var Document[]
	 */
	protected $results = array();

	public function setFacets(FacetSet $facets) {
		$this->facets = $facets;
	}

	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \StingerSoft\EntitySearchBundle\Model\ResultSet::getFacets()
	 */
	public function getFacets() {
		return $this->facets;
	}

	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \StingerSoft\EntitySearchBundle\Model\ResultSet::getResults()
	 */
	public function getResults($offset = 0, $limit = null) {
		return array_slice($this->results, $offset, $limit);
	}
	
	/**
	 * @param Document[] $results
	 */
	public function setResults(array $results){
		$this->results = $results;
	}
}