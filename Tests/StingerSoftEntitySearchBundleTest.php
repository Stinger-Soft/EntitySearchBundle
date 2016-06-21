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

use StingerSoft\EntitySearchBundle\StingerSoftEntitySearchBundle;

class StingerSoftEntitySearchBundleTest extends \PHPUnit_Framework_TestCase {

	public function testRequiredBundles() {
		$bundles = StingerSoftEntitySearchBundle::getRequiredBundles('dev');
		$this->assertCount(1, $bundles);
		$this->assertArrayHasKey('StingerSoftEntitySearchBundle', $bundles);
	}
}