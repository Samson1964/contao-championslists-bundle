<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2014 Leo Feyer
 *
 * @package   fh-counter
 * @author    Frank Hoppe
 * @license   GNU/LGPL
 * @copyright Frank Hoppe 2014
 */

namespace Schachbulle\ContaoChampionslistsBundle\Classes;

class Tags extends \Frontend
{

	public function Meister($strTag)
	{
		$arrSplit = explode('::', $strTag);

		// Inserttag {{meister::id|parameter}}
		// Liefert zu einer ID einer Meisterliste den Namen des aktuellen Meisters
		if($arrSplit[0] == 'meister' || $arrSplit[0] == 'cache_meister')
		{
			// Parameter angegeben?
			if(isset($arrSplit[1]))
			{
				$parameter = explode('|', $arrSplit[1]); // Parameter trennen
				if(!isset($parameter[1])) $parameter[1] = 'name';

				// Meister laden
				$objItem = $this->Database->prepare("SELECT * FROM tl_championslists_items WHERE pid = ? AND published = ? AND name <> ? ORDER BY year DESC")
				                          ->limit(1)
				                          ->execute($parameter[0], 1, '');
				if($objItem)
				{
					switch($parameter[1])
					{
						case 'name':
						default:
							return $objItem->name;
					}
				}
			}
		}

		return false; // Tag nicht dabei

	}
}
