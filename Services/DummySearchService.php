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
namespace StingerSoft\EntitySearchBundle\Services;

use StingerSoft\EntitySearchBundle\Model\Document;
use StingerSoft\EntitySearchBundle\Model\Query;
use StingerSoft\EntitySearchBundle\Model\ResultSetAdapter;
use StingerSoft\EntitySearchBundle\Model\Result\FacetSetAdapter;
use StingerSoft\EntitySearchBundle\Model\Result\FacetSet;
use StingerSoft\EntitySearchBundle\Model\Result\FacetAdapter;

class DummySearchService extends AbstractSearchService {

	/**
	 *
	 * @var Document[]
	 */
	protected $index = array();

	protected function createDocumentId(Document $document) {
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

	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \StingerSoft\EntitySearchBundle\Services\SearchService::clearIndex()
	 */
	public function clearIndex() {
		$this->index = array();
	}

	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \StingerSoft\EntitySearchBundle\Services\SearchService::saveDocument()
	 */
	public function saveDocument(Document $document) {
		$this->index[$this->createDocumentId($document)] = $document;
	}

	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \StingerSoft\EntitySearchBundle\Services\SearchService::removeDocument()
	 */
	public function removeDocument(Document $document) {
		unset($this->index[$this->createDocumentId($document)]);
	}

	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \StingerSoft\EntitySearchBundle\Services\SearchService::autocomplete()
	 */
	public function autocomplete($search, $maxResults = 10) {
		$words = array();
		foreach($this->index as $doc) {
			foreach($doc->getFields() as $content) {
				if(is_string($content)) {
					$words = array_merge($words, explode(' ', $content));
				}
			}
		}
		
		return array_filter($words, function ($word) use ($search) {
			return stripos($word, $search) === 0;
		});
	}

	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \StingerSoft\EntitySearchBundle\Services\SearchService::search()
	 */
	public function search(Query $query) {
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
		foreach(array_keys($hits) as $docId){
			$doc = $this->index[$docId];
			$facets->addFacetValue(FacetSet::FACET_ENTITY_TYPE, $doc->getEntityClass());
			$facets->addFacetValue(FacetSet::FACET_AUTHOR, $doc->getFieldValue(Document::FIELD_AUTHOR));
			$results[] = $doc;
		}
		
		$result->setResults($results);
		$result->setFacets($facets);
		
		return $result;
	}
}