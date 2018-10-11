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

class Query {

	/**
	 *
	 * @var string
	 */
	private $term;

	/**
	 *
	 * @var array
	 */
	private $facets;

	/**
	 *
	 * @var array
	 */
	private $usedFacets = null;

	/**
	 *
	 * @param string $term
	 * @param array $facets
	 * @param array $usedFacets
	 */
	public function __construct(string $term = null, array $facets = array(), array $usedFacets = null) {
		$this->term = $term;
		$this->facets = $facets;
		$this->usedFacets = $usedFacets;
	}

	/**
	 * Get the search term
	 *
	 * @return string Returns the search term
	 */
	public function getSearchTerm(): string {
		return $this->term;
	}

	/**
	 *
	 * @return string[string]
	 */
	public function getFacets(): array {
		return $this->facets;
	}

	public function setFacets(array $facets): Query {
		$this->facets = $facets;
		return $this;
	}

	/**
	 *
	 * @return string[]
	 */
	public function getUsedFacets(): ?array {
		return $this->usedFacets;
	}

	public function setUsedFacets(array $usedFacets): Query {
		$this->usedFacets = $usedFacets;
		return $this;
	}

	public function setSearchTerm(string $term): Query {
		$this->term = $term;
		return $this;
	}

	/**
	 *
	 * @param string $name
	 *            Name of the property to fetch
	 * @return array
	 */
	public function __get($name) {
		if(strrpos($name, 'facet_', -strlen($name)) !== false) {
			$facetname = substr($name, 6);

			if(isset($this->facets[$facetname])) {
				return $this->facets[$facetname];
			}
			return array();
		}
	}

	/**
	 *
	 * @param string $name
	 *            Name of the property to set
	 * @param mixed $value
	 *            The value of the property
	 */
	public function __set($name, $value): void {
		if(strrpos($name, 'facet_', -strlen($name)) !== false) {
			$facetname = substr($name, 6);
			$this->facets[$facetname] = $value;
		}
	}

	/**
	 * Checks whether the given property is set or not
	 *
	 * @param string $name
	 * @return boolean
	 */
	public function __isset($name): bool {
		if(strrpos($name, 'facet_', -strlen($name)) !== false) {
			return true;
		}

		return false;
	}
}