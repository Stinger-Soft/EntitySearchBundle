<?php

/*
 * This file is part of the Stinger Enity Search package.
 *
 * (c) Oliver Kotte <oliver.kotte@stinger-soft.net>
 * (c) Florian Meyer <florian.meyer@stinger-soft.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace StingerSoft\EntitySearchBundle\Model\Result;

class FacetSetAdapter implements FacetSet {

	/**
	 * Facet array.
	 *
	 * @var int[string]
	 */
	protected $facets;

	/**
	 * Constructor.
	 *
	 * @param int[string] $facets        	
	 */
	public function __construct(array $facets = array()) {
		$this->facets = $facets;
	}

	public function addFacetValue($key, $value, $increaseCounterBy = 1) {
		if(!isset($this->facets[$key])){
			$this->facets[$key] = array();
		}
		if(!isset($this->facets[$key][$value])){
			$this->facets[$key][$value] = 0;
		}
		$this->facets[$key][$value] = $this->facets[$key][$value] + $increaseCounterBy;
	}

	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \StingerSoft\EntitySearchBundle\Model\Result\FacetSet::getFacet()
	 */
	public function getFacet($key) {
		if(isset($this->facets[$key])) {
			return $this->facets[$key];
		} else {
			return;
		}
	}

	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \StingerSoft\EntitySearchBundle\Model\Result\FacetSet::getFacets()
	 */
	public function getFacets() {
		return $this->facets;
	}

	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see IteratorAggregate::getIterator()
	 */
	public function getIterator() {
		return new \ArrayIterator($this->facets);
	}

	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see Countable::count()
	 */
	public function count() {
		return count($this->facets);
	}
}