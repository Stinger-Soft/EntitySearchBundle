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

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Util\ClassUtils;
use StingerSoft\EntitySearchBundle\Model\Document;
use StingerSoft\EntitySearchBundle\Model\DocumentAdapter;

abstract class AbstractSearchService implements SearchService {

	/**
	 *
	 * @var ObjectManager
	 */
	protected $objectManager;

	/**
	 *
	 * {@inheritdoc}
	 *
	 * @see \StingerSoft\EntitySearchBundle\Services\SearchService::setObjectManager()
	 */
	public function setObjectManager(ObjectManager $om) {
		if($this->objectManager)
			return;
		$this->objectManager = $om;
	}

	/**
	 *
	 * {@inheritdoc}
	 *
	 * @see \StingerSoft\EntitySearchBundle\Services\SearchService::getObjectManager()
	 */
	public function getObjectManager() {
		return $this->objectManager;
	}

	/**
	 *
	 * {@inheritdoc}
	 *
	 * @see \StingerSoft\EntitySearchBundle\Services\SearchService::createEmptyDocumentFromEntity()
	 */
	public function createEmptyDocumentFromEntity($entity) {
		$document = $this->newDocumentInstance();
		$clazz = $this->getClass($entity);
		$cmd = $this->getObjectManager()->getClassMetadata($clazz);
		$id = $cmd->getIdentifierValues($entity);
		
		$document->setEntityClass($clazz);
		$document->setEntityId(count($id) == 1 ? current($id) : $id);
		return $document;
	}
	
	protected function getClass($entity) {
		$clazz = ClassUtils::getClass($entity);
		return $clazz;
	}

	/**
	 *
	 * {@inheritdoc}
	 *
	 * @see \StingerSoft\EntitySearchBundle\Services\SearchService::getOnlineHelp()
	 */
	public function getOnlineHelp($locale, $defaultLocale = 'en') {
		return null;
	}

	/**
	 * Creates a new document instance
	 *
	 * @return Document
	 */
	protected function newDocumentInstance() {
		return new DocumentAdapter();
	}
}