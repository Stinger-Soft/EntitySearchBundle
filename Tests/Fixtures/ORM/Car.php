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
namespace StingerSoft\EntitySearchBundle\Tests\Fixtures\ORM;

use Doctrine\ORM\Mapping as ORM;
use StingerSoft\EntitySearchBundle\Model\SearchableEntity;
use StingerSoft\EntitySearchBundle\Model\Document;

/**
 * @ORM\Entity
 */
class Car implements SearchableEntity {

	/**
	 * @ORM\Id @ORM\Column(type="string")
	 */
	private $title;

	/**
	 * @ORM\Id @ORM\Column(type="integer")
	 */
	private $year;

	public function __construct($title, $year) {
		$this->title = $title;
		$this->year = $year;
	}

	public function getTitle() {
		return $this->title;
	}

	public function getYearOfProduction() {
		return $this->year;
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