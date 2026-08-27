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

	/**
	 * Altdatensätze aus der Zeit, in der singleSRC noch NOT NULL war, enthalten
	 * 16 Nullbytes statt NULL (MySQL füllt eine leere BINARY-Spalte so auf).
	 * Das muss wie "kein Bild ausgewählt" behandelt werden, ohne einen
	 * FilesModel-Aufruf oder eine Protokollmeldung auszulösen. Ohne Fallback
	 * lässt sich das ohne gebootetes Contao prüfen: Findet getImageData() den
	 * Sonderfall nicht, bricht der Aufruf an System::getContainer() ab statt
	 * still die leeren Bilddaten zu liefern.
	 */
	public function testNullByteUuidGiltAlsKeinBild(): void
	{
		$strNullUuid = str_repeat("\0", 16);

		$this->assertSame(Helper::getEmptyImageData(), Helper::getImageData($strNullUuid));
	}
}
