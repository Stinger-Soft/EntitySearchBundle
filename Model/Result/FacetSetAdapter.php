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

namespace StingerSoft\EntitySearchBundle\Model\Result;

class FacetSetAdapter implements FacetSet {

	/**
	 * Facet array.
	 *
	 * @var array
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

	public function addFacetValue(string $key, string $label, $value = null, int $increaseCounterBy = 1) : void {
		$value = $value ?? $label;
		if(!isset($this->facets[$key])) {
			$this->facets[$key] = array();
		}
		if(!isset($this->facets[$key][$label])) {
			$this->facets[$key][$label] = array(
				'value' => $value,
				'count' => 0,
			);
		}
		$this->facets[$key][$label]['count'] += $increaseCounterBy;
	}

	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \StingerSoft\EntitySearchBundle\Model\Result\FacetSet::getFacet()
	 */
	public function getFacet(string $key): ?array {
		if(isset($this->facets[$key])) {
			return $this->facets[$key];
		}
		return null;
	}

	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \StingerSoft\EntitySearchBundle\Model\Result\FacetSet::getFacets()
	 */
	public function getFacets(): array {
		return $this->facets;
	}

	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see IteratorAggregate::getIterator()
	 */
	public function getIterator(): \Iterator {
		return new \ArrayIterator($this->facets);
	}

	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see Countable::count()
	 */
	public function count(): int {
		return count($this->facets);
	}
}