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
namespace StingerSoft\EntitySearchBundle\Services\Mapping;

use StingerSoft\EntitySearchBundle\Model\SearchableEntity;
use Doctrine\Persistence\ObjectManager;
use StingerSoft\EntitySearchBundle\Model\Document;
use Symfony\Component\PropertyAccess\PropertyAccess;
use StingerSoft\EntitySearchBundle\Services\SearchService;

/**
 * Handles the creation of documents out of entities
 */
class EntityToDocumentMapper implements EntityToDocumentMapperInterface {

	/**
	 *
	 * @var SearchService
	 */
	protected SearchService $searchService;

	/**
	 *
	 * @var array
	 */
	protected array $mapping = array();

	/**
	 *
	 * @var string[string]
	 */
	protected array $cachedMapping = array();

	/**
	 * Constructor
	 *
	 * @param SearchService $searchService        	
	 */
	public function __construct(SearchService $searchService, array $mapping = array()) {
		$this->searchService = $searchService;
		foreach($mapping as $key => $config) {
			if(!isset($config['mappings'])) {
				throw new \InvalidArgumentException($key . ' has no mapping defined!');
			}
			if(!isset($config['persistence'])) {
				throw new \InvalidArgumentException($key . ' has no persistence defined!');
			}
			if(!isset($config['persistence']['model'])) {
				throw new \InvalidArgumentException($key . ' has no model defined!');
			}
			$map = array();
			foreach($config['mappings'] as $fieldKey => $fieldConfig) {
				$map[$fieldKey] = isset($fieldConfig['propertyPath']) && $fieldConfig['propertyPath'] ? $fieldConfig['propertyPath'] : $fieldKey;
			}
			
			$this->mapping[$config['persistence']['model']] = $map;
		}
	}

	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \StingerSoft\EntitySearchBundle\Services\Mapping\EntityToDocumentMapperInterface::isIndexable()
	 */
	public function isIndexable(object $object) : bool {
		if($object instanceof SearchableEntity) {
			return true;
		}
		if(count($this->getMapping(get_class($object))) > 0) {
			return true;
		}
		return false;
	}

	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \StingerSoft\EntitySearchBundle\Services\Mapping\EntityToDocumentMapperInterface::isClassIndexable()
	 * @throws \ReflectionException
	 */
	public function isClassIndexable(string $clazz) : bool {
		$reflectionClass = new \ReflectionClass($clazz);
		if(array_key_exists(SearchableEntity::class, $reflectionClass->getInterfaces())) {
			return true;
		}
		if(count($this->getMapping($clazz)) > 0) {
			return true;
		}
		return false;
	}


	/**
	 * {@inheritDoc}
	 * @see \StingerSoft\EntitySearchBundle\Services\Mapping\EntityToDocumentMapperInterface::createDocument()
	 */
	public function createDocument(ObjectManager $manager, object $object) : ?Document {
		if(!$this->isIndexable($object))
			return null;
		$document = $this->searchService->createEmptyDocumentFromEntity($object);
		$index = $this->fillDocument($document, $object);
		if($index === false)
			return null;
		
		return $document;
	}

	/**
	 * Fills the given document based on the object
	 *
	 * @param Document $document        	
	 * @param object $object        	
	 * @return boolean
	 */
	protected function fillDocument(Document $document, object $object) : bool {
		if($object instanceof SearchableEntity) {
			return $object->indexEntity($document);
		}
		$mapping = $this->getMapping(\get_class($object));
		$accessor = PropertyAccess::createPropertyAccessor();
		foreach($mapping as $fieldName => $propertyPath) {
			$document->addField($fieldName, $accessor->getValue($object, $propertyPath));
		}
		return true;
	}

	/**
	 * Fetches the mapping for the given object including the mapping of superclasses
	 *
	 * @return string[string]
	 * @throws \ReflectionException
	 */
	protected function getMapping(string $clazz) : array {
		if(isset($this->cachedMapping[$clazz])) {
			return $this->cachedMapping[$clazz];
		}
		$ref = new \ReflectionClass($clazz);
		
		$mapping = array();
		
		foreach($this->mapping as $className => $config) {
			if($clazz === $className || $ref->isSubclassOf($className)) {
				$mapping[] = $config;
			}
		}
		$mapping = \array_merge(...$mapping);
		$this->cachedMapping[$clazz] = $mapping;
		
		return $mapping;
	}
}
