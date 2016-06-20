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
	public function setUp() {
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
		$this->assertAttributeCount($this->indexCount, 'index', $service);
		$beer->indexEntity($document);
		$service->saveDocument($document);
		$this->assertAttributeCount(++$this->indexCount, 'index', $service);
		return array(
			$beer,
			$document 
		);
	}

	public function testSaveDocument() {
		$service = $this->getSearchService();
		$this->indexBeer($service);
		$service->clearIndex();
		$this->assertAttributeCount(0, 'index', $service);
	}

	public function testSaveDocumentComposite() {
		$car = new Car('S500', 2016);
		$this->em->persist($car);
		$this->em->flush();
		
		$service = $this->getSearchService();
		$document = $service->createEmptyDocumentFromEntity($car);
		$this->assertAttributeCount(0, 'index', $service);
		$service->saveDocument($document);
		
		$this->assertAttributeCount(1, 'index', $service);
		
		$service->clearIndex();
		$this->assertAttributeCount(0, 'index', $service);
	}

	public function testRemoveDocument() {
		$service = $this->getSearchService();
		$result = $this->indexBeer($service);
		
		$service->removeDocument($result[1]);
		$this->assertAttributeCount(0, 'index', $service);
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
		))->getMock();
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
	protected function getUsedEntityFixtures() {
		return array(
			Beer::class,
			Car::class 
		);
	}
}