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

class QueryTypeTest extends TypeTestCase {

	public function testInitialCall() {
		$form = $this->factory->create(QueryType::class);
		
		$query = new Query('Hemelinger');
		
		$formData = array(
			'searchTerm' => 'Hemelinger' 
		);
		
		// submit the data to the form directly
		$form->submit($formData);
		
		$this->assertTrue($form->isSynchronized());
		$this->assertTrue($form->isValid());
		$this->assertEquals($query, $form->getData());
		
		// $view = $form->createView();
		// $children = $view->children;
		
		// foreach (array_keys($formData) as $key) {
		// $this->assertArrayHasKey($key, $children);
		// }
	}
	
	
	public function testWithNotExistingFacets(){
		
		$query = new Query('Hemelinger', array(), array(
			Document::FIELD_TYPE
		));
		
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
		$this->assertEquals($expectedQuery, $form->getData());
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
				new FacetType(), 
			), array()) 
		);
	}
}