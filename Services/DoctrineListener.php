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

use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use StingerSoft\EntitySearchBundle\Model\SearchableEntity;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\EventSubscriber;

class DoctrineListener implements EventSubscriber {

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
	public function __construct(SearchService $searchService) {
		$this->searchService = $searchService;
	}

	/**
	 * Index the entity after it is persisted for the first time
	 *
	 * @param LifecycleEventArgs $args        	
	 */
	public function postPersist(LifecycleEventArgs $args) {
		$this->updateEntity($args->getEntity(), $args->getObjectManager());
	}

	/**
	 * Removes the entity from the index when it marked for deletion
	 *
	 * @param LifecycleEventArgs $args        	
	 */
	public function preRemove(LifecycleEventArgs $args) {
		$this->removeEntity($args->getEntity(), $args->getObjectManager());
	}

	/**
	 * Updates the entity in the index after it is updated
	 *
	 * @param LifecycleEventArgs $args        	
	 */
	public function postUpdate(LifecycleEventArgs $args) {
		$this->updateEntity($args->getEntity(), $args->getObjectManager());
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
		if(!($entity instanceof SearchableEntity))
			return;
		$document = $this->getSearchService($manager)->createEmptyDocumentFromEntity($entity);
		if($entity->indexEntity($document) !== false) {
			$this->getSearchService($manager)->saveDocument($document);
		} else {
			$this->getSearchService($manager)->removeDocument($document);
		}
	}

	/**
	 *
	 * @param object $entity        	
	 */
	protected function removeEntity($entity, ObjectManager $manager) {
		if(!($entity instanceof SearchableEntity))
			return;
		$document = $this->getSearchService($manager)->createEmptyDocumentFromEntity($entity);
		$this->getSearchService($manager)->removeDocument($document);
	}
}