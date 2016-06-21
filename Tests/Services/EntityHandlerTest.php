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

use StingerSoft\EntitySearchBundle\Model\Document;
use StingerSoft\EntitySearchBundle\Services\DummySearchService;
use StingerSoft\EntitySearchBundle\Services\EntityHandler;
use StingerSoft\EntitySearchBundle\Tests\AbstractORMTestCase;
use StingerSoft\EntitySearchBundle\Tests\DependencyInjection\StingerSoftEntitySearchExtensionTest;
use StingerSoft\EntitySearchBundle\Tests\Fixtures\ORM\Beer;
use StingerSoft\EntitySearchBundle\Tests\Fixtures\ORM\Potato;
use StingerSoft\EntitySearchBundle\Tests\Fixtures\ORM\Whiskey;
use StingerSoft\EntitySearchBundle\Services\AbstractSearchService;

class EntityHandlerTest extends AbstractORMTestCase {

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
	 * @return \StingerSoft\EntitySearchBundle\Services\EntityHandler
	 */
	protected function getEntityHandler() {
		$eh = new EntityHandler(new DummySearchService(), StingerSoftEntitySearchExtensionTest::$mockConfiguration['stinger_soft.entity_search']['types']);
		return $eh;
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testFuckedUpConfiguration() {
		$searchService = $this->getMockBuilder(AbstractSearchService::class)->getMockForAbstractClass();
		new EntityHandler($searchService, array(
			'beer' => array()
		));
	}
	
	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testFuckedUpConfigurationWithoutMapping() {
		$searchService = $this->getMockBuilder(AbstractSearchService::class)->getMockForAbstractClass();
		new EntityHandler($searchService, array(
			'beer' => array('persistance' => array(
				'model' => Beer::class
			))
		));
	}
	
	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testFuckedUpConfigurationWithoutModel() {
		$searchService = $this->getMockBuilder(AbstractSearchService::class)->getMockForAbstractClass();
		new EntityHandler($searchService, array(
			'beer' => array('persistance' => array(
			))
		));
	}
	
	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testFuckedUpConfigurationWithoutPersistance() {
		$searchService = $this->getMockBuilder(AbstractSearchService::class)->getMockForAbstractClass();
		new EntityHandler($searchService, array(
			'beer' => array('mapping' => array(
				'title' => array(
					'propertyPath' => false
				)
			))
		));
	}

	public function testIsIndexable() {
		$eh = $this->getEntityHandler();
		$this->assertTrue($eh->isIndexable(new Beer()));
		$this->assertFalse($eh->isIndexable(new Potato()));
		$this->assertTrue($eh->isIndexable(new Whiskey()));
	}

	public function testCreateDocument() {
		$eh = $this->getEntityHandler();
		
		$beer = new Beer();
		$beer->setTitle('Hemelinger');
		$this->em->persist($beer);
		$this->em->flush();
		$document = $eh->createDocument($this->em, $beer);
		$this->assertInstanceOf(Document::class, $document);
		$this->assertEquals('Hemelinger', $document->getFieldValue(Document::FIELD_TITLE));
		
		$whiskey = new Whiskey();
		$whiskey->setTitle('Laphroaig');
		$this->em->persist($whiskey);
		$this->em->flush();
		$document = $eh->createDocument($this->em, $whiskey);
		$this->assertInstanceOf(Document::class, $document);
		$this->assertEquals('Laphroaig', $document->getFieldValue(Document::FIELD_TITLE));
		
		$potato = new Potato();
		$potato->setTitle('Erna');
		$this->em->persist($potato);
		$this->em->flush();
		$document = $eh->createDocument($this->em, $potato);
		$this->assertFalse($document);
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
			Potato::class,
			Whiskey::class 
		);
	}
}