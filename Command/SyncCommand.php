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

use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Platforms\SQLServerPlatform;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\Mapping\MappingException;
use Exception;
use StingerSoft\EntitySearchBundle\Events\DocumentPreSaveEvent;
use StingerSoft\EntitySearchBundle\Services\Mapping\EntityToDocumentMapperInterface;
use StingerSoft\EntitySearchBundle\Services\SearchService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

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
	 * @var EventDispatcherInterface|null
	 */
	protected $eventDispatcher;

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
			$root = $kernel->getProjectDir();
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
	 * @param InputInterface $input
	 * @param OutputInterface $output
	 * @return int
	 * @throws DBALException
	 * @throws MappingException
	 * @throws NoResultException
	 * @throws NonUniqueResultException
	 * @throws ORMException
	 * @throws OptimisticLockException
	 * @see \Symfony\Component\Console\Command\Command::execute()
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
				$output->writeln('');
			}
		} else {
			$this->indexEntity($input, $output, $entity);
		}
		return 0;
	}

	/**
	 * @param InputInterface $input
	 * @param OutputInterface $output
	 * @param $entity
	 * @throws DBALException
	 * @throws NoResultException
	 * @throws NonUniqueResultException
	 * @throws ORMException
	 * @throws OptimisticLockException
	 * @throws MappingException
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
		} catch(Exception $e) {
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
		$progressBar = new ProgressBar($output, $entityCount);
		$progressBar->display();

		$entitiesIndexed = 0;

		// Index each entity separate
		foreach($iterableResult as $row) {
			$entity = $useBatch ? $row[0] : $row;
			$progressBar->advance();
			if($this->entityToDocumentMapper->isIndexable($entity)) {
				$document = $this->entityToDocumentMapper->createDocument($entityManager, $entity);
				if($document === null) {
					continue;
				}
				try {
					if($this->getEventDispatcher()) {
						$event = new DocumentPreSaveEvent($document);
						$this->getEventDispatcher()->dispatch($event, DocumentPreSaveEvent::NAME);
					}
					$this->searchService->saveDocument($document);
					$entitiesIndexed++;
				} catch(Exception $e) {
					$output->writeln('<error>Failed to index entity with ID ' . $document->getEntityId() . '</error>');
				}
				if($entitiesIndexed % 50 === 0) {
					$entityManager->flush();
				}
			}

		}
		$entityManager->flush();
		$entityManager->clear();
		$progressBar->finish();
		$output->writeln('');
		$output->writeln('<comment>Indexed ' . $entitiesIndexed . ' entities</comment>');
	}

	/**
	 * @return EventDispatcherInterface|null
	 */
	public function getEventDispatcher(): ?EventDispatcherInterface {
		return $this->eventDispatcher;
	}

	/**
	 * @param EventDispatcherInterface|null $eventDispatcher
	 * @required
	 */
	public function setEventDispatcher(EventDispatcherInterface $eventDispatcher = null): void {
		$this->eventDispatcher = $eventDispatcher;
	}

}