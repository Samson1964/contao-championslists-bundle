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
use Contao\Database\Result;
use Contao\StringUtil;
use Schachbulle\ContaoChampionslistsBundle\Classes\Helper;

/**
 * Gemeinsame Basis der Inhaltselemente "Meisterliste Einzelwettbewerb" und
 * "Meisterliste Mannschaftswettbewerb".
 *
 * Beide Elemente laden dieselben Datensätze und bereiten sie identisch auf.
 * Unterschiedlich sind lediglich die verwendeten Standardbilder, die Bildgröße
 * sowie die je Platzierung ausgegebenen Felder. Diese Unterschiede liefern die
 * abgeleiteten Klassen über die abstrakten Methoden.
 */
abstract class AbstractChampionslists extends ContentElement
{
	/**
	 * Liefert den Schlüssel der Systemeinstellung mit der Bildgröße.
	 */
	abstract protected function getImageSizeSetting(): string;

	/**
	 * Liefert die UUID des Standardbildes zum jeweiligen Listentyp.
	 *
	 * @return mixed
	 */
	abstract protected function getDefaultImage(string $strTyp);

	/**
	 * Liefert die typspezifischen Felder des Turniersiegers.
	 *
	 * @return array<string, mixed>
	 */
	abstract protected function getWinnerFields(Result $objItem): array;

	/**
	 * Liefert die typspezifischen Felder einer weiteren Platzierung.
	 *
	 * @param array<string, mixed> $arrPlatz Zeile des MultiColumnWizards
	 *
	 * @return array<string, mixed>
	 */
	abstract protected function getPlacementFields(array $arrPlatz): array;

	/**
	 * Stellt die Meisterliste für das Template zusammen.
	 */
	protected function compile(): void
	{
		// Template-Variablen immer vorbelegen, damit die Templates auch ohne
		// gültige Liste keine Warnungen erzeugen
		$this->Template->id = (int) $this->championslist;
		$this->Template->title = '';
		$this->Template->item = array();

		$objListe = $this->loadList();

		if (null === $objListe)
		{
			return;
		}

		$this->Template->title = $objListe->title;

		$objItems = $this->loadItems();

		if ($objItems->numRows < 1)
		{
			return;
		}

		$varSize = Config::get($this->getImageSizeSetting());
		$varDefaultImage = $this->getDefaultImage((string) $objListe->typ);
		$arrKategorien = Helper::getAliase();

		$arrItems = array();
		$i = 0;

		while ($objItems->next())
		{
			$strClass = ($i % 2) ? 'odd' : 'even';
			$strClass .= $objItems->failed ? ' failed' : '';

			$arrImage = Helper::getImageData($objItems->singleSRC, $varSize, $varDefaultImage, 'Eintrag-ID '.$objItems->id);

			$arrItem = array
			(
				'id'      => (int) $objItems->id,
				'nummer'  => $objItems->number,
				'jahr'    => $objItems->year,
				'class'   => $strClass,
				'ort'     => $objItems->place,
				'name'    => $objItems->name,
				'linkurl' => $objItems->url,
				'linkziel' => $objItems->target,
				'info'    => $objItems->info,
				'platz'   => array
				(
					'meister' => array_merge($this->getWinnerFields($objItems), $this->mapImage($arrImage)),
				),
			);

			// Weitere Platzierungen aus dem MultiColumnWizard
			foreach (StringUtil::deserialize($objItems->platzierungen, true) as $arrPlatz)
			{
				if (!\is_array($arrPlatz))
				{
					continue;
				}

				$intKategorie = (int) ($arrPlatz['platz'] ?? 0);

				// Platzierungen ohne (noch vorhandene) Kategorie überspringen
				if (!isset($arrKategorien[$intKategorie]))
				{
					continue;
				}

				$arrPlatzImage = Helper::getImageData(
					$arrPlatz['image'] ?? null,
					$varSize,
					$varDefaultImage,
					'Eintrag-ID '.$objItems->id.', Platzierung '.$intKategorie
				);

				$arrItem['platz'][$arrKategorien[$intKategorie]] = array_merge(
					$this->getPlacementFields($arrPlatz),
					$this->mapImage($arrPlatzImage)
				);
			}

			$arrItems[$i] = $arrItem;
			++$i;
		}

		$this->Template->item = $arrItems;
	}

	/**
	 * Lädt die Meisterliste, liefert null wenn sie nicht existiert.
	 */
	private function loadList(): ?Result
	{
		$intListe = (int) $this->championslist;

		if ($intListe < 1)
		{
			return null;
		}

		$objListe = Database::getInstance()
			->prepare('SELECT * FROM tl_championslists WHERE id=?')
			->limit(1)
			->execute($intListe);

		return $objListe->numRows ? $objListe : null;
	}

	/**
	 * Lädt die veröffentlichten Listeneinträge, optional nach Jahren gefiltert.
	 */
	private function loadItems(): Result
	{
		$intListe = (int) $this->championslist;

		if (!$this->championslist_filter || !$this->championsfrom || !$this->championsto)
		{
			return Database::getInstance()
				->prepare("SELECT * FROM tl_championslists_items WHERE pid=? AND published='1' ORDER BY year DESC, number DESC")
				->execute($intListe);
		}

		$intFrom = (int) $this->championsfrom;
		$intTo = (int) $this->championsto;

		// Aufsteigend sortieren, wenn das Startjahr vor dem Endjahr liegt
		$strOrder = $intFrom < $intTo ? 'ASC' : 'DESC';
		$intVon = min($intFrom, $intTo);
		$intBis = max($intFrom, $intTo);

		return Database::getInstance()
			->prepare("SELECT * FROM tl_championslists_items WHERE pid=? AND published='1' AND year>=? AND year<=? ORDER BY year $strOrder, number $strOrder")
			->execute($intListe, $intVon, $intBis);
	}

	/**
	 * Übersetzt die Contao-Bilddaten in die im Template verwendeten Schlüssel.
	 *
	 * @param array<string, mixed> $arrImage
	 *
	 * @return array<string, mixed>
	 */
	private function mapImage(array $arrImage): array
	{
		return array
		(
			'image'        => $arrImage['singleSRC'],
			'thumbnail'    => $arrImage['src'],
			'imageSize'    => $arrImage['imgSize'],
			'imageTitle'   => $arrImage['imageTitle'],
			'imageAlt'     => $arrImage['alt'],
			'imageCaption' => $arrImage['caption'],
		);
	}
}
