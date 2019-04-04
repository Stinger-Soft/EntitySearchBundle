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
namespace StingerSoft\EntitySearchBundle\Tests\DependencyInjection;

use StingerSoft\EntitySearchBundle\DependencyInjection\StingerSoftEntitySearchExtension;
use StingerSoft\EntitySearchBundle\Services\DoctrineListener;
use StingerSoft\EntitySearchBundle\Services\Mapping\EntityToDocumentMapperInterface;
use StingerSoft\EntitySearchBundle\Services\SearchService;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use StingerSoft\EntitySearchBundle\Tests\Fixtures\ORM\Whiskey;

class StingerSoftEntitySearchExtensionTest extends \PHPUnit\Framework\TestCase {

	public static $mockConfiguration = array('stinger_soft.entity_search' => array(
			'types' => array(
				'whiskey' => array(
					'mappings' => array(
						'title' => array(
							'propertyPath' => false 
						) 
					),
					'persistence' => array(
						'model' => Whiskey::class 
					) 
				) 
			)
		)
	);

	public function testDefault() {
		$container = new ContainerBuilder();
		$loader = new StingerSoftEntitySearchExtension();
		$loader->load(self::$mockConfiguration, $container);

		$this->assertArrayHasKey(SearchService::class, $container->getAliases());
		$this->assertArrayHasKey('stinger_soft.entity_search.dummy_search_service', $container->getDefinitions());
		$this->assertArrayHasKey(DoctrineListener::class, $container->getDefinitions());
		$this->assertArrayHasKey(EntityToDocumentMapperInterface::class, $container->getDefinitions());
	}
}