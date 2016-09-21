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

use Doctrine\Common\EventManager;
use StingerSoft\EntitySearchBundle\Services\AbstractSearchService;
use StingerSoft\EntitySearchBundle\Services\DoctrineListener;
use StingerSoft\EntitySearchBundle\Services\Mapping\EntityToDocumentMapper;
use StingerSoft\EntitySearchBundle\Tests\AbstractORMTestCase;
use StingerSoft\EntitySearchBundle\Tests\Fixtures\ORM\Beer;
use StingerSoft\EntitySearchBundle\Tests\Fixtures\ORM\Potato;

/**
 */
class DoctrineListenerTest extends AbstractORMTestCase {

	/**
	 *
	 * @param number $save
	 *        	Number of expected saves
	 * @param number $delete
	 *        	Number of expected deletions
	 */
	protected function registerDoctrineListener($save = 0, $delete = 0) {
		/**
		 * @var PHPUnit_Framework_MockObject_MockObject|DoctrineListener $listenerMock
		 */
		$listenerMock = $this->getMockBuilder(DoctrineListener::class)->setMethods(array(
			'updateEntity',
			'removeEntity' 
		))->disableOriginalConstructor()->getMock();
		$listenerMock->enableIndexing();
		
		$listenerMock->expects($this->exactly($save))->method('updateEntity')->will($this->returnValue(null));
		$listenerMock->expects($this->exactly($delete))->method('removeEntity')->will($this->returnValue(null));
		
		$evm = new EventManager();
		$evm->addEventSubscriber($listenerMock);
		$this->getMockSqliteEntityManager($evm);
	}

	/**
	 *
	 * @param number $save
	 *        	Number of expected saves
	 * @param number $delete
	 *        	Number of expected deletions
	 */
	protected function registerSearchService($save = 0, $delete = 0) {
		$searchMock = $this->getMockBuilder(AbstractSearchService::class)->setMethods(array(
			'saveDocument',
			'removeDocument',
			'clearIndex',
			'autocomplete',
			'search' 
		))->disableOriginalConstructor()->getMockForAbstractClass();
		
		$searchMock->expects($this->exactly($save))->method('saveDocument')->will($this->returnValue(null));
		$searchMock->expects($this->exactly($delete))->method('removeDocument')->will($this->returnValue(null));
		
		$evm = new EventManager();
		$evm->addEventSubscriber(new DoctrineListener(new EntityToDocumentMapper($searchMock, array()), $searchMock));
		$this->getMockSqliteEntityManager($evm);
	}

	public function testMockedPersist() {
		$this->registerDoctrineListener(1, 0);
		$beer = new Beer();
		$beer->setTitle('Hemelinger');
		$this->em->persist($beer);
		$this->em->flush();
	}

	public function testMockedUpdate() {
		$this->registerDoctrineListener(2, 0);
		$beer = new Beer();
		$beer->setTitle('Haake Bäck');
		$this->em->persist($beer);
		$this->em->flush();
		
		$beer->setTitle('Haake Beck');
		$this->em->persist($beer);
		$this->em->flush();
	}

	public function testMockedDelete() {
		$this->registerDoctrineListener(1, 1);
		$beer = new Beer();
		$beer->setTitle('Haake Beck');
		$this->em->persist($beer);
		$this->em->flush();
		
		$this->em->remove($beer);
		$this->em->flush();
	}

	public function testPersist() {
		$this->registerSearchService(1, 0);
		$beer = new Beer();
		$beer->setTitle('Hemelinger');
		$this->em->persist($beer);
		$this->em->flush();
	}

	public function testUpdate() {
		$this->registerSearchService(2, 0);
		$beer = new Beer();
		$beer->setTitle('Haake Bäck');
		$this->em->persist($beer);
		$this->em->flush();
		
		$beer->setTitle('Haake Beck');
		$this->em->persist($beer);
		$this->em->flush();
	}

	public function testReturnFalse() {
		$this->registerSearchService(1, 0);
		$beer = new Beer();
		$beer->setTitle('Haake Bäck');
		$this->em->persist($beer);
		$this->em->flush();
		
		Beer::$index = false;
		$beer->setTitle('Haake Beck');
		$this->em->persist($beer);
		$this->em->flush();
		Beer::$index = true;
	}

	public function testDelete() {
		$this->registerSearchService(1, 1);
		$beer = new Beer();
		$beer->setTitle('Haake Beck');
		$this->em->persist($beer);
		$this->em->flush();
		
		$this->em->remove($beer);
		$this->em->flush();
	}

	public function testMockedNonIndexablePersist() {
		$this->registerSearchService(0, 0);
		$potato = new Potato();
		$potato->setTitle('Lisa');
		$this->em->persist($potato);
		$this->em->flush();
		
		$potato->setTitle('Erna');
		$this->em->persist($potato);
		$this->em->flush();
	}

	public function testMockedNonIndexableUpdate() {
		$this->registerSearchService(0, 0);
		$potato = new Potato();
		$potato->setTitle('Lisa');
		$this->em->persist($potato);
		$this->em->flush();
	}

	public function testMockedNonIndexableDelete() {
		$this->registerSearchService(0, 0);
		$potato = new Potato();
		$potato->setTitle('Lisa');
		$this->em->persist($potato);
		$this->em->flush();
		
		$this->em->remove($potato);
		$this->em->flush();
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