<?php

declare(strict_types=1);

/*
 * Dieses Bundle stellt die DSB-Meisterlisten für Contao 4.13 und Contao 5 bereit.
 *
 * @license LGPL-3.0-or-later
 */

namespace Schachbulle\ContaoChampionslistsBundle\ContaoManager;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use Schachbulle\ContaoChampionslistsBundle\ContaoChampionslistsBundle;

/**
 * Contao-Manager-Plugin zur Registrierung des Meisterlisten-Bundles.
 *
 * Über dieses Plugin erkennt der Contao Manager das Bundle automatisch und lädt
 * es nach dem Contao-Core-Bundle, damit dessen Dienste bereitstehen.
 */
class Plugin implements BundlePluginInterface
{
	/**
	 * Registriert das Bundle im Contao-Kernel.
	 *
	 * @return array<BundleConfig>
	 */
	public function getBundles(ParserInterface $parser): array
	{
		return [
			BundleConfig::create(ContaoChampionslistsBundle::class)
				->setLoadAfter([ContaoCoreBundle::class]),
		];
	}
}
