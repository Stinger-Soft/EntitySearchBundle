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
namespace StingerSoft\EntitySearchBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 */
class StingerSoftEntitySearchBundle extends Bundle {

	public static function getRequiredBundles($env) {
		$bundles = array();
		$bundles['StingerSoftEntitySearchBundle'] = '\StingerSoft\EntitySearchBundle\StingerSoftEntitySearchBundle';
		$bundles['KnpPaginatorBundle'] = 'Knp\Bundle\PaginatorBundle\KnpPaginatorBundle';
		return $bundles;
	}
}