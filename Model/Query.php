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

class Query {

	/**
	 *
	 * @var string
	 */
	private $term;

	/**
	 *
	 * @var string[string]
	 */
	private $facets = array();

	/**
	 *
	 * @var string[]
	 */
	private $usedFacets = null;

	/**
	 *
	 * @param string $term        	
	 * @param string[string] $facets        	
	 * @param string[] $usedFacets        	
	 */
	public function __construct($term = null, array $facets = array(), $usedFacets = null) {
		$this->term = $term;
		$this->facets = $facets;
		$this->usedFacets = $usedFacets;
	}

	/**
	 * Get the search term
	 *
	 * @return string Returns the search term
	 */
	public function getSearchTerm() {
		return $this->term;
	}

	/**
	 *
	 * @return string[string]
	 */
	public function getFacets() {
		return $this->facets;
	}

	/**
	 *
	 * @return string[]
	 */
	public function getUsedFacets() {
		return $this->usedFacets;
	}

	public function setSearchTerm($term) {
		$this->term = $term;
		return $this;
	}

	public function setFacets($facets) {
		$this->facets = $facets;
		return $this;
	}

	public function setUsedFacets($usedFacets) {
		$this->usedFacets = $usedFacets;
		return $this;
	}

	/**
	 *
	 * @param string $name
	 *        	Name of the property to fetch
	 * @return \Pec\Bundle\MtuDvpBundle\Entity\TestRiskValue
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
	 *        	Name of the property to set
	 * @param mixed $value
	 *        	The value of the property
	 */
	public function __set($name, $value) {
		debug_print_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 10);
		if(strrpos($name, 'facet_', -strlen($name)) !== false) {
			$facetname = substr($name, 6);
			
			if(!isset($this->facets[$facetname])) {
				$this->facets[$facetname] = array();
			}
			$this->facets[$facetname] = $value;
		}
	}

	/**
	 * Checks whether the given property is set or not
	 *
	 * @param string $name        	
	 * @return boolean
	 */
	public function __isset($name) {
		if(strrpos($name, 'facet_', -strlen($name)) !== false) {
			return true;
		}
		
		return false;
	}
}