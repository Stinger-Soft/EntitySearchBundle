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

use StingerSoft\EntitySearchBundle\Model\Document;

/**
 * Service to fetch an entity from a document object
 */
interface DocumentToEntityMapperInterface {

	/**
	 * Tries to create a document from the given object
	 *
	 * @param object $object
	 * @return object Returns false if no document could be created
	 */
	public function getEntity(Document $document): ?object;

}