<?php
declare(strict_types=1);

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

use StingerSoft\EntitySearchBundle\DependencyInjection\Compiler\FacetCompilerPass;
use StingerSoft\EntitySearchBundle\Services\Facet\FacetServiceInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 */
class StingerSoftEntitySearchBundle extends Bundle {

	public static function getRequiredBundles(string $env, array &$requiredBundles = []): array {

		if(isset($requiredBundles['StingerSoftEntitySearchBundle'])) {
			return $requiredBundles;
		}

		$requiredBundles['StingerSoftEntitySearchBundle'] = '\StingerSoft\EntitySearchBundle\StingerSoftEntitySearchBundle';
		$requiredBundles['KnpPaginatorBundle'] = 'Knp\Bundle\PaginatorBundle\KnpPaginatorBundle';
		return $requiredBundles;
	}

	public function build(ContainerBuilder $container): void {
		$container->registerForAutoconfiguration(FacetServiceInterface::class)->addTag(FacetServiceInterface::TAG_NAME);
		$container->addCompilerPass(new FacetCompilerPass());
	}
}
