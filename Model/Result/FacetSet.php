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
namespace StingerSoft\EntitySearchBundle\Model\Result;

interface FacetSet extends \IteratorAggregate, \Countable {

	const FACET_AUTHOR = 'facet_author';

	const FACET_ENTITY_TYPE = 'facet_entity_type';

	const FACET_MIME_TYPE = 'facet_mime_type';

	const FACET_EDITOR = 'facet_editor';

	/**
	 * Get a facet by key.
	 *
	 * @param string $key
	 *        	The key of the facet
	 *        	
	 * @return string[string][int] The amount of results with facet
	 */
	public function getFacet($key);

	/**
	 * Get all facets.
	 *
	 * @return string[string][int]
	 */
	public function getFacets();
}