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
namespace StingerSoft\EntitySearchBundle\Services\Mapping;

use Doctrine\Common\Persistence\ObjectManager;

/**
 * Service to create a document object from an entity
 */
interface EntityToDocumentMapperInterface {

	/**
	 * Checks if the given object can be added to the index
	 *
	 * @param object $object        	
	 * @return boolean
	 */
	public function isIndexable($object);

	/**
	 * Tries to create a document from the given object
	 *
	 * @param ObjectManager $manager        	
	 * @param object $object        	
	 * @return boolean|Document Returns false if no document could be created
	 */
	public function createDocument(ObjectManager $manager, $object);
}