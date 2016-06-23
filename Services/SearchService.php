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
namespace StingerSoft\EntitySearchBundle\Services;

use StingerSoft\EntitySearchBundle\Model\Document;
use StingerSoft\EntitySearchBundle\Model\Query;
use StingerSoft\EntitySearchBundle\Model\SearchableEntity;
use Doctrine\Common\Persistence\ObjectManager;
use StingerSoft\EntitySearchBundle\Model\ResultSet;

/**
 * Defines a service to execute basic operations on the underlying search index
 */
interface SearchService {

	const SERVICE_ID = 'stinger_soft.entity_search.search_service';

	/**
	 * Completely wipes the search index
	 */
	public function clearIndex();

	/**
	 * Creates an empty document from the given entity
	 *
	 * @param SearchableEntity $entity        	
	 * @return Document
	 */
	public function createEmptyDocumentFromEntity($entity);

	/**
	 * Saves a document to the index
	 *
	 * @param Document $document
	 *        	The document to index
	 */
	public function saveDocument(Document $document);

	/**
	 * Removes a document to the index
	 *
	 * @param Document $document
	 *        	The document to index
	 */
	public function removeDocument(Document $document);

	/**
	 * Tries to autocomplete the given search string and provides possible completions
	 *
	 * @param string $search
	 *        	The search string
	 * @param integer $maxResults
	 *        	The maximum number of autocompletions to be returned
	 * @return string[] Possible autocompletions
	 */
	public function autocomplete($search, $maxResults = 10);

	/**
	 * Executes the given query
	 *
	 * @param Query $query        	
	 * @return ResultSet
	 */
	public function search(Query $query);

	/**
	 * Adds the object manager to this service
	 *
	 * @internal
	 *
	 * @param ObjectManager $om        	
	 */
	public function setObjectManager(ObjectManager $om);

	/**
	 * Get the doctrine object manager
	 *
	 * @return ObjectManager
	 */
	public function getObjectManager();

	/**
	 * Returns the size (ie stored documents) of the given index
	 *
	 * @return int
	 */
	public function getIndexSize();
}