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
	 * @var unknown
	 */
	private $term;

	/**
	 * @var string[string]
	 */
	private $facets;

	/**
	 * @param unknown $term
	 */
	public function __construct($term) {
		$this->term = $term;
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
	 * @return string[string]
	 */
	public function getFacets() {
		return $this->facets;
	}
	
}