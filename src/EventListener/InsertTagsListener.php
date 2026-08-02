<?php

declare(strict_types=1);

/*
 * Dieses Bundle stellt die DSB-Meisterlisten für Contao 4.13 und Contao 5 bereit.
 *
 * @license LGPL-3.0-or-later
 */

namespace Schachbulle\ContaoChampionslistsBundle\EventListener;

use Contao\Database;

/**
 * Stellt den Inserttag {{meister::...}} bereit.
 *
 * Syntax: {{meister::<Listen-ID>}} oder {{meister::<Listen-ID>|<Feld>}}
 * Geliefert wird der aktuelle (jüngste) Meister der angegebenen Meisterliste.
 */
class InsertTagsListener
{
	/**
	 * Unterstützte Inserttags.
	 */
	private const SUPPORTED_TAGS = array('meister', 'cache_meister');

	/**
	 * Ersetzt den Inserttag.
	 *
	 * @param string $strTag Der komplette Inserttag ohne geschweifte Klammern
	 *
	 * @return string|false Der Ersatztext oder false, wenn der Tag nicht zuständig ist
	 */
	public function onReplaceInsertTags(string $strTag)
	{
		$arrSplit = explode('::', $strTag);

		if (!\in_array($arrSplit[0], self::SUPPORTED_TAGS, true) || !isset($arrSplit[1]))
		{
			return false;
		}

		$arrParameter = explode('|', $arrSplit[1]);
		$intListe = (int) $arrParameter[0];
		$strFeld = $arrParameter[1] ?? 'name';

		if ($intListe < 1)
		{
			return '';
		}

		$objItem = Database::getInstance()
			->prepare("SELECT name FROM tl_championslists_items WHERE pid=? AND published='1' AND name!='' ORDER BY year DESC")
			->limit(1)
			->execute($intListe);

		if ($objItem->numRows < 1)
		{
			return '';
		}

		switch ($strFeld)
		{
			case 'name':
			default:
				return (string) $objItem->name;
		}
	}
}
