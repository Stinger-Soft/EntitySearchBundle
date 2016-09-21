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
namespace StingerSoft\EntitySearchBundle\DependencyInjection;

use StingerSoft\EntitySearchBundle\Services\Mapping\EntityToDocumentMapperInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * A Keen Metronic Admin Theme (http://www.keenthemes.com/) Bundle for Symfony2
 * with some additional libraries and PecPlatform specific customization.
 */
class StingerSoftEntitySearchExtension extends Extension {

	/**
	 *
	 * {@inheritDoc}
	 *
	 */
	public function load(array $configs, ContainerBuilder $container) {
		$configuration = new Configuration();
		$config = $this->processConfiguration($configuration, $configs);
		
		$loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
		$loader->load('services.yml');
		
		$entityToDocumentMapperDefinition = $container->getDefinition(EntityToDocumentMapperInterface::SERVICE_ID);
		$entityToDocumentMapperDefinition->addArgument($config['types']);
		
		$container->getDefinition('stinger_soft.entity_search.doctrine.listener')->addArgument($config['enable_indexing']);

		$facetFormDefinition = $container->getDefinition('stinger_soft.entity_search.forms.query_type');
		$facetFormDefinition->addArgument($config['results']);
	}
}
