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

namespace StingerSoft\EntitySearchBundle\Tests\Command;

use PHPUnit\Framework\MockObject\MockObject;
use StingerSoft\EntitySearchBundle\Command\SyncCommand;
use StingerSoft\EntitySearchBundle\Services\Mapping\EntityToDocumentMapper;
use StingerSoft\EntitySearchBundle\Tests\AbstractORMTestCase;
use StingerSoft\EntitySearchBundle\Tests\Fixtures\ORM\Beer;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\HttpKernel\KernelInterface;

class SyncCommandTest extends AbstractORMTestCase {

	public function getRootDir(): string {
		return '';
	}

	public function getProjectDir(): string {
		return '';
	}

	public function setUp(): void {
		parent::setUp();
		$this->getMockSqliteEntityManager();
	}

	/**
	 *
	 */
	public function testExecuteEmpty(): void {
		$commandTester = $this->createCommander();

		$commandTester->execute(array(
			'command' => 'stinger:search:sync',
			'entity'  => Beer::class
		));

		$output = $commandTester->getDisplay();
		$this->assertContains(Beer::class, $output);
		$this->assertContains('No entities found for indexing', $output);
	}

	/**
	 * @throws \Doctrine\ORM\ORMException
	 * @throws \Doctrine\ORM\OptimisticLockException
	 */
	public function testExecuteNonEmpty(): void {
		$beer = new Beer();
		$beer->setTitle('Grebhan\'s');
		$this->em->persist($beer);
		$this->em->flush();

		$commandTester = $this->createCommander();

		$commandTester->execute(array(
			'command' => 'stinger:search:sync',
			'entity'  => Beer::class
		));

		$output = $commandTester->getDisplay();
		$this->assertContains(Beer::class, $output);
		$this->assertContains('Indexed 1 entities', $output);
	}

	/**
	 * @return CommandTester
	 */
	protected function createCommander(): CommandTester {
		/**
		 * @var $kernel KernelInterface|MockObject
		 */
		/** @noinspection ClassMockingCorrectnessInspection */
		$kernel = $this->getMockBuilder(Kernel::class)->setMethods(array(
			'getRootDir',
			'getProjectDir'
		))->disableOriginalConstructor()->getMockForAbstractClass();

		$kernel->method('getRootDir')->willReturn($this->getRootDir());
		$kernel->method('getProjectDir')->willReturn($this->getProjectDir());

		$searchService = $this->getDummySearchService($this->em);

		$syncCommand = new SyncCommand($searchService, new EntityToDocumentMapper($searchService), $kernel);

		$application = new Application();
		$application->add($syncCommand);

		$command = $application->find('stinger:search:sync');
		return new CommandTester($command);
	}

	/**
	 *
	 * {@inheritdoc}
	 *
	 * @see \StingerSoft\EntitySearchBundle\Tests\AbstractTestCase::getUsedEntityFixtures()
	 */
	protected function getUsedEntityFixtures(): array {
		return array(
			Beer::class
		);
	}
}

