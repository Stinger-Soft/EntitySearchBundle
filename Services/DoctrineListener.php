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

class DoctrineListener {

	/**
	 * Index the entity after it is persisted for the first time
	 *
	 * @param LifecycleEventArgs $args        	
	 */
	public function postPersist(LifecycleEventArgs $args) {
		$this->updateEntity($args->getEntity());
	}

	/**
	 * Removes the entity from the index when it marked for deletion
	 *
	 * @param LifecycleEventArgs $args        	
	 */
	public function preRemove(LifecycleEventArgs $args) {
		$this->removeEntity($args->getEntity());
	}

	/**
	 * Updates the entity in the index after it is updated
	 *
	 * @param LifecycleEventArgs $args        	
	 */
	public function postUpdate(LifecycleEventArgs $args) {
		$this->updateEntity($args->getEntity());
	}

	/**
	 *
	 * @return SearchService
	 */
	protected function getSearchService() {
	}

	/**
	 *
	 * @param object $entity        	
	 */
	protected function updateEntity($entity) {
		if(!($entity instanceof SearchableEntity))
			return;
		$document = $this->getSearchService()->createEmptyDocumentFromEntity($entity);
		$this->getSearchService()->saveDocument($document);
	}

	/**
	 *
	 * @param object $entity        	
	 */
	protected function removeEntity($entity) {
		if(!($entity instanceof SearchableEntity))
			return;
		$document = $this->getSearchService()->createEmptyDocumentFromEntity($entity);
		$this->getSearchService()->removeDocument($document);
	}
}