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
namespace StingerSoft\EntitySearchBundle\Tests\Command;

use StingerSoft\EntitySearchBundle\Command\SyncCommand;
use StingerSoft\EntitySearchBundle\Tests\Fixtures\ORM\Beer;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\DependencyInjection\Container;
use StingerSoft\EntitySearchBundle\Tests\AbstractORMTestCase;
use StingerSoft\EntitySearchBundle\Services\Mapping\EntityToDocumentMapperInterface;
use StingerSoft\EntitySearchBundle\Services\Mapping\EntityToDocumentMapper;
use StingerSoft\EntitySearchBundle\Services\DummySearchService;
use StingerSoft\EntitySearchBundle\Services\SearchService;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\HttpKernel\KernelInterface;

class SyncCommandTest extends AbstractORMTestCase {

	public function getRootDir() {
		return '';
	}
	
	public function getProjectDir() {
		return '';
	}

	public function setUp() {
		parent::setUp();
		$this->getMockSqliteEntityManager();
	}

	/**
	 *
	 * @return CommandTester
	 */
	protected function createCommander() {
		$kernel = $this->getMockBuilder(Kernel::class)->setMethods(array(
			'getRootDir'
		))->disableOriginalConstructor()->getMockForAbstractClass();
		$kernel->method('getRootDir')->willReturn($this->getRootDir());


		$searchService = $this->getDummySearchService($this->em);


		$syncCommand = new SyncCommand($searchService, new EntityToDocumentMapper($searchService), $kernel);

		$application = new Application();
		$application->add($syncCommand);
		
		$command = $application->find('stinger:search:sync');
		$commandTester = new CommandTester($command);
		return $commandTester;
	}

	public function testExecuteEmpty() {
		$commandTester = $this->createCommander();
		
		$commandTester->execute(array(
			'command' => 'stinger:search:sync',
			'entity' => Beer::class 
		));
		
		$output = $commandTester->getDisplay();
		$this->assertContains(Beer::class, $output);
		$this->assertContains('No entities found for indexing', $output);
	}

	public function testExecuteNonEmpty() {
		$beer = new Beer();
		$beer->setTitle('Grebhan\'s');
		$this->em->persist($beer);
		$this->em->flush();
		
		$commandTester = $this->createCommander();
		
		$commandTester->execute(array(
			'command' => 'stinger:search:sync',
			'entity' => Beer::class 
		));
		
		$output = $commandTester->getDisplay();
		$this->assertContains(Beer::class, $output);
		$this->assertContains('Indexed 1 entities', $output);
	}

	/**
	 *
	 * {@inheritdoc}
	 *
	 * @see \StingerSoft\EntitySearchBundle\Tests\AbstractTestCase::getUsedEntityFixtures()
	 */
	protected function getUsedEntityFixtures() {
		return array(
			Beer::class 
		);
	}
}

