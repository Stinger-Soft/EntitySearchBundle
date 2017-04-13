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

/**
 * POPO containing suggestions to redefine the query (like "Did you mean?" from google)
 */
class Correction {

	/**
	 * The corrected query
	 *
	 * @var string
	 */
	protected $query;

	/**
	 * The amount of hit for the corrected query.
	 * <code>NULL</code> if the search service cannot predict it
	 *
	 * @var integer
	 */
	protected $hits;

	/**
	 * Gets the corrected query
	 *
	 * @return string
	 */
	public function getQuery() {
		return $this->query;
	}

	/**
	 * Sets the corrected query
	 *
	 * @param string $query        	
	 * @return \Model\Result\Correction
	 */
	public function setQuery($query) {
		$this->query = $query;
		return $this;
	}

	/**
	 * Gets the amount of hit for the corrected query.
	 * <code>NULL</code> if the search service cannot predict it
	 *
	 * @return number
	 */
	public function getHits() {
		return $this->hits;
	}

	/**
	 * Sets the amount of hit for the corrected query.
	 * <code>NULL</code> if the search service cannot predict it
	 *
	 * @param integer $hits        	
	 * @return \Model\Result\Correction
	 */
	public function setHits($hits) {
		$this->hits = $hits;
		return $this;
	}
}

