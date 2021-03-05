<?php if (!defined('TL_ROOT')) die('You cannot access this file directly!');

/**
 * Contao Open Source CMS
 *
 * Copyright (C) 2005-2013 Leo Feyer
 *
 * @package   bdf
 * @author    Frank Hoppe
 * @license   GNU/LGPL
 * @copyright Frank Hoppe 2014
 */

$GLOBALS['BE_MOD']['content']['championslists'] = array
(
	'tables'         => array('tl_championslists', 'tl_championslists_categories', 'tl_championslists_items'),
	'icon'           => 'bundles/contaochampionslists/images/icon.png',
);

/**
 * -------------------------------------------------------------------------
 * CONTENT ELEMENTS
 * -------------------------------------------------------------------------
 */
$GLOBALS['TL_CTE']['schach']['championslists_mono'] = 'Schachbulle\ContaoChampionslistsBundle\ContentElements\ChampionslistsMono';
$GLOBALS['TL_CTE']['schach']['championslists_multi'] = 'Schachbulle\ContaoChampionslistsBundle\ContentElements\ChampionslistsMulti';
$GLOBALS['TL_CTE']['schach']['champion'] = 'Schachbulle\ContaoChampionslistsBundle\ContentElements\Champion';
