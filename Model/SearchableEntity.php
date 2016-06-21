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

/**
 * Interface to make an entity appear in the search index
 */
interface SearchableEntity {

	/**
	 * This method is triggered after each persist or update event to allow the entity to decide which
	 * information should be saved to the search index
	 *
	 * @param Document $document
	 *        	The document object to be filled with information
	 * @return bool If this entity returns <code>false</code> no information will be indexed
	 */
	public function indexEntity(Document &$document);
}