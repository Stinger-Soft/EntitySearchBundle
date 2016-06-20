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
namespace StingerSoft\EntitySearchBundle\Tests\DependencyInjection;

use StingerSoft\EntitySearchBundle\DependencyInjection\StingerSoftEntitySearchExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class StingerSoftEntitySearchExtensionTest extends \PHPUnit_Framework_TestCase {

	public function testDefault() {
		$container = new ContainerBuilder();
		$loader = new StingerSoftEntitySearchExtension();
		$loader->load(array(
			array() 
		), $container);
		
		$this->assertArrayHasKey('stinger_soft.entity_search.doctrine_update_listener', $container->getDefinitions());
		$this->assertArrayHasKey('stinger_soft.entity_search.doctrine.listener', $container->getDefinitions());
	}
}