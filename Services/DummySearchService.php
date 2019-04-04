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

use StingerSoft\EntitySearchBundle\Model\Document;
use StingerSoft\EntitySearchBundle\Model\Query;
use StingerSoft\EntitySearchBundle\Model\Result\FacetSet;
use StingerSoft\EntitySearchBundle\Model\Result\FacetSetAdapter;
use StingerSoft\EntitySearchBundle\Model\ResultSet;
use StingerSoft\EntitySearchBundle\Model\ResultSetAdapter;

class DummySearchService extends AbstractSearchService {

	/**
	 *
	 * @var Document[]
	 */
	protected $index = array();

	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \StingerSoft\EntitySearchBundle\Services\SearchService::clearIndex()
	 */
	public function clearIndex(): void {
		$this->index = array();
	}

	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \StingerSoft\EntitySearchBundle\Services\SearchService::saveDocument()
	 */
	public function saveDocument(Document $document): void {
		$this->index[$this->createDocumentId($document)] = $document;
	}

	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \StingerSoft\EntitySearchBundle\Services\SearchService::removeDocument()
	 */
	public function removeDocument(Document $document): void {
		unset($this->index[$this->createDocumentId($document)]);
	}

	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \StingerSoft\EntitySearchBundle\Services\SearchService::autocomplete()
	 */
	public function autocomplete(string $search, int $maxResults = 10): array {
		$words = array();
		foreach($this->index as $doc) {
			foreach($doc->getFields() as $content) {
				if(is_string($content)) {
					$words = array_merge($words, explode(' ', $content));
				}
			}
		}

		return array_filter($words, function($word) use ($search) {
			return stripos($word, $search) === 0;
		});
	}

	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \StingerSoft\EntitySearchBundle\Services\SearchService::search()
	 */
	public function search(Query $query): ?ResultSet {
		$term = $query->getSearchTerm();

		$result = new ResultSetAdapter();
		$facets = new FacetSetAdapter();

		$hits = array();
		foreach($this->index as $key => $doc) {
			foreach($doc->getFields() as $content) {
				if(is_string($content) && stripos($content, $term) !== false) {
					if(!isset($hits[$key])) {
						$hits[$key] = 0;
					}
					$hits[$key] = $hits[$key] + 1;
				}
			}
		}
		arsort($hits);
		$results = array();
		foreach(array_keys($hits) as $docId) {
			$doc = $this->index[$docId];
			$facets->addFacetValue(FacetSet::FACET_ENTITY_TYPE, $doc->getEntityClass());
			$facets->addFacetValue(FacetSet::FACET_AUTHOR, (string)$doc->getFieldValue(Document::FIELD_AUTHOR));
			$results[] = $doc;
		}

		$result->setResults($results);
		$result->setFacets($facets);

		return $result;
	}

	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \StingerSoft\EntitySearchBundle\Services\SearchService::getIndexSize()
	 */
	public function getIndexSize() : int {
		return count($this->index);
	}

	protected function createDocumentId(Document $document): string {
		$id = $document->getEntityClass();
		$id .= '#';
		$entityId = $document->getEntityId();
		if(is_scalar($entityId)) {
			$id .= $entityId;
		} else {
			$id .= md5(serialize($entityId));
		}
		return $id;
	}
}