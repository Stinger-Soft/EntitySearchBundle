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

use StingerSoft\EntitySearchBundle\DependencyInjection\Configuration;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

class ConfigurationTest extends \PHPUnit\Framework\TestCase {

	public function testGetConfigTreeBuilder() {
		$config = new Configuration();
		$tb = $config->getConfigTreeBuilder();
		$this->assertNotNull($tb);
		$this->assertInstanceOf(TreeBuilder::class, $tb);
	}
}