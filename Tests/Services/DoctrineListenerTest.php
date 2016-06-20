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

use Doctrine\Common\EventManager;
use StingerSoft\EntitySearchBundle\Services\DoctrineListener;
use StingerSoft\EntitySearchBundle\Tests\AbstractORMTestCase;
use StingerSoft\EntitySearchBundle\Tests\Fixtures\ORM\Beer;

/**
 */
class DoctrineListenerTest extends AbstractORMTestCase {

	/**
	 * @param number $save Number of expected saves
	 * @param number $delete Number of expected deletions
	 */
	protected function registerDoctrineListener($save = 0, $delete = 0) {
		$listenerMock = $this->getMockBuilder(DoctrineListener::class)->setMethods(array(
			'updateEntity',
			'removeEntity' 
		))->disableOriginalConstructor()->getMock();
		
		$listenerMock->expects($this->exactly($save))->method('updateEntity')->will($this->returnValue(null));
		$listenerMock->expects($this->exactly($delete))->method('removeEntity')->will($this->returnValue(null));
		
		$evm = new EventManager();
		$evm->addEventSubscriber($listenerMock);
		$this->getMockSqliteEntityManager($evm);
	}

	public function testPersist() {
		$this->registerDoctrineListener(1, 0);
		$beer = new Beer();
		$beer->setTitle('Hemelinger');
		$this->em->persist($beer);
		$this->em->flush();
	}

	public function testUpdate() {
		$this->registerDoctrineListener(2, 0);
		$beer = new Beer();
		$beer->setTitle('Haake Bäck');
		$this->em->persist($beer);
		$this->em->flush();
		
		$beer = new Beer();
		$beer->setTitle('Haake Beck');
		$this->em->persist($beer);
		$this->em->flush();
	}

	public function testDelete() {
		$this->registerDoctrineListener(1, 1);
		$beer = new Beer();
		$beer->setTitle('Haake Beck');
		$this->em->persist($beer);
		$this->em->flush();
		
		$this->em->remove($beer);
		$this->em->flush();
	}

	protected function getUsedEntityFixtures() {
		return array(
			Beer::class 
		);
	}
}