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

use StingerSoft\EntitySearchBundle\Services\DummySearchService;
use StingerSoft\EntitySearchBundle\Tests\AbstractORMTestCase;
use StingerSoft\EntitySearchBundle\Tests\Fixtures\ORM\Beer;
use StingerSoft\EntitySearchBundle\Tests\Fixtures\ORM\Car;
use StingerSoft\EntitySearchBundle\Services\SearchService;
use StingerSoft\EntitySearchBundle\Model\Query;

class DummySearchServiceTest extends AbstractORMTestCase {

	protected $indexCount = 0;
	
	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see PHPUnit_Framework_TestCase::setUp()
	 */
	public function setUp(): void {
		parent::setUp();
		$this->getMockSqliteEntityManager();
		$this->indexCount = 0;
	}

	/**
	 *
	 * @return \StingerSoft\EntitySearchBundle\Services\DummySearchService
	 */
	protected function getSearchService() {
		$service = new DummySearchService();
		$service->setObjectManager($this->em);
		return $service;
	}

	protected function indexBeer(SearchService $service, $title = 'Hemelinger') {
		$beer = new Beer();
		$beer->setTitle($title);
		$this->em->persist($beer);
		$this->em->flush();
		
		$document = $service->createEmptyDocumentFromEntity($beer);
		$this->assertEquals($this->indexCount, $service->getIndexSize());
		$beer->indexEntity($document);
		$service->saveDocument($document);
		$this->assertEquals(++$this->indexCount, $service->getIndexSize());
		return array(
			$beer,
			$document 
		);
	}

	public function testSaveDocument() {
		$service = $this->getSearchService();
		$this->indexBeer($service);
		$service->clearIndex();
		$this->assertEquals(0, $service->getIndexSize());
	}

	public function testSaveDocumentComposite() {
		$car = new Car('S500', 2016);
		$this->em->persist($car);
		$this->em->flush();
		
		$service = $this->getSearchService();
		$document = $service->createEmptyDocumentFromEntity($car);
		$this->assertEquals(0, $service->getIndexSize());
		$service->saveDocument($document);
		
		$this->assertEquals(1, $service->getIndexSize());
		
		$service->clearIndex();
		$this->assertEquals(0, $service->getIndexSize());
	}

	public function testRemoveDocument() {
		$service = $this->getSearchService();
		$result = $this->indexBeer($service);
		
		$service->removeDocument($result[1]);
		$this->assertEquals(0, $service->getIndexSize());
	}

	public function testAutocompletion() {
		$service = $this->getSearchService();
		$result = $this->indexBeer($service);
		
		$suggests = $service->autocomplete('He');
		$this->assertCount(1, $suggests);
		$this->assertContains($result[0]->getTitle(), $suggests);
	}

	public function testSearch() {
		$service = $this->getSearchService();
		$this->indexBeer($service);
		$this->indexBeer($service, 'Haake Beck');
		$this->indexBeer($service, 'Haake Beck Kräusen');
		$query = $this->getMockBuilder(Query::class)->setMethods(array(
			'getSearchTerm' 
		))->disableOriginalConstructor()->getMock();
		$query->expects($this->once())->method('getSearchTerm')->will($this->returnValue('Beck'));
		$result = $service->search($query);
		$this->assertCount(2, $result->getResults());
	}

	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \StingerSoft\EntitySearchBundle\Tests\AbstractTestCase::getUsedEntityFixtures()
	 */
	protected function getUsedEntityFixtures(): array {
		return array(
			Beer::class,
			Car::class 
		);
	}
}
