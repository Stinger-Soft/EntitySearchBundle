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

use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\Event\PostFlushEventArgs;
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

	/**
	 * @var bool
	 */
	protected $needsFlush = false;

	/**
	 * @var bool
	 */
	protected $enableIndexing;

	/**
	 * DoctrineListener constructor.
	 * @param EntityToDocumentMapperInterface $entityToDocumentMapper
	 * @param SearchService $searchService
	 * @param bool $enableIndexing
	 */
	public function __construct(EntityToDocumentMapperInterface $entityToDocumentMapper, SearchService $searchService, $enableIndexing = false) {
		$this->entityToDocumentMapper = $entityToDocumentMapper;
		$this->searchService = $searchService;
		$this->enableIndexing = $enableIndexing;
	}

	/**
	 * @return array|string[]
	 */
	public function getSubscribedEvents(): array {
		return array(
			'postPersist',
			'postUpdate',
			'preRemove',
			'postFlush'
		);
	}

	/**
	 *
	 */
	public function enableIndexing(): void {
		$this->enableIndexing = true;
	}

	/**
	 *
	 */
	public function disableIndexing(): void {
		$this->enableIndexing = false;
	}

	/**
	 * @return bool
	 */
	public function isIndexingEnabled(): bool {
		return $this->enableIndexing;
	}

	/**
	 * Index the entity after it is persisted for the first time
	 *
	 * @param LifecycleEventArgs $args
	 */
	public function postPersist(LifecycleEventArgs $args): void {
		if(!$this->enableIndexing) {
			return;
		}
		$this->updateEntity($args->getObject(), $args->getObjectManager());
	}

	/**
	 * @param PostFlushEventArgs $eventArgs
	 * @throws \Doctrine\ORM\ORMException
	 * @throws \Doctrine\ORM\OptimisticLockException
	 */
	public function postFlush(PostFlushEventArgs $eventArgs): void {
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
	public function preRemove(LifecycleEventArgs $args): void {
		if(!$this->enableIndexing) {
			return;
		}
		$this->removeEntity($args->getObject(), $args->getObjectManager());
	}

	/**
	 * Updates the entity in the index after it is updated
	 *
	 * @param LifecycleEventArgs $args
	 */
	public function postUpdate(LifecycleEventArgs $args): void {
		if(!$this->enableIndexing) return;
		$this->updateEntity($args->getObject(), $args->getObjectManager());
	}

	/**
	 *
	 * @return EntityToDocumentMapperInterface
	 */
	protected function getEntityToDocumentMapper(): EntityToDocumentMapperInterface {
		return $this->entityToDocumentMapper;
	}

	/**
	 *
	 * @return SearchService
	 */
	protected function getSearchService(): SearchService {
		return $this->searchService;
	}

	/**
	 *
	 * @param object $entity
	 */
	protected function updateEntity(object $entity, ObjectManager $manager): void {
		$document = $this->getEntityToDocumentMapper()->createDocument($manager, $entity);
		if($document !== null) {
			$this->getSearchService()->saveDocument($document);
		}
	}

	/**
	 *
	 * @param object $entity
	 */
	protected function removeEntity(object $entity, ObjectManager $manager): void {
		$document = $this->getEntityToDocumentMapper()->createDocument($manager, $entity);
		if($document !== null) {
			$this->getSearchService()->removeDocument($document);
		}
	}
}