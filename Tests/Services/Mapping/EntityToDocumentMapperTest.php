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

use StingerSoft\EntitySearchBundle\Model\Document;
use StingerSoft\EntitySearchBundle\Services\AbstractSearchService;
use StingerSoft\EntitySearchBundle\Services\DummySearchService;
use StingerSoft\EntitySearchBundle\Services\Mapping\EntityToDocumentMapper;
use StingerSoft\EntitySearchBundle\Tests\AbstractORMTestCase;
use StingerSoft\EntitySearchBundle\Tests\DependencyInjection\StingerSoftEntitySearchExtensionTest;
use StingerSoft\EntitySearchBundle\Tests\Fixtures\ORM\Beer;
use StingerSoft\EntitySearchBundle\Tests\Fixtures\ORM\Potato;
use StingerSoft\EntitySearchBundle\Tests\Fixtures\ORM\Whiskey;

class EntityToDocumentMapperTest extends AbstractORMTestCase {

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
	 * @return \StingerSoft\EntitySearchBundle\Services\EntityToDocumentMapper
	 */
	protected function getEntityToDocumentMapper() {
		$eh = new EntityToDocumentMapper(new DummySearchService(), StingerSoftEntitySearchExtensionTest::$mockConfiguration['stinger_soft.entity_search']['types']);
		return $eh;
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testFuckedUpConfiguration() {
		$searchService = $this->getMockBuilder(AbstractSearchService::class)->getMockForAbstractClass();
		new EntityToDocumentMapper($searchService, array(
			'beer' => array() 
		));
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testFuckedUpConfigurationWithoutMapping() {
		$searchService = $this->getMockBuilder(AbstractSearchService::class)->getMockForAbstractClass();
		new EntityToDocumentMapper($searchService, array(
			'beer' => array(
				'persistence' => array(
					'model' => Beer::class 
				) 
			) 
		));
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testFuckedUpConfigurationWithoutModel() {
		$searchService = $this->getMockBuilder(AbstractSearchService::class)->getMockForAbstractClass();
		new EntityToDocumentMapper($searchService, array(
			'beer' => array(
				'mappings' => array(
					'title' => array(
						'propertyPath' => false
					)
				),
				'persistence' => array() 
			) 
		));
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testFuckedUpConfigurationWithoutPersistance() {
		$searchService = $this->getMockBuilder(AbstractSearchService::class)->getMockForAbstractClass();
		new EntityToDocumentMapper($searchService, array(
			'beer' => array(
				'mappings' => array(
					'title' => array(
						'propertyPath' => false 
					) 
				) 
			) 
		));
	}

	public function testIsIndexable() {
		$eh = $this->getEntityToDocumentMapper();
		$this->assertTrue($eh->isIndexable(new Beer()));
		$this->assertFalse($eh->isIndexable(new Potato()));
		$this->assertTrue($eh->isIndexable(new Whiskey()));
	}

	public function testCreateDocument() {
		$eh = $this->getEntityToDocumentMapper();
		
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