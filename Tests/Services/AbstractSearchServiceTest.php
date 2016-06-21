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

use StingerSoft\EntitySearchBundle\Tests\AbstractORMTestCase;
use StingerSoft\EntitySearchBundle\Tests\Fixtures\ORM\Beer;
use StingerSoft\EntitySearchBundle\Services\AbstractSearchService;
use Doctrine\Common\Persistence\ObjectManager;
use StingerSoft\EntitySearchBundle\Model\Document;
use StingerSoft\EntitySearchBundle\Tests\Fixtures\ORM\Car;

class AbstractSearchServiceTest extends AbstractORMTestCase {

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

	public function testSetObjectManager() {
		/**
		 *
		 * @var AbstractSearchService $service
		 */
		$service = $this->getMockBuilder(AbstractSearchService::class)->getMockForAbstractClass();
		$this->assertNotNull($this->em);
		$service->setObjectManager($this->em);
		$this->assertNotNull($service->getObjectManager());
		$this->assertInstanceOf(ObjectManager::class, $service->getObjectManager());
	}

	public function testCreateEmptyDocumentFromEntity() {
		$beer = new Beer();
		$beer->setTitle('Hemelinger');
		$this->em->persist($beer);
		$this->em->flush();
		
		/**
		 *
		 * @var AbstractSearchService $service
		 */
		$service = $this->getMockBuilder(AbstractSearchService::class)->getMockForAbstractClass();
		$service->setObjectManager($this->em);
		
		/**
		 *
		 * @var Document $doc
		 */
		$doc = $service->createEmptyDocumentFromEntity($beer);
		$this->assertNotNull($doc);
		$this->assertInstanceOf(Document::class, $doc);
		$this->assertEquals($beer->getId(), $doc->getEntityId());
		$this->assertEquals(Beer::class, $doc->getEntityClass());
	}
	
	public function testCreateEmptyDocumentFromCompositeEntity() {
		$car = new Car('S500', 2016);
		$this->em->persist($car);
		$this->em->flush();
	
		/**
		 *
		 * @var AbstractSearchService $service
		 */
		$service = $this->getMockBuilder(AbstractSearchService::class)->getMockForAbstractClass();
		$service->setObjectManager($this->em);
	
		/**
		 *
		 * @var Document $doc
		 */
		$doc = $service->createEmptyDocumentFromEntity($car);
		$this->assertNotNull($doc);
		$this->assertInstanceOf(Document::class, $doc);
		$this->assertInternalType('array', $doc->getEntityId());
		$this->assertCount(2, $doc->getEntityId());
		$this->assertEquals(Car::class, $doc->getEntityClass());
	}

	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \StingerSoft\EntitySearchBundle\Tests\AbstractTestCase::getUsedEntityFixtures()
	 */
	protected function getUsedEntityFixtures() {
		return array(
			Beer::class,
			Car::class
		);
	}
}