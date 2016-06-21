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
namespace StingerSoft\EntitySearchBundle\Tests\Fixtures\ORM;

use Doctrine\ORM\Mapping as ORM;
use StingerSoft\EntitySearchBundle\Model\SearchableEntity;
use StingerSoft\EntitySearchBundle\Model\Document;

/**
 * @ORM\Entity
 */
class Beer implements SearchableEntity {
	
	
	public static $index = true;

	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	private $id;

	/**
	 * @ORM\Column(name="title", type="string", length=128)
	 */
	private $title;

	public function getId() {
		return $this->id;
	}

	public function getTitle() {
		return $this->title;
	}

	public function setTitle($title) {
		$this->title = $title;
		return $this;
	}

	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \StingerSoft\EntitySearchBundle\Model\SearchableEntity::indexEntity()
	 */
	public function indexEntity(Document &$document) {
		$document->addField(Document::FIELD_TITLE, $this->getTitle());
		return self::$index;
	}
}