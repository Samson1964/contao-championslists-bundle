<?php

declare(strict_types=1);

/*
 * Dieses Bundle stellt die DSB-Meisterlisten für Contao 4.13 und Contao 5 bereit.
 *
 * @license LGPL-3.0-or-later
 */

namespace Schachbulle\ContaoChampionslistsBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * DependencyInjection-Extension des Meisterlisten-Bundles.
 *
 * Lädt die Service-Definitionen des Bundles in den Symfony-Container.
 */
class ContaoChampionslistsExtension extends Extension
{
	/**
	 * Lädt die Service-Konfiguration des Bundles in den Container.
	 *
	 * @param array<mixed> $configs Zusammengeführte Bundle-Konfiguration (hier ungenutzt)
	 */
	public function load(array $configs, ContainerBuilder $container): void
	{
		$loader = new YamlFileLoader(
			$container,
			new FileLocator(__DIR__.'/../Resources/config')
		);

		$loader->load('services.yaml');
	}
}
