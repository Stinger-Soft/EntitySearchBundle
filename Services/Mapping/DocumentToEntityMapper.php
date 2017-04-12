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
namespace StingerSoft\EntitySearchBundle\Services\Mapping;

use StingerSoft\EntitySearchBundle\Model\Document;
use Doctrine\Common\Persistence\AbstractManagerRegistry;

class DocumentToEntityMapper implements DocumentToEntityMapperInterface {

	/**
	 * @var AbstractManagerRegistry
	 */
	protected $managerRegistry;

	public function __construct(AbstractManagerRegistry $managerRegistry) {
		$this->managerRegistry = $managerRegistry;
	}

	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \StingerSoft\EntitySearchBundle\Services\Mapping\DocumentToEntityMapperInterface::getEntity()
	 */
	public function getEntity(Document $document) {
		$clazz = $document->getEntityClass();
		
		if($clazz == null) {
			return null;
		}
		if(!$document->getEntityId()) {
			return null;
		}
		return $this->managerRegistry->getManagerForClass($clazz)->getRepository($clazz)->find($document->getEntityId());
	}
}