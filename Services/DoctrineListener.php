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

use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\EventSubscriber;
use StingerSoft\EntitySearchBundle\Services\Mapping\EntityToDocumentMapperInterface;
use Doctrine\ORM\Event\PostFlushEventArgs;

class DoctrineListener implements EventSubscriber {

	/**
	 *
	 * @var EntityToDocumentMapperInterface
	 */
	protected $entityToDocumentMapper;

	/**
	 *
	 * @var SearchService
	 */
	protected $searchService;

	protected $needsFlush = false;
	
	protected $enableIndexing;

	public function getSubscribedEvents() {
		return array(
			'postPersist',
			'postUpdate',
			'preRemove',
			'postFlush' 
		);
	}

	/**
	 * Constructor
	 *
	 * @param SearchService $searchService        	
	 */
	public function __construct(EntityToDocumentMapperInterface $entityToDocumentMapper, SearchService $searchService, $enableIndexing = true) {
		$this->entityToDocumentMapper = $entityToDocumentMapper;
		$this->searchService = $searchService;
		$this->enableIndexing = $enableIndexing;
	}
	
	public function enableIndexing() {
		$this->enableIndexing = true;
	}
	
	public function disableIndexing() {
		$this->enableIndexing = false;
	}
	
	public function isIndexingEnabled() {
		return $this->enableIndexing;
	}

	/**
	 * Index the entity after it is persisted for the first time
	 *
	 * @param LifecycleEventArgs $args        	
	 */
	public function postPersist(LifecycleEventArgs $args) {
		if(!$this->enableIndexing) return;
		$this->updateEntity($args->getObject(), $args->getObjectManager());
	}

	public function postFlush(PostFlushEventArgs $eventArgs) {
		if($this->needsFlush) {
			$this->needsFlush = false;
			$eventArgs->getEntityManager()->flush();
		}
	}

	/**
	 * Removes the entity from the index when it marked for deletion
	 *
	 * @param LifecycleEventArgs $args        	
	 */
	public function preRemove(LifecycleEventArgs $args) {
		if(!$this->enableIndexing) return;
		$this->removeEntity($args->getObject(), $args->getObjectManager());
	}

	/**
	 * Updates the entity in the index after it is updated
	 *
	 * @param LifecycleEventArgs $args        	
	 */
	public function postUpdate(LifecycleEventArgs $args) {
		if(!$this->enableIndexing) return;
		$this->updateEntity($args->getObject(), $args->getObjectManager());
	}

	/**
	 *
	 * @return EntityToDocumentMapperInterface
	 */
	protected function getEntityToDocumentMapper() {
		return $this->entityToDocumentMapper;
	}

	/**
	 *
	 * @return SearchService
	 */
	protected function getSearchService(ObjectManager $manager) {
		$this->searchService->setObjectManager($manager);
		return $this->searchService;
	}

	/**
	 *
	 * @param object $entity        	
	 */
	protected function updateEntity($entity, ObjectManager $manager) {
		$document = $this->getEntityToDocumentMapper()->createDocument($manager, $entity);
		if($document !== false) {
			$this->getSearchService($manager)->saveDocument($document);
		}
	}

	/**
	 *
	 * @param object $entity        	
	 */
	protected function removeEntity($entity, ObjectManager $manager) {
		$document = $this->getEntityToDocumentMapper()->createDocument($manager, $entity);
		if($document !== false) {
			$this->getSearchService($manager)->removeDocument($document);
		}
	}
}