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
	protected $file = null;

	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \StingerSoft\EntitySearchBundle\Model\Document::addField()
	 */
	public function addField($fieldname, $value) {
		$this->fields[$fieldname] = $value;
	}

	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \StingerSoft\EntitySearchBundle\Model\Document::getFields()
	 */
	public function getFields() {
		return $this->fields;
	}

	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \StingerSoft\EntitySearchBundle\Model\Document::getFieldValue()
	 */
	public function getFieldValue($field) {
		return isset($this->fields[$field]) ? $this->fields[$field] : null;
	}

	/**
	 *
	 * {@inheritDoc}
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
	 * {@inheritDoc}
	 *
	 * @see \StingerSoft\EntitySearchBundle\Model\Document::setEntityClass()
	 */
	public function setEntityClass($clazz) {
		$this->entityClass = $clazz;
	}

	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \StingerSoft\EntitySearchBundle\Model\Document::getEntityClass()
	 */
	public function getEntityClass() {
		return $this->entityClass;
	}

	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \StingerSoft\EntitySearchBundle\Model\Document::setEntityId()
	 */
	public function setEntityId($id) {
		$this->entityId = $id;
	}

	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \StingerSoft\EntitySearchBundle\Model\Document::getEntityId()
	 */
	public function getEntityId() {
		return $this->entityId;
	}

	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \StingerSoft\EntitySearchBundle\Model\Document::setFile()
	 */
	public function setFile($path) {
		$this->file = $path;
	}

	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \StingerSoft\EntitySearchBundle\Model\Document::__get()
	 */
	public function __get($name) {
		return $this->getFieldValue($name);
	}

	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \StingerSoft\EntitySearchBundle\Model\Document::__isset()
	 */
	public function __isset($name) {
		return $this->getFieldValue($name) !== null;
	}
}