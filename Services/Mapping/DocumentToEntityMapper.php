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

use Doctrine\Common\Persistence\ObjectManager;
use StingerSoft\EntitySearchBundle\Model\Document;

class DocumentToEntityMapper implements DocumentToEntityMapperInterface {

	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \StingerSoft\EntitySearchBundle\Services\Mapping\DocumentToEntityMapperInterface::getEntity()
	 */
	public function getEntity(ObjectManager $manager, Document $document) {
		return $manager->getRepository($document->getEntityClass())->find($document->getEntityId());
	}
}