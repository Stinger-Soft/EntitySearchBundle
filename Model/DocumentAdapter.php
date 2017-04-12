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
namespace StingerSoft\EntitySearchBundle\Model;

class DocumentAdapter implements Document {

	/**
	 * Stores are fields with their values which should be stored in the index
	 *
	 * @var array
	 */
	protected $fields = array();

	/**
	 *
	 * @var string
	 */
	protected $entityClass = null;

	/**
	 *
	 * @var mixed
	 */
	protected $entityId = null;

	/**
	 *
	 * @var string
	 */
	protected $entityType = null;

	/**
	 *
	 * @var string
	 */
	protected $file = null;

	/**
	 *
	 * {@inheritdoc}
	 *
	 * @see \StingerSoft\EntitySearchBundle\Model\Document::addField()
	 */
	public function addField($fieldname, $value) {
		$this->fields[$fieldname] = $value;
	}

	/**
	 *
	 * {@inheritdoc}
	 *
	 * @see \StingerSoft\EntitySearchBundle\Model\Document::getFields()
	 */
	public function getFields() {
		return $this->fields;
	}

	/**
	 *
	 * {@inheritdoc}
	 *
	 * @see \StingerSoft\EntitySearchBundle\Model\Document::getFieldValue()
	 */
	public function getFieldValue($field) {
		return isset($this->fields[$field]) ? $this->fields[$field] : null;
	}

	/**
	 *
	 * {@inheritdoc}
	 *
	 * @see \StingerSoft\EntitySearchBundle\Model\Document::addMultiValueField()
	 */
	public function addMultiValueField($field, $value) {
		if(!array_key_exists($field, $this->fields)) {
			$this->fields[$field] = array(
				$value 
			);
		} else if(!in_array($value, $this->fields[$field])) {
			$this->fields[$field][] = $value;
		}
	}

	/**
	 *
	 * {@inheritdoc}
	 *
	 * @see \StingerSoft\EntitySearchBundle\Model\Document::setEntityClass()
	 */
	public function setEntityClass($clazz) {
		$this->entityClass = $clazz;
		if(!$this->entityType) {
			$this->entityType = $clazz;
		}
	}

	/**
	 *
	 * {@inheritdoc}
	 *
	 * @see \StingerSoft\EntitySearchBundle\Model\Document::getEntityClass()
	 */
	public function getEntityClass() {
		return $this->entityClass;
	}

	/**
	 *
	 * {@inheritdoc}
	 *
	 * @see \StingerSoft\EntitySearchBundle\Model\Document::setEntityId()
	 */
	public function setEntityId($id) {
		$this->entityId = $id;
	}

	/**
	 *
	 * {@inheritdoc}
	 *
	 * @see \StingerSoft\EntitySearchBundle\Model\Document::getEntityId()
	 */
	public function getEntityId() {
		return $this->entityId;
	}

	/**
	 *
	 * {@inheritdoc}
	 *
	 * @see \StingerSoft\EntitySearchBundle\Model\Document::setEntityType()
	 */
	public function setEntityType($type) {
		$this->entityType = $type;
	}

	/**
	 *
	 * {@inheritdoc}
	 *
	 * @see \StingerSoft\EntitySearchBundle\Model\Document::getEntityType()
	 */
	public function getEntityType() {
		return $this->entityType ? $this->entityType : $this->getEntityClass();
	}

	/**
	 *
	 * {@inheritdoc}
	 *
	 * @see \StingerSoft\EntitySearchBundle\Model\Document::setFile()
	 */
	public function setFile($path) {
		$this->file = $path;
	}
	
	/**
	 *
	 * {@inheritdoc}
	 *
	 * @see \StingerSoft\EntitySearchBundle\Model\Document::getFile()
	 */
	public function getFile() {
		return $this->file;
	}

	/**
	 *
	 * {@inheritdoc}
	 *
	 * @see \StingerSoft\EntitySearchBundle\Model\Document::__get()
	 */
	public function __get($name) {
		return $this->getFieldValue($name);
	}

	/**
	 *
	 * {@inheritdoc}
	 *
	 * @see \StingerSoft\EntitySearchBundle\Model\Document::__isset()
	 */
	public function __isset($name) {
		return $this->getFieldValue($name) !== null;
	}
}