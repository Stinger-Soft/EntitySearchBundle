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

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\DBAL\Platforms\SQLServerPlatform;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use StingerSoft\EntitySearchBundle\Services\Mapping\EntityToDocumentMapperInterface;
use StingerSoft\EntitySearchBundle\Services\SearchService;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\KernelInterface;

class SyncCommand extends Command {

	/**
	 * @var string|null The default command name
	 */
	protected static $defaultName = 'stinger:search:sync';

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

	public function __construct(SearchService $searchService, EntityToDocumentMapperInterface $mapper, KernelInterface $kernel) {
		parent::__construct();
		$this->searchService = $searchService;
		$this->entityToDocumentMapper = $mapper;
		// Detect upload path
		if(!self::$defaultUploadPath) {
			$root = $kernel->getRootDir();
			self::$defaultUploadPath = $root . '/../web/uploads';
		}
	}

	/**
	 *
	 * {@inheritdoc}
	 *
	 * @see \Symfony\Component\Console\Command\Command::configure()
	 */
	protected function configure() {
		/* @formatter:off */
		$this
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
	 * @throws \Doctrine\Common\Persistence\Mapping\MappingException
	 * @throws \Doctrine\DBAL\DBALException
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 * @throws \Doctrine\ORM\ORMException
	 * @throws \Doctrine\ORM\OptimisticLockException
	 */
	protected function execute(InputInterface $input, OutputInterface $output) {


		// Get the entity argument
		$entity = $input->getArgument('entity');

		if($entity === 'all') {
			/**
			 * @var EntityManager $entityManager
			 */
			$entityManager = $this->searchService->getObjectManager();

			$meta = $entityManager->getMetadataFactory()->getAllMetadata();

			/**
			 * @var ClassMetadata $m
			 */
			foreach($meta as $m) {

				if($m->getReflectionClass()->isAbstract() || $m->getReflectionClass()->isInterface()) {
					continue;
				}
				if(!$this->entityToDocumentMapper->isClassIndexable($m->getReflectionClass()->getName())) {
					continue;
				}
				$this->indexEntity($input, $output, $m->getReflectionClass()->getName());
			}
		} else {
			$this->indexEntity($input, $output, $entity);
		}
	}

	/**
	 * @param InputInterface $input
	 * @param OutputInterface $output
	 * @param $entity
	 * @throws \Doctrine\Common\Persistence\Mapping\MappingException
	 * @throws \Doctrine\DBAL\DBALException
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 * @throws \Doctrine\ORM\ORMException
	 * @throws \Doctrine\ORM\OptimisticLockException
	 */
	protected function indexEntity(InputInterface $input, OutputInterface $output, $entity) {
		$output->writeln(sprintf('<comment>Indexing entities of type "%s"</comment>', $entity));
		/**
		 *
		 * @var EntityManager $entityManager
		 */
		$entityManager = $this->searchService->getObjectManager();
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
		$entityCount = (int)$countQueryBuilder->getQuery()->getSingleScalarResult();

		$useBatch = !($entityManager->getConnection()->getDatabasePlatform() instanceof SQLServerPlatform);
		$iterableResult = $useBatch ? $queryBuilder->getQuery()->iterate() : $queryBuilder->getQuery()->getResult();
		if($entityCount === 0) {
			$output->writeln('<comment>No entities found for indexing</comment>');
			return;
		}

		$entitiesIndexed = 0;

		// Index each entity separate
		foreach($iterableResult as $row) {
			$entity = $useBatch ? $row[0] : $row;
			if($this->entityToDocumentMapper->isIndexable($entity)) {
				$document = $this->entityToDocumentMapper->createDocument($entityManager, $entity);
				if($document === null) continue;
				try {
					$this->searchService->saveDocument($document);
					$entitiesIndexed++;
				} catch(\Exception $e) {
					$output->writeln('<error>Failed to index entity with ID ' . $document->getEntityId() . '</error>');
				}
				if($entitiesIndexed % 50 === 0) {
					$entityManager->flush();
				}
			}

		}
		$entityManager->flush();
		$entityManager->clear();
		$output->writeln('<comment>Indexed ' . $entitiesIndexed . ' entities</comment>');
	}

}