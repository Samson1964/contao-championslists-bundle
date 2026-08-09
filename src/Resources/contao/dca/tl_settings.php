<?php

declare(strict_types=1);

/*
 * Dieses Bundle stellt die DSB-Meisterlisten für Contao 4.13 und Contao 5 bereit.
 *
 * @license LGPL-3.0-or-later
 */

use Contao\BackendUser;
use Contao\StringUtil;
use Contao\System;
use Contao\Validator;

/*
 * Paletten
 */
$GLOBALS['TL_DCA']['tl_settings']['palettes']['default'] .= ';{championslists_legend:hide},championslists_defaultImageMen,championslists_defaultImageWomen,championslists_imageSizePlayer,championslists_defaultImageTeamsMen,championslists_defaultImageTeamsWomen,championslists_imageSizeTeam';

/*
 * Felder
 */
$GLOBALS['TL_DCA']['tl_settings']['fields']['championslists_defaultImageMen'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['championslists_defaultImageMen'],
	'inputType'               => 'fileTree',
	'eval'                    => array
	(
		'filesOnly'           => true,
		'fieldType'           => 'radio',
		'extensions'          => 'jpg,jpeg,png,gif,webp',
		'tl_class'            => 'w50 clr',
	),
	// Der Dateibaum liefert die UUID als 16 Byte Binärwert. Die Einstellungen
	// landen aber in system/config/localconfig.php, also in einer PHP-Datei,
	// die den Binärwert nicht unbeschadet übersteht: Nullbytes und Backslashes
	// gehen dabei verloren, und FilesModel::findByUuid() findet die Datei
	// später nicht mehr. Deshalb wird hier in die lesbare Schreibweise
	// umgewandelt, die findByUuid() ebenso versteht. Gilt für alle vier
	// Standardbild-Felder dieser Datei.
	'save_callback' => array
	(
		static function ($varValue)
		{
			return Validator::isBinaryUuid($varValue) ? StringUtil::binToUuid($varValue) : $varValue;
		},
	),
);

$GLOBALS['TL_DCA']['tl_settings']['fields']['championslists_defaultImageWomen'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['championslists_defaultImageWomen'],
	'inputType'               => 'fileTree',
	'eval'                    => array
	(
		'filesOnly'           => true,
		'fieldType'           => 'radio',
		'extensions'          => 'jpg,jpeg,png,gif,webp',
		'tl_class'            => 'w50',
	),
	// Umwandlung der binären UUID, siehe Hinweis beim ersten Standardbild-Feld
	'save_callback' => array
	(
		static function ($varValue)
		{
			return Validator::isBinaryUuid($varValue) ? StringUtil::binToUuid($varValue) : $varValue;
		},
	),
);

$GLOBALS['TL_DCA']['tl_settings']['fields']['championslists_defaultImageTeamsMen'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['championslists_defaultImageTeamsMen'],
	'inputType'               => 'fileTree',
	'eval'                    => array
	(
		'filesOnly'           => true,
		'fieldType'           => 'radio',
		'extensions'          => 'jpg,jpeg,png,gif,webp',
		'tl_class'            => 'w50 clr',
	),
	// Umwandlung der binären UUID, siehe Hinweis beim ersten Standardbild-Feld
	'save_callback' => array
	(
		static function ($varValue)
		{
			return Validator::isBinaryUuid($varValue) ? StringUtil::binToUuid($varValue) : $varValue;
		},
	),
);

$GLOBALS['TL_DCA']['tl_settings']['fields']['championslists_defaultImageTeamsWomen'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['championslists_defaultImageTeamsWomen'],
	'inputType'               => 'fileTree',
	'eval'                    => array
	(
		'filesOnly'           => true,
		'fieldType'           => 'radio',
		'extensions'          => 'jpg,jpeg,png,gif,webp',
		'tl_class'            => 'w50',
	),
	// Umwandlung der binären UUID, siehe Hinweis beim ersten Standardbild-Feld
	'save_callback' => array
	(
		static function ($varValue)
		{
			return Validator::isBinaryUuid($varValue) ? StringUtil::binToUuid($varValue) : $varValue;
		},
	),
);

$GLOBALS['TL_DCA']['tl_settings']['fields']['championslists_imageSizePlayer'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['championslists_imageSizePlayer'],
	'exclude'                 => true,
	'inputType'               => 'imageSize',
	'reference'               => &$GLOBALS['TL_LANG']['MSC'],
	'eval'                    => array('rgxp'=>'natural', 'includeBlankOption'=>true, 'nospace'=>true, 'helpwizard'=>true, 'tl_class'=>'w50'),
	// Liefert die im System hinterlegten Bildgrößen als Auswahlliste, beschränkt
	// auf die Größen, die der angemeldete Benutzer sehen darf. Der Dienst heißt
	// seit Contao 5 "contao.image.sizes"; unter Contao 4.13 ist
	// "contao.image.image_sizes" nur noch ein Alias darauf, der alte Name führt
	// in Contao 5 dagegen zu einem Fehler.
	'options_callback' => static function (): array
	{
		return System::getContainer()->get('contao.image.sizes')->getOptionsForUser(BackendUser::getInstance());
	},
);

$GLOBALS['TL_DCA']['tl_settings']['fields']['championslists_imageSizeTeam'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['championslists_imageSizeTeam'],
	'exclude'                 => true,
	'inputType'               => 'imageSize',
	'reference'               => &$GLOBALS['TL_LANG']['MSC'],
	'eval'                    => array('rgxp'=>'natural', 'includeBlankOption'=>true, 'nospace'=>true, 'helpwizard'=>true, 'tl_class'=>'w50'),
	// Liefert die im System hinterlegten Bildgrößen als Auswahlliste, beschränkt
	// auf die Größen, die der angemeldete Benutzer sehen darf. Der Dienst heißt
	// seit Contao 5 "contao.image.sizes"; unter Contao 4.13 ist
	// "contao.image.image_sizes" nur noch ein Alias darauf, der alte Name führt
	// in Contao 5 dagegen zu einem Fehler.
	'options_callback' => static function (): array
	{
		return System::getContainer()->get('contao.image.sizes')->getOptionsForUser(BackendUser::getInstance());
	},
);
