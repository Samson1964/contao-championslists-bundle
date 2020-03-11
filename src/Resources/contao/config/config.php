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
	'tables'         => array('tl_championslists', 'tl_championslists_items'),
	'icon'           => 'system/modules/championslists/assets/images/icon.png',
);

/**
 * -------------------------------------------------------------------------
 * CONTENT ELEMENTS
 * -------------------------------------------------------------------------
 */
$GLOBALS['TL_CTE']['schach']['championslists'] = 'ChampionslistClass';

/**
 * -------------------------------------------------------------------------
 * Voreinstellungen
 * -------------------------------------------------------------------------
 */

$GLOBALS['TL_CONFIG']['championslists_picWidthPlayer'] = 60;
$GLOBALS['TL_CONFIG']['championslists_picHeightPlayer'] = 80;
$GLOBALS['TL_CONFIG']['championslists_picWidthTeam'] = 100;
$GLOBALS['TL_CONFIG']['championslists_picHeightTeam'] = 60;

// Konfiguration für ProSearch
$GLOBALS['PS_SEARCHABLE_MODULES']['championslists'] = array(
	'icon'           => 'system/modules/championslists/assets/images/icon.png',
	'title'          => array('title','name'),
	'searchIn'       => array('title','name', 'nomination', 'info'),
	'tables'         => array('tl_championslists', 'tl_championslists_items'),
	'shortcut'       => 'clist'
);