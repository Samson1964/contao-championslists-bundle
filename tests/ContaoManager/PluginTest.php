<?php

declare(strict_types=1);

/*
 * Dieses Bundle stellt die DSB-Meisterlisten für Contao 4.13 und Contao 5 bereit.
 *
 * @license LGPL-3.0-or-later
 */

namespace Schachbulle\ContaoChampionslistsBundle\Tests\ContaoManager;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\DelegatingParser;
use PHPUnit\Framework\TestCase;
use Schachbulle\ContaoChampionslistsBundle\ContaoChampionslistsBundle;
use Schachbulle\ContaoChampionslistsBundle\ContaoManager\Plugin;

/**
 * Prüft die Registrierung des Bundles im Contao Manager.
 */
class PluginTest extends TestCase
{
	public function testRegistriertDasBundleNachDemCoreBundle(): void
	{
		$arrBundles = (new Plugin())->getBundles(new DelegatingParser());

		$this->assertCount(1, $arrBundles);
		$this->assertInstanceOf(BundleConfig::class, $arrBundles[0]);
		$this->assertSame(ContaoChampionslistsBundle::class, $arrBundles[0]->getName());
		$this->assertSame(array(ContaoCoreBundle::class), $arrBundles[0]->getLoadAfter());
	}
}
