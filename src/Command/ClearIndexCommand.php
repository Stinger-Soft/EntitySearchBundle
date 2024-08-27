<?php
declare(strict_types=1);

/*
 * This file is part of the Stinger Entity Search package.
 *
 * (c) Oliver Kotte <oliver.kotte@stinger-soft.net>
 * (c) Florian Meyer <florian.meyer@stinger-soft.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace StingerSoft\EntitySearchBundle\Command;

use StingerSoft\EntitySearchBundle\Services\SearchService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'stinger:search:clear')]
class ClearIndexCommand extends Command {

	public function __construct(protected SearchService $searchService) {
		parent::__construct();
		$this->searchService = $searchService;
	}

	/**
	 *
	 * {@inheritdoc}
	 *
	 * @see \Symfony\Component\Console\Command\Command::configure()
	 */
	protected function configure(): void {
		$this->setDescription('Clears the configured search index');
	}

	/**
	 *
	 * {@inheritdoc}
	 *
	 * @see \Symfony\Component\Console\Command\Command::execute()
	 */
	protected function execute(InputInterface $input, OutputInterface $output): int {
		$this->searchService->clearIndex();
		return Command::SUCCESS;
	}
}

