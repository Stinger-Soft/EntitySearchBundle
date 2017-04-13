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
use StingerSoft\EntitySearchBundle\Model\Result\Correction;

interface ResultSet {

	/**
	 * Returns a set of all available facets
	 *
	 * @return FacetSet
	 */
	public function getFacets();

	/**
	 *
	 * @param number $offset        	
	 * @param number $limit        	
	 * @return Document[]
	 */
	public function getResults($offset = 0, $limit = null);

	/**
	 * Returns an excerpt for the given document
	 *
	 * @param Document $document        	
	 */
	public function getExcerpt(Document $document);
	
	
	/**
	 * @return Correction[]
	 */
	public function getCorrections();
}