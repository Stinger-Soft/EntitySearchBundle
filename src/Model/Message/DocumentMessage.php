<?php

namespace StingerSoft\EntitySearchBundle\Model\Message;

use StingerSoft\EntitySearchBundle\Model\Document;

abstract class DocumentMessage {

	protected string $type;

	public function __construct(protected readonly Document $document) {
		$this->type = self::class;
	}

	public function getDocument(): Document {
		return $this->document;
	}
}