<?php
declare(strict_types=1);

namespace StingerSoft\EntitySearchBundle\DependencyInjection\Compiler;

use StingerSoft\EntitySearchBundle\Services\Facet\FacetServiceInterface;
use StingerSoft\EntitySearchBundle\Services\SearchService;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Compiler\ServiceLocatorTagPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class FacetCompilerPass implements CompilerPassInterface {

	/**
	 * @param ContainerBuilder $container
	 */
	public function process(ContainerBuilder $container): void {
		if(!$container->hasAlias(SearchService::class)) {
			return;
		}

		$serviceId = (string)$container->getAlias(SearchService::class);

		if(!$container->hasDefinition($serviceId)) {
			return;
		}

		$definition = $container->getDefinition($serviceId);

		$servicesMap = array();
		// Builds an array with fully-qualified type class names as keys and service IDs as values
		foreach($container->findTaggedServiceIds(FacetServiceInterface::TAG_NAME, true) as $serviceId => $tag) {
			// Add form type service to the service locator
			$serviceDefinition = $container->getDefinition($serviceId);
			$servicesMap[$serviceDefinition->getClass()] = new Reference($serviceId);
			$servicesMap[$serviceId] = new Reference($serviceId);
		}
		$definition->addMethodCall('setFacetContainer', [ServiceLocatorTagPass::register($container, $servicesMap)]);
	}
}
