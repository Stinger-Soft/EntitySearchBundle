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

namespace StingerSoft\EntitySearchBundle\Services\Mapping;

use Doctrine\Common\Persistence\ManagerRegistry;
use StingerSoft\EntitySearchBundle\Model\Document;

class DocumentToEntityMapper implements DocumentToEntityMapperInterface {

	/**
	 * @var ManagerRegistry
	 */
	protected $managerRegistry;

	public function __construct(ManagerRegistry $managerRegistry) {
		$this->managerRegistry = $managerRegistry;
	}

	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \StingerSoft\EntitySearchBundle\Services\Mapping\DocumentToEntityMapperInterface::getEntity()
	 */
	public function getEntity(Document $document): ?object {
		$clazz = $document->getEntityClass();

		if($clazz === null) {
			return null;
		}
		if(!$document->getEntityId()) {
			return null;
		}
		$manager = $this->managerRegistry->getManagerForClass($clazz);
		if($manager === null) {
			return null;
		}
		return $manager->getRepository($clazz)->find($document->getEntityId());
	}
}