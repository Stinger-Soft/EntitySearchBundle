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

/**
 * POPO containing suggestions to redefine the query (like "Did you mean?" from google)
 */
class Correction {

	/**
	 * The corrected query
	 *
	 * @var string|null
	 */
	protected ?string $query = null;

	/**
	 * The amount of hit for the corrected query.
	 * <code>NULL</code> if the search service cannot predict it
	 *
	 * @var integer|null
	 */
	protected ?int $hits = null;

	/**
	 * Gets the corrected query
	 *
	 * @return string|null
	 */
	public function getQuery(): ?string {
		return $this->query;
	}

	/**
	 * Sets the corrected query
	 *
	 * @param string $query
	 * @return Correction
	 */
	public function setQuery(string $query): Correction {
		$this->query = $query;
		return $this;
	}

	/**
	 * Gets the amount of hit for the corrected query.
	 * <code>NULL</code> if the search service cannot predict it
	 *
	 * @return int
	 */
	public function getHits(): ?int {
		return $this->hits;
	}

	/**
	 * Sets the amount of hit for the corrected query.
	 * <code>NULL</code> if the search service cannot predict it
	 *
	 * @param integer $hits
	 * @return Correction
	 */
	public function setHits(?int $hits): Correction {
		$this->hits = $hits;
		return $this;
	}
}

