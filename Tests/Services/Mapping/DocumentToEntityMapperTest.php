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
namespace StingerSoft\EntitySearchBundle\Tests\Services;

use Doctrine\Common\Persistence\AbstractManagerRegistry;
use StingerSoft\EntitySearchBundle\Services\Mapping\DocumentToEntityMapper;
use StingerSoft\EntitySearchBundle\Tests\AbstractORMTestCase;
use StingerSoft\EntitySearchBundle\Tests\Fixtures\ORM\Beer;
use StingerSoft\EntitySearchBundle\Tests\Fixtures\ORM\Potato;
use StingerSoft\EntitySearchBundle\Tests\Fixtures\ORM\Whiskey;
use StingerSoft\EntitySearchBundle\Model\Document;

class DocumentToEntityMapperTest extends AbstractORMTestCase {

	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see PHPUnit_Framework_TestCase::setUp()
	 */
	public function setUp() {
		parent::setUp();
		$this->getMockSqliteEntityManager();
	}

	/**
	 *
	 * @return \StingerSoft\EntitySearchBundle\Services\Mapping\DocumentToEntityMapper
	 */
	protected function getDocumentToEntityMapper() {
		$managerMock = $this->getMockBuilder(AbstractManagerRegistry::class)->setMethods(array(
			'getManagerForClass' 
		))->disableOriginalConstructor()->getMockForAbstractClass();
		$managerMock->expects($this->once())->method('getManagerForClass')->will($this->returnValue($this->em));
		$eh = new DocumentToEntityMapper($managerMock);
		return $eh;
	}

	public function testGetEntity() {
		$beer = new Beer();
		$beer->setTitle('Hemelinger');
		$this->em->persist($beer);
		$this->em->flush();
		
		$document = $this->getMockBuilder(Document::class)->setMethods(array(
			'getEntityClass',
			'getEntityId',
		))->getMockForAbstractClass();
		$document->expects($this->once())->method('getEntityClass')->will($this->returnValue(Beer::class));
		$document->expects($this->once())->method('getEntityId')->will($this->returnValue($beer->getId()));
		
		$eh = $this->getDocumentToEntityMapper();
		
		$entity = $eh->getEntity($document);
		$this->assertEquals($beer, $entity);
		
	}

	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \StingerSoft\EntitySearchBundle\Tests\AbstractTestCase::getUsedEntityFixtures()
	 */
	protected function getUsedEntityFixtures() {
		return array(
			Beer::class
		);
	}
}