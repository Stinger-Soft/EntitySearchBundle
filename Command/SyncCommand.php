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

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\Common\Persistence\ObjectManager;
use StingerSoft\EntitySearchBundle\Services\Mapping\EntityToDocumentMapperInterface;
use StingerSoft\EntitySearchBundle\Services\SearchService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;

class SyncCommand extends ContainerAwareCommand {

	/**
	 *
	 * @var EntityToDocumentMapperInterface
	 */
	protected $entityToDocumentMapper;

	/**
	 *
	 * @var SearchService
	 */
	protected $searchService;

	/**
	 *
	 * Cache for the default upload path of this platform
	 *
	 * @var string
	 */
	protected static $defaultUploadPath = null;

	/**
	 *
	 * {@inheritdoc}
	 *
	 * @see \Symfony\Component\Console\Command\Command::configure()
	 */
	protected function configure() {
		/* @formatter:off */
		$this
			->setName('stinger:search:sync')
			->addArgument('entity', InputArgument::REQUIRED, 'The entity you want to index')
			->addOption('source', null, InputArgument::OPTIONAL, 'specify a source from where to load entities [relational, mongodb] (unsupported!)', 'relational')
			->setDescription('Index all entities');
		/* @formatter:on */
	}

	/**
	 *
	 * {@inheritdoc}
	 *
	 * @see \Symfony\Component\Console\Command\Command::execute()
	 */
	protected function execute(InputInterface $input, OutputInterface $output) {
		// Detect upload path
		if(!self::$defaultUploadPath) {
			$root = $this->getContainer()->get('kernel')->getRootDir();
			self::$defaultUploadPath = $root . '/../web/uploads';
		}
		
		// Get the entity argument
		$entity = $input->getArgument('entity');
		
		if($entity == 'all') {
			/**
			 * @var EntityManager $entityManager
			 */
			$entityManager = $this->getContainer()->get('doctrine.orm.entity_manager');
			
			$meta = $entityManager->getMetadataFactory()->getAllMetadata();
			
			/**
			 * @var EntityToDocumentMapperInterface $mapper
			 */
			$mapper = $this->getContainer()->get(EntityToDocumentMapperInterface::SERVICE_ID);
			
			/**
			 * @var ClassMetadata $m
			 */
			foreach($meta as $m) {
				
				if($m->getReflectionClass()->isAbstract() || $m->getReflectionClass()->isInterface()) {
					continue;
				}
				if(!$mapper->isClassIndexable($m->getReflectionClass()->getName())) {
					continue;
				}
				$this->indexEntity($input, $output, $m->getReflectionClass()->getName());
			}
		} else {
			$this->indexEntity($input, $output, $entity);
		}
	}

	protected function indexEntity(InputInterface $input, OutputInterface $output, $entity) {
		$output->writeln(sprintf('<comment>Indexing entities of type "%s"</comment>', $entity));
		/**
		 *
		 * @var EntityManager $entityManager
		 */
		$entityManager = $this->getContainer()->get('doctrine.orm.entity_manager');
		$repository = null;
		try {
			// Get repository for the given entity type
			$repository = $entityManager->getRepository($entity);
		} catch(\Exception $e) {
			$output->writeln(sprintf('<error>No repository found for "%s", check your input</error>', $entity));
			return;
		}
		
		// Get all entities
		$queryBuilder = $repository->createQueryBuilder('e');
		$countQueryBuilder = $repository->createQueryBuilder('e')->select('COUNT(e)');
		$entityCount = $countQueryBuilder->getQuery()->getSingleScalarResult();
		
		$iterableResult = $queryBuilder->getQuery()->iterate();
		if($entityCount == 0) {
			$output->writeln('<comment>No entities found for indexing</comment>');
			return;
		}
		
		$entitiesIndexed = 0;
		
		// Index each entity seperate
		foreach ($iterableResult as $row) {
			$entity = $row[0];
			if($this->getEntityToDocumentMapper()->isIndexable($entity)) {
				$document = $this->getEntityToDocumentMapper()->createDocument($entityManager, $entity);
				if($document === false) continue;
				$this->getSearchService($entityManager)->saveDocument($document);
				$entitiesIndexed++;
				if($entitiesIndexed % 50 == 0) {
					$entityManager->flush();
				}
			}
			
		}
		$entityManager->flush();
		$entityManager->clear();
		$output->writeln('<comment>Indexed ' . $entitiesIndexed . ' entities</comment>');
	}

	/**
	 *
	 * @return EntityToDocumentMapperInterface
	 */
	protected function getEntityToDocumentMapper() {
		if(!$this->entityToDocumentMapper) {
			$this->entityToDocumentMapper = $this->getContainer()->get(EntityToDocumentMapperInterface::SERVICE_ID);
		}
		return $this->entityToDocumentMapper;
	}

	/**
	 *
	 * @return SearchService
	 */
	protected function getSearchService(ObjectManager $manager) {
		if(!$this->searchService) {
			$this->searchService = $this->getContainer()->get(SearchService::SERVICE_ID);
		}
		$this->searchService->setObjectManager($manager);
		return $this->searchService;
	}
}