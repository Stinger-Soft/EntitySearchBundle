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
namespace StingerSoft\EntitySearchBundle\Tests;

use Symfony\Component\Form\Test\TypeTestCase;
use StingerSoft\EntitySearchBundle\Form\QueryType;
use StingerSoft\EntitySearchBundle\Form\FacetType;
use StingerSoft\EntitySearchBundle\Model\Query;
use Symfony\Component\Form\PreloadedExtension;
use StingerSoft\EntitySearchBundle\Model\Document;
use StingerSoft\EntitySearchBundle\Model\ResultSetAdapter;
use StingerSoft\EntitySearchBundle\Model\Result\FacetSetAdapter;
use Symfony\Component\Form\Extension\Validator\Type\FormTypeValidatorExtension;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Form\Forms;
use Symfony\Component\Form\FormBuilder;

class QueryTypeTest extends TypeTestCase {

	public function testInitialCall() {
		$query = new Query('Hemelinger');
		
		$form = $this->factory->create(QueryType::class, $query, array(
			'used_facets' => array() 
		));
		
		$formData = array(
			'searchTerm' => 'Hemelinger' 
		);
		
		// submit the data to the form directly
		$form->submit($formData);
		
		$this->assertTrue($form->isSynchronized());
		$this->assertTrue($form->isValid());
		$this->assertEquals($query, $form->getData());
	}

	public function testWithNotExistingFacets() {
		$query = new Query('Hemelinger', array(), array(
			Document::FIELD_TYPE 
		));
		
		$form = $this->factory->create(QueryType::class, $query, array(
			'used_facets' => $query->getUsedFacets() 
		));
		
		$expectedQuery = new Query('Hemelinger', array(
			Document::FIELD_TYPE => array(
				'\StingerSoft\TestBundle\Entity\Test' 
			) 
		), array(
			Document::FIELD_TYPE 
		));
		
		$formData = array(
			'searchTerm' => 'Hemelinger',
			'facet_type' => array(
				'\StingerSoft\TestBundle\Entity\Test' 
			) 
		);
		
		// submit the data to the form directly
		$form->submit($formData);
		
		$this->assertTrue($form->isSynchronized());
		$this->assertTrue($form->isValid());
		$this->assertEquals($expectedQuery, $form->getData());
	}

	public function testWithResult() {
		$query = new Query('Hemelinger', array(
			Document::FIELD_TYPE => array(
				'\StingerSoft\TestBundle\Entity\Test' 
			) 
		), array(
			Document::FIELD_TYPE 
		));
		
		$result = new ResultSetAdapter();
		$typeFacets = new FacetSetAdapter();
		$typeFacets->addFacetValue(Document::FIELD_TYPE, '\StingerSoft\TestBundle\Entity\Test');
		$typeFacets->addFacetValue(Document::FIELD_TYPE, '\StingerSoft\TestBundle\Entity\TestNew');
		$result->setFacets($typeFacets);
		
		$form = $this->factory->create(QueryType::class, $query, array(
			'used_facets' => $query->getUsedFacets(),
		));
		
		$expectedQuery = new Query('Hemelinger', array(
			Document::FIELD_TYPE => array(
				'\StingerSoft\TestBundle\Entity\Test' 
			) 
		), array(
			Document::FIELD_TYPE 
		));
		
		$formData = array(
			'searchTerm' => 'Hemelinger',
			'facet_type' => array(
				'\StingerSoft\TestBundle\Entity\Test' 
			) 
		);
		
		// submit the data to the form directly
		$form->submit($formData);
		
		$this->assertTrue($form->isSynchronized());
		$this->assertTrue($form->isValid());
		$this->assertTrue($form->isSubmitted());
		$this->assertCount(0, $form->getErrors(true, true));
		
		$form = $this->factory->create(QueryType::class, $query, array(
			'used_facets' => $query->getUsedFacets(),
			'result' => $result
		));
		
		$this->assertEquals($expectedQuery, $form->getData());
		
		
		$view = $form->createView();
		$children = $view->children;
		
		foreach(array_keys($formData) as $key) {
			$this->assertArrayHasKey($key, $children);
		}
		
		$typeForm = $view->offsetGet('facet_type');
		$this->assertEquals(2, $typeForm->count());
		$this->assertContains('\StingerSoft\TestBundle\Entity\Test', $typeForm->vars['value']);
// 		$this->assertCount(2, $typeForm->vars['choices']);
	}
	
	protected function mockValidator() {
		$validator = $this->getMockBuilder(ValidatorInterface::class)->setMethods(array('validate', 'getErrors'))->getMockForAbstractClass();
		$validator->method('validate')->willReturn(array());
		$validator->method('getErrors')->willReturn(array());
		
		return $validator;
	}
	
	protected function setUp()
	{
		$this->factory = Forms::createFormFactoryBuilder()
		->addExtensions($this->getExtensions())
		->addTypeExtension(new FormTypeValidatorExtension($this->mockValidator()))
		->getFormFactory();
		
		$this->dispatcher = $this->getMockBuilder('Symfony\Component\EventDispatcher\EventDispatcherInterface')->getMock();
		$this->builder = new FormBuilder(null, null, $this->dispatcher, $this->factory);
		parent::setUp();
	}

	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \Symfony\Component\Form\Test\FormIntegrationTestCase::getExtensions()
	 */
	protected function getExtensions() {
		return array(
			new PreloadedExtension(array(
				new QueryType(),
				new FacetType() 
			), array(
				new FormTypeValidatorExtension($this->mockValidator()),
			))
		);
	}
}