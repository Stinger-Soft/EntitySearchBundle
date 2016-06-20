<?php

/*
 * This file is part of the Stinger Enity Search package.
 *
 * (c) Oliver Kotte <oliver.kotte@stinger-soft.net>
 * (c) Florian Meyer <florian.meyer@stinger-soft.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace StingerSoft\EntitySearchBundle\Tests\Model;

use StingerSoft\EntitySearchBundle\Model\DocumentAdapter;
use StingerSoft\EntitySearchBundle\Tests\Fixtures\ORM\Beer;
use StingerSoft\EntitySearchBundle\Model\Document;

class DocumentAdapterTest extends \PHPUnit_Framework_TestCase {

	public function testAddFields() {
		$doc = new DocumentAdapter();
		
		$doc->setEntityClass(Beer::class);
		$doc->setEntityId(1);
		$doc->addField(Document::FIELD_AUTHOR, 'florian_meyer');
		$doc->addMultiValueField(Document::FIELD_EDITORS, 'florian_meyer');
		$doc->addMultiValueField(Document::FIELD_EDITORS, 'oliver_kotte');
		
		$this->assertEquals(Beer::class, $doc->getEntityClass());
		$this->assertEquals(1, $doc->getEntityId());
		
		$fields = $doc->getFields();
		$this->assertArrayHasKey(Document::FIELD_AUTHOR, $fields);
		$this->assertNotNull($doc->getFieldValue(Document::FIELD_AUTHOR));
		$this->assertArrayHasKey(Document::FIELD_EDITORS, $fields);
		$this->assertNotNull($doc->getFieldValue(Document::FIELD_EDITORS));
		$this->assertContains('florian_meyer', $fields[Document::FIELD_EDITORS]);
		
		$this->assertNull($doc->getFieldValue(Document::FIELD_ROLES));
	}
}