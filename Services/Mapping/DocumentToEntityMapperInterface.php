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
use StingerSoft\EntitySearchBundle\Model\Document;

/**
 * Service to fetch an entity from a document object
 */
interface DocumentToEntityMapperInterface {
	
	const SERVICE_ID = 'stinger_soft.entity_search.document_to_entity_mapper';

	/**
	 * Tries to create a document from the given object
	 *
	 * @param ObjectManager $manager        	
	 * @param object $object        	
	 * @return boolean|Document Returns false if no document could be created
	 */
	public function getEntity(ObjectManager $manager, Document $document);
}