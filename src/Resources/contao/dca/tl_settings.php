<?php if (!defined('TL_ROOT')) die('You cannot access this file directly!');

/**
 * Contao Open Source CMS
 *
 * Copyright (C) 2005-2013 Leo Feyer
 *
 * @package   fen
 * @author    Frank Hoppe
 * @license   GNU/LGPL
 * @copyright Frank Hoppe 2013
 */

/**
 * palettes
 */
$GLOBALS['TL_DCA']['tl_settings']['palettes']['default'] .= ';{championslists_legend:hide},championslists_defaultImageMen,championslists_defaultImageWomen,championslists_imageSizePlayer,championslists_defaultImageTeamsMen,championslists_defaultImageTeamsWomen,championslists_imageSizeTeam';

/**
 * fields
 */

$GLOBALS['TL_DCA']['tl_settings']['fields']['championslists_defaultImageMen'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['championslists_defaultImageMen'],
	'inputType'               => 'fileTree',
	'eval'                    => array
	(
		'filesOnly'           => true,
		'fieldType'           => 'radio',
		'tl_class'            => 'w50 clr'
	)
);

$GLOBALS['TL_DCA']['tl_settings']['fields']['championslists_defaultImageWomen'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['championslists_defaultImageWomen'],
	'inputType'               => 'fileTree',
	'eval'                    => array
	(
		'filesOnly'           => true,
		'fieldType'           => 'radio',
		'tl_class'            => 'w50'
	)
);

$GLOBALS['TL_DCA']['tl_settings']['fields']['championslists_defaultImageTeamsMen'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['championslists_defaultImageTeamsMen'],
	'inputType'               => 'fileTree',
	'eval'                    => array
	(
		'filesOnly'           => true,
		'fieldType'           => 'radio',
		'tl_class'            => 'w50 clr'
	)
);

$GLOBALS['TL_DCA']['tl_settings']['fields']['championslists_defaultImageTeamsWomen'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['championslists_defaultImageTeamsWomen'],
	'inputType'               => 'fileTree',
	'eval'                    => array
	(
		'filesOnly'           => true,
		'fieldType'           => 'radio',
		'tl_class'            => 'w50'
	)
);

$GLOBALS['TL_DCA']['tl_settings']['fields']['championslists_imageSizePlayer'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['championslists_imageSizePlayer'],
	'exclude'                 => true,
	'inputType'               => 'imageSize',
	'reference'               => &$GLOBALS['TL_LANG']['MSC'],
	'eval'                    => array('rgxp'=>'natural', 'includeBlankOption'=>true, 'nospace'=>true, 'helpwizard'=>true, 'tl_class'=>'w50'),
	'options_callback' => static function ()
	{
		return System::getContainer()->get('contao.image.image_sizes')->getOptionsForUser(BackendUser::getInstance());
	},
	'sql'                     => "varchar(255) NOT NULL default ''"
); 

$GLOBALS['TL_DCA']['tl_settings']['fields']['championslists_imageSizeTeam'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['championslists_imageSizeTeam'],
	'exclude'                 => true,
	'inputType'               => 'imageSize',
	'reference'               => &$GLOBALS['TL_LANG']['MSC'],
	'eval'                    => array('rgxp'=>'natural', 'includeBlankOption'=>true, 'nospace'=>true, 'helpwizard'=>true, 'tl_class'=>'w50'),
	'options_callback' => static function ()
	{
		return System::getContainer()->get('contao.image.image_sizes')->getOptionsForUser(BackendUser::getInstance());
	},
	'sql'                     => "varchar(255) NOT NULL default ''"
); 
