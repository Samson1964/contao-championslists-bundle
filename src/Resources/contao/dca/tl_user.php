<?php

declare(strict_types=1);

/*
 * Dieses Bundle stellt die DSB-Meisterlisten für Contao 4.13 und Contao 5 bereit.
 *
 * @license LGPL-3.0-or-later
 */

use Contao\CoreBundle\DataContainer\PaletteManipulator;

/*
 * Paletten
 *
 * Die Rechte erscheinen nur, wenn der Benutzer eigene Rechte hat ("extend")
 * oder ausschließlich eigene ("custom"). Erbt er alles von seinen Gruppen,
 * werden sie dort gepflegt (siehe tl_user_group.php).
 */
PaletteManipulator::create()
	->addLegend('championslists_legend', 'amg_legend', PaletteManipulator::POSITION_BEFORE)
	->addField(array('championslists', 'championslistsp'), 'championslists_legend', PaletteManipulator::POSITION_APPEND)
	->applyToPalette('extend', 'tl_user')
	->applyToPalette('custom', 'tl_user')
;

/*
 * Felder
 */

// Meisterlisten, die der Benutzer sehen und bearbeiten darf
$GLOBALS['TL_DCA']['tl_user']['fields']['championslists'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_user']['championslists'],
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'foreignKey'              => 'tl_championslists.title',
	'eval'                    => array('multiple'=>true),
	'sql'                     => "blob NULL",
	'relation'                => array('type'=>'hasMany', 'load'=>'lazy'),
);

// Rechte an ganzen Meisterlisten: anlegen (schließt kopieren ein) und löschen
$GLOBALS['TL_DCA']['tl_user']['fields']['championslistsp'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_user']['championslistsp'],
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'options'                 => array('create', 'delete'),
	'reference'               => &$GLOBALS['TL_LANG']['MSC'],
	'eval'                    => array('multiple'=>true),
	'sql'                     => "blob NULL",
);
