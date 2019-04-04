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
namespace StingerSoft\EntitySearchBundle\Tests\Model\Result;

use StingerSoft\EntitySearchBundle\Model\Result\FacetSetAdapter;
use StingerSoft\EntitySearchBundle\Model\Document;

class FacetSetAdapterTest extends \PHPUnit\Framework\TestCase {

	public function testAddFacetValue(){
		$facets = new FacetSetAdapter(array());
		$this->assertCount(0, $facets);
		$facets->addFacetValue(Document::FIELD_AUTHOR, 'Oliver Kotte');
		$this->assertCount(1, $facets);
		$this->assertCount(1, $facets->getFacet(Document::FIELD_AUTHOR));
		$facets->addFacetValue(Document::FIELD_AUTHOR, 'Florian Meyer');
		$this->assertCount(1, $facets);
		$this->assertCount(2, $facets->getFacet(Document::FIELD_AUTHOR));
		
		foreach($facets as $facetKey => $facetValues){
			$this->assertEquals(Document::FIELD_AUTHOR, $facetKey);
			$this->assertCount(2, $facetValues);
		}
	}
	
	public function testGetFacet(){
		$facets = new FacetSetAdapter(array());
		$this->assertCount(0, $facets);
		$this->assertNull($facets->getFacet('NotExisting'));
	}
	
	
}