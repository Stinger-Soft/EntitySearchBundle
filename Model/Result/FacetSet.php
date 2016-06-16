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

interface FacetSet extends \IteratorAggregate, \Countable {

	/**
	 * Get a facet by key.
	 *
	 * @param string $key
	 *        	The key of the facet
	 *        	
	 * @return integer The amount of results with facet
	 */
	public function getFacet($key);

	/**
	 * Get all facets.
	 *
	 * @return array
	 */
	public function getFacets();
}