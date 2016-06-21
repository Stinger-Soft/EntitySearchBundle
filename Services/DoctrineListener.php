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

	public function getSubscribedEvents() {
		return array(
			'postPersist',
			'postUpdate',
			'preRemove' 
		);
	}

	/**
	 * Constructor
	 *
	 * @param SearchService $searchService        	
	 */
	public function __construct(EntityToDocumentMapperInterface $entityToDocumentMapper, SearchService $searchService) {
		$this->entityToDocumentMapper = $entityToDocumentMapper;
		$this->searchService = $searchService;
	}

	/**
	 * Index the entity after it is persisted for the first time
	 *
	 * @param LifecycleEventArgs $args        	
	 */
	public function postPersist(LifecycleEventArgs $args) {
		$this->updateEntity($args->getObject(), $args->getObjectManager());
	}

	/**
	 * Removes the entity from the index when it marked for deletion
	 *
	 * @param LifecycleEventArgs $args        	
	 */
	public function preRemove(LifecycleEventArgs $args) {
		$this->removeEntity($args->getObject(), $args->getObjectManager());
	}

	/**
	 * Updates the entity in the index after it is updated
	 *
	 * @param LifecycleEventArgs $args        	
	 */
	public function postUpdate(LifecycleEventArgs $args) {
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