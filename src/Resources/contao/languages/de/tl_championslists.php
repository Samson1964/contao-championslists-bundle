<?php

declare(strict_types=1);

/*
 * Dieses Bundle stellt die DSB-Meisterlisten für Contao 4.13 und Contao 5 bereit.
 *
 * @license LGPL-3.0-or-later
 */

/*
 * Listenansicht
 */
$GLOBALS['TL_LANG']['tl_championslists']['kategorien'] = array('Platzierungsnamen', 'Platzierungsnamen bearbeiten');

/*
 * Eingabemaske
 */
$GLOBALS['TL_LANG']['tl_championslists']['title_legend'] = 'Titel';
$GLOBALS['TL_LANG']['tl_championslists']['title'] = array('Listentitel', 'Name/Bezeichnung der Liste');

$GLOBALS['TL_LANG']['tl_championslists']['options_legend'] = 'Einstellungen';
$GLOBALS['TL_LANG']['tl_championslists']['typ'] = array('Listentyp', 'Legen Sie hier den Listentyp fest');

$GLOBALS['TL_LANG']['tl_championslists']['publish_legend'] = 'Veröffentlichung';
$GLOBALS['TL_LANG']['tl_championslists']['published'] = array('Veröffentlicht', 'Liste veröffentlicht');

/*
 * Buttons für Operationen
 */
$GLOBALS['TL_LANG']['tl_championslists']['new'] = array('Neue Liste', 'Neue Liste anlegen');
$GLOBALS['TL_LANG']['tl_championslists']['edit'] = array('Listeneinträge bearbeiten', 'Einträge der Liste %s bearbeiten');
$GLOBALS['TL_LANG']['tl_championslists']['editheader'] = array('Listenkonfiguration bearbeiten', 'Listenkonfiguration %s bearbeiten');
$GLOBALS['TL_LANG']['tl_championslists']['copy'] = array('Liste kopieren', 'Liste %s kopieren');
$GLOBALS['TL_LANG']['tl_championslists']['delete'] = array('Liste löschen', 'Liste %s löschen');
$GLOBALS['TL_LANG']['tl_championslists']['toggle'] = array('Liste veröffentlichen', 'Liste %s veröffentlichen');
$GLOBALS['TL_LANG']['tl_championslists']['show'] = array('Listendetails anzeigen', 'Details der Liste %s anzeigen');

/*
 * Optionslisten
 */
$GLOBALS['TL_LANG']['tl_championslists']['typen'] = array
(
	'E' => 'Einzelturnier',
	'F' => 'Einzelturnier (weiblich)',
	'M' => 'Mannschaftsturnier',
	'W' => 'Mannschaftsturnier (weiblich)',
);
