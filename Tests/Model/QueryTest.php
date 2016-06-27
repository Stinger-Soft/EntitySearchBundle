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
namespace StingerSoft\EntitySearchBundle\Tests\Model;

use StingerSoft\EntitySearchBundle\Model\Query;
use StingerSoft\EntitySearchBundle\Model\Document;

class QueryTest extends \PHPUnit_Framework_TestCase {

	public function testSetGetFacets() {
		$query = new Query();
		$facets = array(
			Document::FIELD_AUTHOR => array(
				'Oliver Kotte',
				'Florian Meyer' 
			) 
		);
		$query->setFacets($facets);
		$this->assertEquals($facets, $query->getFacets());
	}
	
	public function testSetGetUsedFacets() {
		$query = new Query();
		$facets = array(
			Document::FIELD_AUTHOR,
		);
		$query->setUsedFacets($facets);
		$this->assertEquals($facets, $query->getUsedFacets());
	}

	public function testMagicMethods() {
		$facets = array(
			Document::FIELD_AUTHOR => array(
				'Oliver Kotte',
				'Florian Meyer'
			)
		);
		$query = new Query(null, $facets);
		
		$this->assertTrue($query->__isset('facet_'.Document::FIELD_AUTHOR));
		$this->assertEquals($facets[Document::FIELD_AUTHOR], $query->__get('facet_'.Document::FIELD_AUTHOR));
		
		$this->assertTrue($query->__isset('facet_'.Document::FIELD_CONTENT));
		$this->assertEmpty($query->__get('facet_'.Document::FIELD_CONTENT));
		
		$this->assertFalse($query->__isset('WrongPrefixedPropety'));
		$this->assertNull($query->__get('WrongPrefixedPropety'));
		
		$query->__set('facet_'.Document::FIELD_EDITORS, $facets[Document::FIELD_AUTHOR]);
		$this->assertEquals($facets[Document::FIELD_AUTHOR], $query->__get('facet_'.Document::FIELD_EDITORS));
		
		
		
	}
}