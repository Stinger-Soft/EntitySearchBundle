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

namespace StingerSoft\EntitySearchBundle\Events;


use StingerSoft\EntitySearchBundle\Model\Document;
use Symfony\Contracts\EventDispatcher\Event;

abstract class DocumentEvent extends Event {

	public function __construct(protected Document $document) {
	}

	public function getDocument(): Document {
		return $this->document;
	}
}
