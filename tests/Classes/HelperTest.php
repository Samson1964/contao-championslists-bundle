<?php

declare(strict_types=1);

/*
 * Dieses Bundle stellt die DSB-Meisterlisten für Contao 4.13 und Contao 5 bereit.
 *
 * @license LGPL-3.0-or-later
 */

namespace Schachbulle\ContaoChampionslistsBundle\Tests\Classes;

use PHPUnit\Framework\TestCase;
use Schachbulle\ContaoChampionslistsBundle\Classes\Helper;

/**
 * Prüft die datenbankunabhängigen Hilfsfunktionen.
 */
class HelperTest extends TestCase
{
	/**
	 * Eine leere Bildgröße darf nicht als Array durchgereicht werden, sonst
	 * erzeugt der FigureBuilder eine leere Bildkonfiguration.
	 */
	public function testNormalizeSizeLiefertNullBeiLeerenAngaben(): void
	{
		$this->assertNull(Helper::normalizeSize(null));
		$this->assertNull(Helper::normalizeSize(''));
		$this->assertNull(Helper::normalizeSize(array()));
		$this->assertNull(Helper::normalizeSize(array('', '', '')));
		$this->assertNull(Helper::normalizeSize(serialize(array('', '', ''))));
	}

	public function testNormalizeSizeEntpacktSerialisierteWerte(): void
	{
		$this->assertSame(
			array('200', '150', 'crop'),
			Helper::normalizeSize(serialize(array('200', '150', 'crop')))
		);
	}

	public function testNormalizeSizeReichtBildgroessenIdDurch(): void
	{
		$this->assertSame(array('', '', '3'), Helper::normalizeSize(array('', '', '3')));
		$this->assertSame('3', Helper::normalizeSize('3'));
	}

	/**
	 * Die Templates greifen unbedingt auf diese Schlüssel zu. Fehlt einer,
	 * entstehen "Undefined array key"-Warnungen.
	 */
	public function testLeereBilddatenEnthaltenAlleTemplateSchluessel(): void
	{
		$arrImage = Helper::getEmptyImageData();

		foreach (array('singleSRC', 'src', 'imgSize', 'alt', 'imageTitle', 'caption') as $strKey)
		{
			$this->assertArrayHasKey($strKey, $arrImage);
			$this->assertSame('', $arrImage[$strKey]);
		}
	}
}
