<?php 

/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2014 Leo Feyer
 *
 */

// Listenansicht
$GLOBALS['TL_LANG']['tl_championslists_categories']['listen'] = array('Meisterlisten', 'Meisterlisten bearbeiten');

// Eingabemaske
$GLOBALS['TL_LANG']['tl_championslists_categories']['title_legend'] = 'Titel';
$GLOBALS['TL_LANG']['tl_championslists_categories']['title'] = array('Titel', 'Name/Bezeichnung der Platzierung, z.B. 2. Platz, Nestorenmeister. Bitte den Titel Meister bzw. das Alias davon nicht verwenden. Dieser ist reserviert für den Turniersieger.');
$GLOBALS['TL_LANG']['tl_championslists_categories']['alias'] = array('Alias', 'Lassen Sie das Feld leer, um beim Speichern ein automatisches Alias anzulegen. Das Alias wird als Referenz im Template verwendet.');

$GLOBALS['TL_LANG']['tl_championslists_categories']['publish_legend'] = 'Veröffentlichung';
$GLOBALS['TL_LANG']['tl_championslists_categories']['published'] = array('Veröffentlicht', 'Platzierungsname veröffentlicht');

// Meldungen
$GLOBALS['TL_LANG']['tl_championslists_categories']['error_alias'] = "Das Alias '%s' kan nicht erezugt/gespeichert werden, da es für den (Gesamt-)Sieger reserviert ist!";

/**
 * Buttons für Operationen
 */

$GLOBALS['TL_LANG']['tl_championslists_categories']['new'] = array('Neuer Platzierungsname', 'Neuen Platzierungsnamen anlegen');
$GLOBALS['TL_LANG']['tl_championslists_categories']['edit'] = array('Platzierungsname bearbeiten', 'Platzierungsname %s bearbeiten');
$GLOBALS['TL_LANG']['tl_championslists_categories']['copy'] = array('Platzierungsname kopieren', 'Platzierungsname %s kopieren');
$GLOBALS['TL_LANG']['tl_championslists_categories']['delete'] = array('Platzierungsname löschen', 'Platzierungsname %s löschen');
$GLOBALS['TL_LANG']['tl_championslists_categories']['toggle'] = array('Platzierungsname veröffentlichen', 'Platzierungsname %s veröffentlichen');
$GLOBALS['TL_LANG']['tl_championslists_categories']['show'] = array('Platzierungsnamendetails anzeigen', 'Details des Platzierungsnamen %s anzeigen');
