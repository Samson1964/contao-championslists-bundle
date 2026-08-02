<?php

declare(strict_types=1);

/*
 * Dieses Bundle stellt die DSB-Meisterlisten für Contao 4.13 und Contao 5 bereit.
 *
 * @license LGPL-3.0-or-later
 */

/*
 * Bootstrap der Testsuite.
 *
 * Das Bundle wird ohne eigenes vendor-Verzeichnis entwickelt. Deshalb wird
 * zuerst nach einem lokalen Autoloader gesucht und anschließend nach der
 * Contao-Referenzinstallation, die die Contao-Klassen bereitstellt. Zusätzlich
 * werden die Klassen des Bundles über einen eigenen PSR-4-Autoloader geladen.
 */
$arrAutoloader = array(
	__DIR__.'/../vendor/autoload.php',
	__DIR__.'/../../../vendor/autoload.php',
	'F:/Claude/contao-test/vendor/autoload.php',
);

foreach ($arrAutoloader as $strFile)
{
	if (is_file($strFile))
	{
		require_once $strFile;
		break;
	}
}

spl_autoload_register(
	static function (string $strClass): void
	{
		$strPrefix = 'Schachbulle\\ContaoChampionslistsBundle\\';

		if (0 !== strpos($strClass, $strPrefix))
		{
			return;
		}

		$strRelative = substr($strClass, \strlen($strPrefix));

		if (0 === strpos($strRelative, 'Tests\\'))
		{
			$strFile = __DIR__.'/'.str_replace('\\', '/', substr($strRelative, 6)).'.php';
		}
		else
		{
			$strFile = __DIR__.'/../src/'.str_replace('\\', '/', $strRelative).'.php';
		}

		if (is_file($strFile))
		{
			require_once $strFile;
		}
	},
	true,
	// Vorrang vor einem eventuell installierten Composer-Paket, damit immer der
	// Arbeitsstand aus src/ getestet wird
	true
);
