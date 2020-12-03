<?php

/**
 * Paletten
 */
//$GLOBALS['TL_DCA']['tl_content']['palettes']['__selector__'][] = 'championslistType';
$GLOBALS['TL_DCA']['tl_content']['palettes']['__selector__'][] = 'championslist_alttemplate';
$GLOBALS['TL_DCA']['tl_content']['palettes']['__selector__'][] = 'championslist_filter';
$GLOBALS['TL_DCA']['tl_content']['palettes']['championslists'] = '{type_legend},type,headline;{champions_legend},championslist,championslist_alttemplate,championslist_filter;{protected_legend:hide},protected;{expert_legend:hide},guest,cssID,space;{invisible_legend:hide},invisible,start,stop';
$GLOBALS['TL_DCA']['tl_content']['palettes']['champion'] = '{type_legend},type,headline;{champions_legend},championslist,championslist_alttemplate;{protected_legend:hide},protected;{expert_legend:hide},guest,cssID,space;{invisible_legend:hide},invisible,start,stop';
$GLOBALS['TL_DCA']['tl_content']['palettes']['championslistsMono'] = '{type_legend},type,headline,championslistType;{champions_legend},championslist,championslist_alttemplate,championslist_filter;{protected_legend:hide},protected;{expert_legend:hide},guest,cssID,space;{invisible_legend:hide},invisible,start,stop';
$GLOBALS['TL_DCA']['tl_content']['palettes']['championslistsMulti'] = '{type_legend},type,headline,championslistType;{champions_legend},championslist,championslist_alttemplate,championslist_filter;{protected_legend:hide},protected;{expert_legend:hide},guest,cssID,space;{invisible_legend:hide},invisible,start,stop';
//
$GLOBALS['TL_DCA']['tl_content']['subpalettes']['championslist_alttemplate'] = 'championstemplate';
$GLOBALS['TL_DCA']['tl_content']['subpalettes']['championslist_filter'] = 'championsfrom,championsto';

/**
 * Felder
 */

// championslistsliste anzeigen

$GLOBALS['TL_DCA']['tl_content']['fields']['championslistType'] = array
(
	'label'                    => &$GLOBALS['TL_LANG']['tl_content']['championslistType'],
	'default'                  => 'championslistsMono',
	'exclude'                  => true,
	'inputType'                => 'radio',
	'options'                  => array('championslistsMono', 'championslistsMulti'),
	//'reference'                => &$GLOBALS['TL_LANG']['tl_content']['championslistControl'],
	'eval'                     => array
	(
		'helpwizard'           => true,
		'submitOnChange'       => true,
		'tl_class'             => 'clr'
	),
	'sql'                      => "varchar(20) NOT NULL default ''"
);

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
		'tl_class'             => 'w50 wizard widget'
	),
	'wizard'                   => array
	(
		array('tl_content_championslist', 'editListe')
	),
	'sql'                  => "int(10) unsigned NOT NULL default '0'"
);

$GLOBALS['TL_DCA']['tl_content']['fields']['championslist_alttemplate'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_content']['championslist_alttemplate'],
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'eval'                    => array('tl_class'=>'clr','isBoolean' => true,'submitOnChange'=>true),
	'sql'                     => "char(1) NOT NULL default ''",
);

$GLOBALS['TL_DCA']['tl_content']['fields']['championstemplate'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_content']['championstemplate'],
	'exclude'                 => true,
	'inputType'               => 'select',
	'options_callback'        => array('tl_content_championslist', 'getTemplates'),
	'eval'                    => array('mandatory'=>true, 'tl_class'=>'w50'),
	'sql'                     => "varchar(64) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_content']['fields']['championslist_filter'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_content']['championslist_filter'],
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'eval'                    => array('tl_class'=>'clr','isBoolean' => true,'submitOnChange'=>true),
	'sql'                     => "char(1) NOT NULL default ''",
);

$GLOBALS['TL_DCA']['tl_content']['fields']['championsfrom'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_content']['championsfrom'],
	'exclude'                 => true,
	'inputType'               => 'text',
	'eval'                    => array('mandatory'=>true, 'rgxp'=>'digit', 'tl_class'=>'w50', 'maxlength'=>4),
	'sql'                     => "varchar(4) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_content']['fields']['championsto'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_content']['championsto'],
	'exclude'                 => true,
	'inputType'               => 'text',
	'eval'                    => array('mandatory'=>true, 'rgxp'=>'digit', 'tl_class'=>'w50', 'maxlength'=>4),
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
		return ($dc->value < 1) ? '' : ' <a href="contao/main.php?do=championslists&amp;table=tl_championslists_items&amp;id=' . $dc->value . '&amp;popup=1&amp;rt=' . REQUEST_TOKEN . '" title="' . sprintf(specialchars($GLOBALS['TL_LANG']['tl_content']['editalias'][1]), $dc->value) . '" style="padding-left:3px" onclick="Backend.openModalIframe({\'width\':765,\'title\':\'' . specialchars(str_replace("'", "\\'", sprintf($GLOBALS['TL_LANG']['tl_content']['editalias'][1], $dc->value))) . '\',\'url\':this.href});return false">' . Image::getHtml('alias.gif', $GLOBALS['TL_LANG']['tl_content']['editalias'][0], 'style="vertical-align:top"') . '</a>';
	}

	public function getChampionslists(DataContainer $dc)
	{
		$array = array();
		$objAdresse = $this->Database->prepare("SELECT * FROM tl_championslists ORDER BY title ASC")->execute();
		while($objAdresse->next())
		{
			$array[$objAdresse->id] = $objAdresse->title;
		}
		return $array;

	}

	public function getTemplates($dc)
	{

		$template_prefix = $dc->activeRecord->type == 'champion' ? 'ce_champion_' : 'mod_championslists_';
		
		if(version_compare(VERSION.BUILD, '2.9.0', '>=') && version_compare(VERSION.BUILD, '4.8.0', '<'))
		{
			// Den 2. Parameter gibt es nur ab Contao 2.9 bis 4.7
			return $this->getTemplateGroup($template_prefix, $dc->activeRecord->id);
		}
		else
		{
			return $this->getTemplateGroup($template_prefix);
		}
	}

}
