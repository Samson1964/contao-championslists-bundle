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
$GLOBALS['TL_DCA']['tl_settings']['palettes']['default'] .= ';{championslists_legend:hide},championslists_picWidthPlayer,championslists_picHeightPlayer,championslists_picWidthTeam,championslists_picHeightTeam';

/**
 * fields
 */

$GLOBALS['TL_DCA']['tl_settings']['fields']['championslists_picWidthPlayer'] = array
(
	'label'         => &$GLOBALS['TL_LANG']['tl_settings']['championslists_picWidthPlayer'],
	'inputType'     => 'text',
	'eval'          => array
	(
		'tl_class'  => 'w50',
		'rgxp'      => 'digit'
	)
);

$GLOBALS['TL_DCA']['tl_settings']['fields']['championslists_picHeightPlayer'] = array
(
	'label'         => &$GLOBALS['TL_LANG']['tl_settings']['championslists_picHeightPlayer'],
	'inputType'     => 'text',
	'eval'          => array
	(
		'tl_class'  => 'w50',
		'rgxp'      => 'digit'
	)
);

$GLOBALS['TL_DCA']['tl_settings']['fields']['championslists_picWidthTeam'] = array
(
	'label'         => &$GLOBALS['TL_LANG']['tl_settings']['championslists_picWidthTeam'],
	'inputType'     => 'text',
	'eval'          => array
	(
		'tl_class'  => 'w50',
		'rgxp'      => 'digit'
	)
);

$GLOBALS['TL_DCA']['tl_settings']['fields']['championslists_picHeightTeam'] = array
(
	'label'         => &$GLOBALS['TL_LANG']['tl_settings']['championslists_picHeightTeam'],
	'inputType'     => 'text',
	'eval'          => array
	(
		'tl_class'  => 'w50',
		'rgxp'      => 'digit'
	)
);
