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

class DummySearchService extends AbstractSearchService {

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
		return array();
	}

	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \StingerSoft\EntitySearchBundle\Services\SearchService::search()
	 */
	public function search(Query $query) {
	}
}