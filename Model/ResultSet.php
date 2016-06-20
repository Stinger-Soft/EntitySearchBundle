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
namespace StingerSoft\EntitySearchBundle\Model;

use StingerSoft\EntitySearchBundle\Model\Result\FacetSet;

interface ResultSet {

	/**
	 * Returns a set of all available facets
	 *
	 * @return FacetSet
	 */
	public function getFacets();
}