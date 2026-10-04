<?php

declare(strict_types=1);

/*
 * Dieses Bundle stellt die DSB-Meisterlisten für Contao 4.13 und Contao 5 bereit.
 *
 * @license LGPL-3.0-or-later
 */

use Contao\CoreBundle\DataContainer\PaletteManipulator;

/*
 * Palette
 */
PaletteManipulator::create()
	->addLegend('championslists_legend', 'amg_legend', PaletteManipulator::POSITION_BEFORE)
	->addField(array('championslists', 'championslistsp'), 'championslists_legend', PaletteManipulator::POSITION_APPEND)
	->applyToPalette('default', 'tl_user_group')
;

/*
 * Felder
 */

// Meisterlisten, die Mitglieder der Gruppe sehen und bearbeiten dürfen
$GLOBALS['TL_DCA']['tl_user_group']['fields']['championslists'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_user_group']['championslists'],
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'foreignKey'              => 'tl_championslists.title',
	'eval'                    => array('multiple'=>true),
	'sql'                     => "blob NULL",
	'relation'                => array('type'=>'hasMany', 'load'=>'lazy'),
);

// Rechte an ganzen Meisterlisten: anlegen (schließt kopieren ein) und löschen
$GLOBALS['TL_DCA']['tl_user_group']['fields']['championslistsp'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_user_group']['championslistsp'],
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'options'                 => array('create', 'delete'),
	'reference'               => &$GLOBALS['TL_LANG']['MSC'],
	'eval'                    => array('multiple'=>true),
	'sql'                     => "blob NULL",
);
