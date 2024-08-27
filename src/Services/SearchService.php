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

namespace StingerSoft\EntitySearchBundle\Services;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;
use StingerSoft\EntitySearchBundle\Model\Document;
use StingerSoft\EntitySearchBundle\Model\Query;
use StingerSoft\EntitySearchBundle\Model\ResultSet;
use StingerSoft\EntitySearchBundle\Model\SearchableEntity;
use StingerSoft\EntitySearchBundle\Services\Facet\FacetServiceInterface;

/**
 * Defines a service to execute basic operations on the underlying search index
 */
interface SearchService {

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
	public function createEmptyDocumentFromEntity(object $entity): Document;

	/**
	 * Saves a document to the index
	 *
	 * @param Document $document
	 *            The document to index
	 */
	public function saveDocument(Document $document): void;

	/**
	 * Removes a document to the index
	 *
	 * @param Document $document
	 *            The document to index
	 */
	public function removeDocument(Document $document): void;

	/**
	 * Tries to autocomplete the given search string and provides possible completions
	 *
	 * @param string $search
	 *            The search string
	 * @param integer $maxResults
	 *            The maximum number of autocompletions to be returned
	 * @return string[] Possible autocompletions
	 */
	public function autocomplete(string $search, int $maxResults = 10): array;

	/**
	 * Executes the given query
	 *
	 * @param Query $query
	 * @return ResultSet
	 */
	public function search(Query $query): ?ResultSet;

	/**
	 * Adds the object manager to this service
	 *
	 * @internal
	 *
	 * @param EntityManagerInterface $om
	 */
	public function setObjectManager(EntityManagerInterface $om) : void;

	/**
	 * Get the doctrine object manager
	 *
	 * @return EntityManagerInterface|null
	 */
	public function getObjectManager(): ?EntityManagerInterface;

	/**
	 * Sets the facet container. This method is automatically called by the EntitySearchBundle
	 *
	 * @param ContainerInterface $facetContainer
	 * @return void
	 */
	public function setFacetContainer(ContainerInterface $facetContainer): void;

	/**
	 * Get a facet service by id
	 * @return FacetServiceInterface
	 */
	public function getFacet(string $facetId): FacetServiceInterface;

	/**
	 * Returns the size (ie stored documents) of the given index
	 *
	 * @return int
	 */
	public function getIndexSize(): int;

	/**
	 * Returns the path to a template including the online help for this search service
	 *
	 * @return string template path
	 */
	public function getOnlineHelp(string $locale, string $defaultLocale = 'en'): ?string;
}
