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
namespace StingerSoft\EntitySearchBundle\Command;

use StingerSoft\EntitySearchBundle\Services\SearchService;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ClearIndexCommand extends ContainerAwareCommand {

	/**
	 *
	 * {@inheritdoc}
	 *
	 * @see \Symfony\Component\Console\Command\Command::configure()
	 */
	protected function configure() {
		$this->setName('stinger:search:clear')->setDescription('Clears the configured search index');
	}

	/**
	 *
	 * {@inheritdoc}
	 *
	 * @see \Symfony\Component\Console\Command\Command::execute()
	 */
	protected function execute(InputInterface $input, OutputInterface $output) {
		/**
		 *
		 * @var SearchService $searchService
		 */
		$searchService = $this->getContainer()->get(SearchService::SERVICE_ID);
		$searchService->setObjectManager($this->getContainer()->get('doctrine.orm.entity_manager'));
		$searchService->clearIndex();
	}
}

