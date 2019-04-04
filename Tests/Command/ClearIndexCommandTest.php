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
use PHPUnit\Framework\TestCase;
use StingerSoft\EntitySearchBundle\Command\ClearIndexCommand;
use StingerSoft\EntitySearchBundle\Services\SearchService;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class ClearIndexCommandTest extends TestCase {

	public function testExecute(): void {
		/**
		 * @var $searchService SearchService|MockObject
		 */
		$searchService = $this->getMockBuilder(SearchService::class)->setMethods(array(
			'clearIndex',
		))->disableOriginalConstructor()->getMockForAbstractClass();

		$searchService->expects($this->once())->method('clearIndex');

		$clearCommand = new ClearIndexCommand($searchService);

		$application = new Application();
		$application->add($clearCommand);

		$command = $application->find('stinger:search:clear');

		$commandTester = new CommandTester($command);

		$commandTester->execute(array(
			'command' => 'stinger:search:clear',
		));
	}
}