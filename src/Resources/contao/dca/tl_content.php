<?php

/**
 * Paletten
 */
$GLOBALS['TL_DCA']['tl_content']['palettes']['__selector__'][] = 'championslist_filter';
$GLOBALS['TL_DCA']['tl_content']['palettes']['champion'] = '{type_legend},type,headline;{champions_legend},championslist;{sourcesize_legend},size,fullsize;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID;{invisible_legend:hide},invisible,start,stop';
$GLOBALS['TL_DCA']['tl_content']['palettes']['championslists_mono'] = '{type_legend},type,headline;{champions_legend},championslist,championslist_filter;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID;{invisible_legend:hide},invisible,start,stop';
$GLOBALS['TL_DCA']['tl_content']['palettes']['championslists_multi'] = '{type_legend},type,headline;{champions_legend},championslist,championslist_filter;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID;{invisible_legend:hide},invisible,start,stop';
//
$GLOBALS['TL_DCA']['tl_content']['subpalettes']['championslist_filter'] = 'championsfrom,championsto';

/**
 * Felder
 */

$GLOBALS['TL_DCA']['tl_content']['fields']['championslist'] = array
(
	'label'                    => &$GLOBALS['TL_LANG']['tl_content']['championslist'],
	'exclude'                  => true,
	'options_callback'         => array('tl_content_championslist', 'getChampionslists'),
	'inputType'                => 'select',
	'eval'                     => array
	(
		'mandatory'            => false,
		'multiple'             => false,
		'chosen'               => true,
		'submitOnChange'       => true,
		'tl_class'             => 'w50 wizard'
	),
	'wizard'                   => array
	(
		array('tl_content_championslist', 'editListe')
	),
	'sql'                  => "int(10) unsigned NOT NULL default '0'"
);

$GLOBALS['TL_DCA']['tl_content']['fields']['championslist_filter'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_content']['championslist_filter'],
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'eval'                    => array
	(
		'tl_class'            => 'clr',
		'isBoolean'           => true,
		'submitOnChange'      => true
	),
	'sql'                     => "char(1) NOT NULL default ''",
);

$GLOBALS['TL_DCA']['tl_content']['fields']['championsfrom'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_content']['championsfrom'],
	'exclude'                 => true,
	'inputType'               => 'text',
	'eval'                    => array
	(
		'mandatory'           => true,
		'rgxp'                => 'digit',
		'tl_class'            => 'w50',
		'maxlength'           => 4
	),
	'sql'                     => "varchar(4) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_content']['fields']['championsto'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_content']['championsto'],
	'exclude'                 => true,
	'inputType'               => 'text',
	'eval'                    => array
	(
		'mandatory'           => true,
		'rgxp'                => 'digit',
		'tl_class'            => 'w50',
		'maxlength'           => 4
	),
	'sql'                     => "varchar(4) NOT NULL default ''"
);

/*****************************************
 * Klasse tl_content_championslist
 *****************************************/

class tl_content_championslist extends \Backend
{

	/**
	 * Import the back end user object
	 */
	public function __construct()
	{
		parent::__construct();
		$this->import('BackendUser', 'User');
	}

	/**
	 * Funktion editAdresse
	 * @param \DataContainer
	 * @return string
	 */
	public function editListe(DataContainer $dc)
	{
		if ($dc->value < 1)
		{
			return '';
		}

		$title = sprintf($GLOBALS['TL_LANG']['tl_content']['editalias'], $dc->value);

		return ' <a href="contao/main.php?do=championslists&amp;table=tl_championslists_items&amp;act=edit&amp;id=' . $dc->value . '&amp;popup=1&amp;nb=1&amp;rt=' . REQUEST_TOKEN . '" title="' . StringUtil::specialchars($title) . '" onclick="Backend.openModalIframe({\'title\':\'' . StringUtil::specialchars(str_replace("'", "\\'", $title)) . '\',\'url\':this.href});return false">' . Image::getHtml('alias.svg', $title) . '</a>';
	}

	public function getChampionslists(DataContainer $dc)
	{
		// Meisterlisten nach Einzel- (E,F) und Mannschaftswettbewerben (M,W) unterscheiden
		if($dc->activeRecord->type == 'championslists_multi') $filter = ' WHERE typ = \'M\' OR typ = \'W\'';
		else $filter = ' WHERE typ = \'E\' OR typ = \'F\'';

		$array = array();
		$objAdresse = $this->Database->prepare("SELECT * FROM tl_championslists".$filter." ORDER BY title ASC")
		                             ->execute();

		while($objAdresse->next())
		{
			$array[$objAdresse->id] = $objAdresse->title;
		}
		return $array;

	}

}
