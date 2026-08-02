<?php

declare(strict_types=1);

/*
 * Dieses Bundle stellt die DSB-Meisterlisten für Contao 4.13 und Contao 5 bereit.
 *
 * @license LGPL-3.0-or-later
 */

namespace Schachbulle\ContaoChampionslistsBundle\ContentElements;

use Contao\Config;
use Contao\ContentElement;
use Contao\Database;
use Schachbulle\ContaoChampionslistsBundle\Classes\Helper;

/**
 * Inhaltselement "Aktueller Meister".
 *
 * Gibt den jüngsten Eintrag einer Meisterliste aus, bei dem ein Name hinterlegt
 * ist. Die Bildgröße wird am Inhaltselement selbst eingestellt.
 */
class Champion extends ContentElement
{
	/**
	 * Template.
	 *
	 * @var string
	 */
	protected $strTemplate = 'ce_champion';

	/**
	 * Stellt den aktuellen Meister für das Template zusammen.
	 */
	protected function compile(): void
	{
		// Template-Variablen immer vorbelegen, damit das Template auch ohne
		// gültige Liste keine Warnungen erzeugt
		$this->Template->id = (int) $this->championslist;
		$this->Template->title = '';
		$this->Template->item = array();

		$intListe = (int) $this->championslist;

		if ($intListe < 1)
		{
			return;
		}

		$objDatabase = Database::getInstance();

		$objListe = $objDatabase
			->prepare('SELECT * FROM tl_championslists WHERE id=?')
			->limit(1)
			->execute($intListe);

		if ($objListe->numRows < 1)
		{
			return;
		}

		$this->Template->title = $objListe->title;

		$objItem = $objDatabase
			->prepare("SELECT * FROM tl_championslists_items WHERE pid=? AND published='1' AND name!='' ORDER BY year DESC")
			->limit(1)
			->execute($intListe);

		if ($objItem->numRows < 1)
		{
			return;
		}

		$arrImage = Helper::getImageData(
			$objItem->singleSRC,
			$this->size,
			$this->getDefaultImage((string) $objListe->typ),
			'Eintrag-ID '.$objItem->id
		);

		$this->Template->item = array
		(
			'id'           => (int) $objItem->id,
			'number'       => $objItem->number,
			'year'         => $objItem->year,
			'place'        => $objItem->place,
			'url'          => $objItem->url,
			'target'       => $objItem->target,
			'name'         => $objItem->name,
			'nomination'   => $objItem->nomination,
			'age'          => $objItem->age,
			'verein'       => $objItem->verein,
			'rating'       => $objItem->rating,
			// Für ältere eigene Templates: Verein und Wertungszahl kombiniert
			'clubrating'   => trim($objItem->verein.' '.$objItem->rating),
			'image'        => $arrImage['singleSRC'],
			'thumbnail'    => $arrImage['src'],
			'imageSize'    => $arrImage['imgSize'],
			'imageTitle'   => $arrImage['imageTitle'],
			'imageAlt'     => $arrImage['alt'],
			'imageCaption' => $arrImage['caption'],
			'info'         => $objItem->info,
		);
	}

	/**
	 * Liefert die UUID des Standardbildes zum Listentyp.
	 *
	 * @return mixed
	 */
	private function getDefaultImage(string $strTyp)
	{
		switch ($strTyp)
		{
			case 'F': // Einzelturnier (weiblich)
				return Config::get('championslists_defaultImageWomen') ?: Config::get('championslists_defaultImageMen');

			case 'M': // Mannschaftsturnier
				return Config::get('championslists_defaultImageTeamsMen');

			case 'W': // Mannschaftsturnier (weiblich)
				return Config::get('championslists_defaultImageTeamsWomen') ?: Config::get('championslists_defaultImageTeamsMen');

			default: // Einzelturnier
				return Config::get('championslists_defaultImageMen');
		}
	}
}
