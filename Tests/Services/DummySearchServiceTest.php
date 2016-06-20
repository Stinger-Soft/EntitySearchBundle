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

use StingerSoft\EntitySearchBundle\Tests\AbstractORMTestCase;
use StingerSoft\EntitySearchBundle\Services\DummySearchService;
use StingerSoft\EntitySearchBundle\Tests\Fixtures\ORM\Beer;
use StingerSoft\EntitySearchBundle\Tests\Fixtures\ORM\Potato;

class DummySearchServiceTest extends AbstractORMTestCase {

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
	 * @return \StingerSoft\EntitySearchBundle\Services\DummySearchService
	 */
	protected function getSearchService() {
		$service = new DummySearchService();
		$service->setObjectManager($this->em);
		return $service;
	}

	public function testSaveDocument() {
		$beer = new Beer();
		$beer->setTitle('Hemelinger');
		$this->em->persist($beer);
		$this->em->flush();
		
		$service = $this->getSearchService();
		$document = $service->createEmptyDocumentFromEntity($beer);
		$this->assertAttributeCount(0, 'index', $service);
		$service->saveDocument($document);
		$this->assertAttributeCount(1, 'index', $service);
		
		$service->clearIndex();
		$this->assertAttributeCount(0, 'index', $service);
	}

	public function testRemoveDocument() {
		$beer = new Beer();
		$beer->setTitle('Hemelinger');
		$this->em->persist($beer);
		$this->em->flush();
		
		$service = $this->getSearchService();
		$document = $service->createEmptyDocumentFromEntity($beer);
		$this->assertAttributeCount(0, 'index', $service);
		$service->saveDocument($document);
		$this->assertAttributeCount(1, 'index', $service);
		
		$service->removeDocument($document);
		$this->assertAttributeCount(0, 'index', $service);
	}

	public function testAutocompletion() {
		$beer = new Beer();
		$beer->setTitle('Hemelinger');
		$this->em->persist($beer);
		$this->em->flush();
		
		$service = $this->getSearchService();
		$document = $service->createEmptyDocumentFromEntity($beer);
		$this->assertAttributeCount(0, 'index', $service);
		$beer->indexEntity($document);
		$service->saveDocument($document);
		$this->assertAttributeCount(1, 'index', $service);
		
		$suggests = $service->autocomplete('He');
		$this->count(1, $suggests);
		$this->assertContains($beer->getTitle(), $suggests);
		
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
			Potato::class 
		);
	}
}