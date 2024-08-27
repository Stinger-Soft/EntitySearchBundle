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

namespace StingerSoft\EntitySearchBundle\Services;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;
use StingerSoft\EntitySearchBundle\Services\ClassUtils;
use StingerSoft\EntitySearchBundle\Model\Document;
use StingerSoft\EntitySearchBundle\Model\DocumentAdapter;
use StingerSoft\EntitySearchBundle\Services\Facet\FacetServiceInterface;

abstract class AbstractSearchService implements SearchService {

	/**
	 *
	 * @var EntityManagerInterface
	 */
	protected ?EntityManagerInterface $objectManager = null;

	/**
	 * @var ContainerInterface
	 */
	protected ?ContainerInterface $facetContainer = null;

	/**
	 *
	 * {@inheritdoc}
	 *
	 * @see \StingerSoft\EntitySearchBundle\Services\SearchService::getObjectManager()
	 */
	public function getObjectManager(): ?EntityManagerInterface {
		return $this->objectManager;
	}

	/**
	 *
	 * {@inheritdoc}
	 *
	 * @see \StingerSoft\EntitySearchBundle\Services\SearchService::setObjectManager()
	 *
	 * @required
	 */
	public function setObjectManager(EntityManagerInterface $om): void {
		if($this->objectManager) {
			return;
		}
		$this->objectManager = $om;
	}

	/**
	 *
	 * {@inheritdoc}
	 *
	 * @see \StingerSoft\EntitySearchBundle\Services\SearchService::setFacetContainer()
	 */
	public function setFacetContainer(ContainerInterface $facetContainer): void {
		$this->facetContainer = $facetContainer;
	}

	/**
	 *
	 * {@inheritdoc}
	 *
	 * @see \StingerSoft\EntitySearchBundle\Services\SearchService::getFacet()
	 */
	public function getFacet(string $facetId): FacetServiceInterface {
		return $this->facetContainer->get($facetId);
	}

	/**
	 *
	 * {@inheritdoc}
	 *
	 * @see \StingerSoft\EntitySearchBundle\Services\SearchService::createEmptyDocumentFromEntity()
	 */
	public function createEmptyDocumentFromEntity(object $entity): Document {
		$document = $this->newDocumentInstance();
		$clazz = $this->getClass($entity);
		$cmd = $this->getObjectManager()->getClassMetadata($clazz);
		$id = $cmd->getIdentifierValues($entity);

		$document->setEntityClass($clazz);
		$document->setEntityId(count($id) == 1 ? current($id) : $id);
		return $document;
	}

	/**
	 *
	 * {@inheritdoc}
	 *
	 * @see \StingerSoft\EntitySearchBundle\Services\SearchService::getOnlineHelp()
	 */
	public function getOnlineHelp(string $locale, string $defaultLocale = 'en'): ?string {
		return null;
	}

	protected function getClass($entity): string {
		return $this->objectManager->getClassMetadata(get_class($entity))->getName();
	}

	/**
	 * Creates a new document instance
	 *
	 * @return Document
	 */
	protected function newDocumentInstance(): Document {
		return new DocumentAdapter();
	}
}
