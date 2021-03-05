<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2014 Leo Feyer
 *
 * @package Core
 * @link    https://contao.org
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */

/**
 * Benutzerdefinierten Namespace festlegen, damit die Klasse ersetzt werden kann
 */
namespace Schachbulle\ContaoChampionslistsBundle\Classes;

class Helper
{

	/**
	 * Liefert die Aliase der Kategorien
	 */
	public static function getAliase()
	{
		$objKategorien = \Database::getInstance()->prepare("SELECT * FROM tl_championslists_categories")
		                                         ->execute();

		$array = array();
		while($objKategorien->next())
		{
			$array[$objKategorien->id] = $objKategorien->alias;
		}
		return $array;
	}

	/**
	 * Liefert die Titel der Kategorien
	 */
	public static function getTitles()
	{
		$objKategorien = \Database::getInstance()->prepare("SELECT * FROM tl_championslists_categories")
		                                         ->execute();

		$array = array();
		while($objKategorien->next())
		{
			$array[$objKategorien->id] = $objKategorien->title;
		}
		return $array;
	}

}
