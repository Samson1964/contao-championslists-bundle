<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2014 Leo Feyer
 *
 * @package News
 * @link    https://contao.org
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */

use Contao\CoreBundle\DataContainer\PaletteManipulator;

/**
 * Table tl_championslists_items
 */
$GLOBALS['TL_DCA']['tl_championslists_items'] = array
(

	// Config
	'config' => array
	(
		'dataContainer'               => 'Table',
		'ptable'                      => 'tl_championslists',
		'switchToEdit'                => true,
		'enableVersioning'            => true,
		'onload_callback'             => array
		(
			array('tl_championslists_items', 'checkPalette'),
		),
		'sql' => array
		(
			'keys' => array
			(
				'id' => 'primary',
				'pid' => 'index',
			)
		)
	),

	// List
	'list' => array
	(
		'sorting' => array
		(
			'mode'                    => 4,
			'disableGrouping'         => true,
			'fields'                  => array('year DESC', 'number ASC'),
			'headerFields'            => array('title', 'typ'),
			'panelLayout'             => 'filter;sort,search,limit',
			'child_record_callback'   => array('tl_championslists_items', 'listPersons'),
		),
		'global_operations' => array
		(
			'all' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['MSC']['all'],
				'href'                => 'act=select',
				'class'               => 'header_edit_all',
				'attributes'          => 'onclick="Backend.getScrollOffset()" accesskey="e"'
			)
		),
		'operations' => array
		(
			'edit' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_championslists_items']['edit'],
				'href'                => 'act=edit',
				'icon'                => 'edit.gif'
			),
			'copy' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_championslists_items']['copy'],
				'href'                => 'act=paste&amp;mode=copy',
				'icon'                => 'copy.gif'
			),
			'cut' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_championslists_items']['cut'],
				'href'                => 'act=paste&amp;mode=cut',
				'icon'                => 'cut.gif'
			),
			'delete' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_championslists_items']['delete'],
				'href'                => 'act=delete',
				'icon'                => 'delete.gif',
				'attributes'          => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"'
			),
			'toggle' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_championslists_items']['toggle'],
				'icon'                => 'visible.gif',
				'attributes'          => 'onclick="Backend.getScrollOffset();return AjaxRequest.toggleVisibility(this,%s)"',
				'button_callback'       => array('tl_championslists_items', 'toggleIcon')
			),
			'show' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_championslists_items']['show'],
				'href'                => 'act=show',
				'icon'                => 'show.gif'
			)
		)
	),

	// Palettes
	'palettes' => array
	(
		'default'                     => '{place_legend},year,failed,number,place,url,target;{info_legend},info;{person1_legend},name,age,verein,rating,cowinner,singleSRC,spielerregister_id;{platzierungen_legend:hide},platzierungen;{publish_legend},published'
	),

	// Fields
	'fields' => array
	(
		'id' => array
		(
			'sql'                     => "int(10) unsigned NOT NULL auto_increment"
		),
		'pid' => array
		(
			'foreignKey'              => 'tl_championslists.title',
			'sql'                     => "int(10) unsigned NOT NULL default '0'",
			'relation'                => array('type'=>'belongsTo', 'load'=>'eager')
		),
		'tstamp' => array
		(
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		),
		'year' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_championslists_items']['year'],
			'exclude'                 => true,
			'sorting'                 => true,
			'flag'                    => 12,
			'inputType'               => 'text',
			'eval'                    => array('mandatory'=>true, 'rgxp'=>'alnum', 'tl_class'=>'w50', 'maxlength'=>10),
			'sql'                     => "varchar(10) NOT NULL default ''"
		),
		'failed' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_championslists_items']['failed'],
			'exclude'                 => true,
			'inputType'               => 'checkbox',
			'eval'                    => array('tl_class'=>'w50 m12'),
			'sql'                     => "char(1) NOT NULL default ''"
		),
		'number' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_championslists_items']['number'],
			'exclude'                 => true,
			'sorting'                 => true,
			'inputType'               => 'text',
			'eval'                    => array('rgxp'=>'digit', 'tl_class'=>'clr w50', 'maxlength'=>5),
			'sql'                     => "varchar(5) NOT NULL default ''"
		),
		'place' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_championslists_items']['place'],
			'exclude'                 => true,
			'search'                  => true,
			'sorting'                 => true,
			'flag'                    => 1,
			'inputType'               => 'text',
			'eval'                    => array('maxlength'=>255, 'tl_class'=>'w50'),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'url' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_championslists_items']['url'],
			'exclude'                 => true,
			'search'                  => true,
			'inputType'               => 'text',
			'eval'                    => array
			(
				'mandatory'           => false,
				'rgxp'                => 'url',
				'decodeEntities'      => true,
				'maxlength'           => 255,
				'dcaPicker'           => true,
				'addWizardClass'      => false,
				'tl_class'            => 'w50'
			),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'target' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_championslists_items']['target'],
			'exclude'                 => true,
			'inputType'               => 'checkbox',
			'eval'                    => array('tl_class'=>'w50 m12'),
			'sql'                     => "char(1) NOT NULL default ''"
		),
		'name' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_championslists_items']['name'],
			'exclude'                 => true,
			'search'                  => true,
			'sorting'                 => true,
			'flag'                    => 1,
			'inputType'               => 'text',
			'eval'                    => array('maxlength'=>255, 'tl_class'=>'w50'),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'age' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_championslists_items']['age'],
			'exclude'                 => true,
			'inputType'               => 'text',
			'eval'                    => array('rgxp'=>'digit', 'tl_class'=>'w50', 'maxlength'=>5),
			'sql'                     => "varchar(5) NOT NULL default ''"
		),
		'verein' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_championslists_items']['verein'],
			'exclude'                 => true,
			'search'                  => true,
			'flag'                    => 1,
			'inputType'               => 'text',
			'eval'                    => array('maxlength'=>40, 'tl_class'=>'w50'),
			'sql'                     => "varchar(40) NOT NULL default ''"
		),
		'rating' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_championslists_items']['rating'],
			'exclude'                 => true,
			'search'                  => true,
			'flag'                    => 1,
			'inputType'               => 'text',
			'eval'                    => array('maxlength'=>40, 'tl_class'=>'w50'),
			'sql'                     => "varchar(40) NOT NULL default ''"
		),
		'cowinner' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_championslists_items']['cowinner'],
			'exclude'                 => true,
			'inputType'               => 'checkbox',
			'eval'                    => array('tl_class'=>'w50 m12'),
			'sql'                     => "char(1) NOT NULL default ''"
		),
		'singleSRC' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_championslists_items']['singleSRC'],
			'exclude'                 => true,
			'inputType'               => 'fileTree',
			'eval'                    => array('filesOnly'=>true, 'fieldType'=>'radio', 'tl_class'=>'clr'),
			'sql'                     => "binary(16) NULL",
		),
		'spielerregister_id' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_championslists_items']['spielerregister_id'],
			'exclude'                 => true,
			'options_callback'        => array('\Schachbulle\ContaoSpielerregisterBundle\Klassen\Helper', 'getRegister'),
			'inputType'               => 'select',
			'eval'                    => array
			(
				'mandatory'           => false,
				'multiple'            => false,
				'chosen'              => true,
				'submitOnChange'      => false,
				'includeBlankOption'  => true,
				'tl_class'            => 'long'
			),
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		),
		'platzierungen' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_championslists_items']['platzierungen'],
			'exclude'                 => true,
			'inputType'               => 'multiColumnWizard',
			'eval'                    => array
			(
				'tl_class'            => 'long',
				'buttonPos'           => 'top',
				'columnFields'        => array
				(
					'platz' => array
					(
						'label'                   => &$GLOBALS['TL_LANG']['tl_championslists_items']['platzierungen_platz'],
						'exclude'                 => true,
						'inputType'               => 'select',
						'foreignKey'              => 'tl_championslists_categories.title',
						'eval'                    => array
						(
							'includeBlankOption'  => true,
							'columnPos'           => 'spalte1',
							'valign'              => 'top',
							'style'               => 'width:180px'
						),
					),
					'name' => array
					(
						'label'                   => &$GLOBALS['TL_LANG']['tl_championslists_items']['platzierungen_name'],
						'exclude'                 => true,
						'inputType'               => 'text',
						'eval'                    => array
						(
							'maxlength'           => 40,
							'columnPos'           => 'spalte2',
							'valign'              => 'top',
							'style'               => 'width:350px'
						),
					),
					'verein' => array
					(
						'label'                   => &$GLOBALS['TL_LANG']['tl_championslists_items']['platzierungen_verein'],
						'exclude'                 => true,
						'inputType'               => 'text',
						'eval'                    => array
						(
							'maxlength'           => 40,
							'columnPos'           => 'spalte3',
							'valign'              => 'top',
							'style'               => 'width:250px'
						),
					),
					'alter' => array
					(
						'label'                   => &$GLOBALS['TL_LANG']['tl_championslists_items']['platzierungen_alter'],
						'exclude'                 => true,
						'inputType'               => 'text',
						'eval'                    => array
						(
							'maxlength'           => 40,
							'columnPos'           => 'spalte3',
							'valign'              => 'top',
							'style'               => 'width:100px'
						),
					),
					'rating' => array
					(
						'label'                   => &$GLOBALS['TL_LANG']['tl_championslists_items']['platzierungen_rating'],
						'exclude'                 => true,
						'inputType'               => 'text',
						'eval'                    => array
						(
							'maxlength'           => 40,
							'columnPos'           => 'spalte3',
							'valign'              => 'top',
							'style'               => 'width:100px'
						),
					),
					'image' => array
					(
						'label'                   => &$GLOBALS['TL_LANG']['tl_championslists_items']['platzierungen_image'],
						'exclude'                 => true,
						'inputType'               => 'fileTree',
						'eval'                    => array
						(
							'filesOnly'           => true,
							'fieldType'           => 'radio',
							'columnPos'           => 'spalte4',
							'valign'              => 'top',
							'style'               => 'width:250px'
						),
					),
					'spielerregister' => array
					(
						'label'                   => &$GLOBALS['TL_LANG']['tl_championslists_items']['platzierungen_spielerregister'],
						'exclude'                 => true,
						'options_callback'        => array('\Schachbulle\ContaoSpielerregisterBundle\Klassen\Helper', 'getRegister'),
						'inputType'               => 'select',
						'eval'                    => array
						(
							'mandatory'           => false,
							'multiple'            => false,
							'chosen'              => true,
							'submitOnChange'      => false,
							'includeBlankOption'  => true,
							'columnPos'           => 'spalte2',
							'valign'              => 'top',
							'style'               => 'width:350px'
						),
					),
					'aufstellung' => array
					(
						'label'                   => &$GLOBALS['TL_LANG']['tl_championslists_items']['platzierungen_aufstellung'],
						'exclude'                 => true,
						'search'                  => true,
						'inputType'               => 'textarea',
						'eval'                    => array
						(
							'class'               => 'noresize',
							'columnPos'           => 'spalte2',
							'style'               => 'width:350px'
						),
						'explanation'             => 'insertTags',
					),
				)
			),
			'sql'                     => "blob NULL"
		),
		'nomination' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_championslists_items']['nomination'],
			'exclude'                 => true,
			'search'                  => true,
			'inputType'               => 'textarea',
			'eval'                    => array
			(
				'class'               => 'clr noresize',
			),
			'explanation'             => 'insertTags',
			'sql'                     => "mediumtext NULL"
		),
		'info' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_championslists_items']['info'],
			'exclude'                 => true,
			'search'                  => true,
			'inputType'               => 'textarea',
			'eval'                    => array('rte'=>'tinyMCE', 'helpwizard'=>true),
			'explanation'             => 'insertTags',
			'sql'                     => "mediumtext NULL"
		),
		'published' => array
		(
			'label'                     => &$GLOBALS['TL_LANG']['tl_championslists_items']['published'],
			'exclude'                   => true,
			'search'                    => false,
			'sorting'                   => false,
			'filter'                    => true,
			'inputType'                 => 'checkbox',
			'eval'                      => array('tl_class' => 'w50','isBoolean' => true),
			'sql'                       => "char(1) NOT NULL default ''"
		),
	)
);

// Listentyp mittels Abfrage ermitteln
//$GLOBALS['championslist-typ'] = 'E';
//$objItem = \Database::getInstance()->prepare("SELECT pid FROM tl_championslists_items WHERE id=?")
//                                   ->limit(1)
//                                   ->execute($dc->id);
//$objListe = \Database::getInstance()->prepare("SELECT typ FROM tl_championslists WHERE id=?")
//                                    ->limit(1)
//                                    ->execute($objItem->pid);
//
//if($objListe->typ == 'M' || $objListe->typ == 'W') $GLOBALS['championslist-typ'] = $objListe->typ;
//
//// DCA ändern, wenn Mannschaftseingabe
//if($GLOBALS['championslist-typ'] == 'M' || $GLOBALS['championslist-typ'] == 'W')
//{
//	$GLOBALS['TL_DCA']['tl_championslists_items']['fields']['platzierungen']['eval']['columnFields']['alter'] = array();
//}

/**
 * Class tl_championslists_items
 *
 * Provide miscellaneous methods that are used by the data configuration array.
 * @copyright  Leo Feyer 2005-2014
 * @author     Leo Feyer <https://contao.org>
 * @package    News
 */
class tl_championslists_items extends Backend
{

	var $nummer = 0;

	/**
	 * Import the back end user object
	 */
	public function __construct()
	{
		parent::__construct();
		$this->import('BackendUser', 'User');
	}

	/**
	 * Ändert das Aussehen des Toggle-Buttons.
	 * @param $row
	 * @param $href
	 * @param $label
	 * @param $title
	 * @param $icon
	 * @param $attributes
	 * @return string
	 */
	public function toggleIcon($row, $href, $label, $title, $icon, $attributes)
	{
		$this->import('BackendUser', 'User');

		if (strlen($this->Input->get('tid')))
		{
			$this->toggleVisibility($this->Input->get('tid'), ($this->Input->get('state') == 0));
			$this->redirect($this->getReferer());
		}

		// Check permissions AFTER checking the tid, so hacking attempts are logged
		if (!$this->User->isAdmin && !$this->User->hasAccess('tl_championslists_items::published', 'alexf'))
		{
			return '';
		}

		$href .= '&amp;id='.$this->Input->get('id').'&amp;tid='.$row['id'].'&amp;state='.$row[''];

		if (!$row['published'])
		{
			$icon = 'invisible.gif';
		}

		return '<a href="'.$this->addToUrl($href).'" title="'.specialchars($title).'"'.$attributes.'>'.$this->generateImage($icon, $label).'</a> ';
	}

	/**
	 * Toggle the visibility of an element
	 * @param integer
	 * @param boolean
	 */
	public function toggleVisibility($intId, $blnPublished)
	{
		// Check permissions to publish
		if (!$this->User->isAdmin && !$this->User->hasAccess('tl_championslists_items::published', 'alexf'))
		{
			$this->log('Not enough permissions to show/hide record ID "'.$intId.'"', 'tl_championslists_items toggleVisibility', TL_ERROR);
			$this->redirect('contao/main.php?act=error');
		}

		$this->createInitialVersion('tl_championslists_items', $intId);

		// Trigger the save_callback
		if (is_array($GLOBALS['TL_DCA']['tl_championslists_items']['fields']['published']['save_callback']))
		{
			foreach ($GLOBALS['TL_DCA']['tl_championslists_items']['fields']['published']['save_callback'] as $callback)
			{
				$this->import($callback[0]);
				$blnPublished = $this->$callback[0]->$callback[1]($blnPublished, $this);
			}
		}

		// Update the database
		$this->Database->prepare("UPDATE tl_championslists_items SET tstamp=". time() .", published='" . ($blnPublished ? '' : '1') . "' WHERE id=?")
		               ->execute($intId);
		$this->createNewVersion('tl_championslists_items', $intId);
	}


	public function listPersons($arrRow)
	{
		$kategorien = \Schachbulle\ContaoChampionslistsBundle\Classes\Helper::getTitles();

		$failed_style = $arrRow['failed'] ? 'background-color:#FFD2D2;' : '';
		$failed_info = $arrRow['failed'] ? 'Veranstaltung ist ausgefallen' : '';
		$temp = '<div class="tl_content_left" style="'.$failed_style.'" title="'.$failed_info.'"><b>'.$arrRow['year'].'</b> ';
		if($arrRow['url']) $temp .= '<img src="bundles/contaochampionslists/images/link-add-icon.png" title="Link zur Detailseite vorhanden"> ';
		else $temp .= '<img src="bundles/contaochampionslists/images/link-delete-icon.png" title="Link zur Detailseite nicht vorhanden"> ';
		if($arrRow['number']) $temp .= '['.$arrRow['number'].'] ';
		if($arrRow['place']) $temp .= $arrRow['place'].' - ';
		if($arrRow['name']) $temp .= '1. <b style="color:#007500">'.$arrRow['name'].'</b> ';

		if($GLOBALS['championslist-typ'] == 'E' || $GLOBALS['championslist-typ'] == 'F')
		{
			if($arrRow['singleSRC']) $temp .= '<img src="bundles/contaochampionslists/images/user-icon.png" title="Bild vorhanden">';
			if($arrRow['spielerregister_id']) $temp .= '<img src="bundles/contaochampionslists/images/register-icon.png" title="mit Spielerregister verknüpft">';
			// Restliche Spieler ausgeben
			if($arrRow['platzierungen'])
			{
				$platzierungen = unserialize($arrRow['platzierungen']);
				if($platzierungen)
				{
					foreach($platzierungen as $platz)
					{
						if($platz['platz'])
						{
							$temp .= ' | <b>'.$platz['name'].'</b> (<i>'.$kategorien[$platz['platz']].'</i>) ';
							if($platz['image']) $temp .= '<img src="bundles/contaochampionslists/images/user-icon.png" title="Bild vorhanden">';
							if($platz['spielerregister']) $temp .= '<img src="bundles/contaochampionslists/images/register-icon.png" title="mit Spielerregister verknüpft">';
						}
					}
				}
			}
		}
		else
		{
			if($arrRow['singleSRC']) $temp .= '<img src="bundles/contaochampionslists/images/team-icon.png" title="Bild vorhanden">';
			// Restliche Mannschaften ausgeben
			if($arrRow['platzierungen'])
			{
				$platzierungen = unserialize($arrRow['platzierungen']);
				if($platzierungen)
				{
					foreach($platzierungen as $platz)
					{
						if($platz['platz'])
						{
							$temp .= ' | <b>'.$platz['name'].'</b> (<i>'.$kategorien[$platz['platz']].'</i>) ';
							if($platz['image']) $temp .= '<img src="bundles/contaochampionslists/images/team-icon.png" title="Bild vorhanden">';
						}
					}
				}
			}
		}

		return $temp.'</div>';
	}

	/**
	 * Palette entsprechend Listentyp anpassen
	 */
	public function checkPalette(DataContainer $dc)
	{

		// Listentyp mittels Abfrage ermitteln
		$GLOBALS['championslist-typ'] = 'E';
		$objItem = $this->Database->prepare("SELECT pid FROM tl_championslists_items WHERE id=?")
		                 ->limit(1)
		                 ->execute($dc->id);
		$objListe = $this->Database->prepare("SELECT typ FROM tl_championslists WHERE id=?")
		                 ->limit(1)
		                 ->execute($objItem->pid);

		if($objListe->typ == 'M' || $objListe->typ == 'W')
		{
			// Mannschaftsturnier männlich/weiblich
			$GLOBALS['championslist-typ'] = $objListe->typ;
			//PaletteManipulator::create()
			//    ->removeField('age', 'person1_legend')
			//    ->removeField('clubrating', 'person1_legend')
			//    ->removeField('cowinner', 'person1_legend')
			//    ->removeField('spielerregister_id', 'person1_legend')
			//    ->removeField('person2', 'person2_legend')
			//    ->removeField('person3', 'person3_legend')
			//    ->removeField('person4', 'person4_legend')
			//    ->removeField('person5', 'person5_legend')
			//    ->removeField('person6', 'person6_legend')
			//    ->addField('nomination', 'singleSRC', PaletteManipulator::POSITION_AFTER)
			//    ->addLegend('person2_legend', 'person_legend', PaletteManipulator::POSITION_AFTER)
			//    ->addLegend('person3_legend', 'person2_legend', PaletteManipulator::POSITION_AFTER)
			//    ->addField('name2', 'person2_legend', PaletteManipulator::POSITION_APPEND)
			//    ->addField('singleSRC2', 'person2_legend', PaletteManipulator::POSITION_APPEND)
			//    ->addField('nomination2', 'person2_legend', PaletteManipulator::POSITION_APPEND)
			//    ->addField('name3', 'person3_legend', PaletteManipulator::POSITION_APPEND)
			//    ->addField('singleSRC3', 'person3_legend', PaletteManipulator::POSITION_APPEND)
			//    ->addField('nomination3', 'person3_legend', PaletteManipulator::POSITION_APPEND)
			//    ->applyToPalette('default', 'tl_championslists_items');
			//// Sprachvariablen anpassen
			//$GLOBALS['TL_LANG']['tl_championslists_items']['name'] = $GLOBALS['TL_LANG']['tl_championslists_items']['team_name'];
			//$GLOBALS['TL_LANG']['tl_championslists_items']['name2'] = $GLOBALS['TL_LANG']['tl_championslists_items']['team_name2'];
			//$GLOBALS['TL_LANG']['tl_championslists_items']['name3'] = $GLOBALS['TL_LANG']['tl_championslists_items']['team_name3'];
			//$GLOBALS['TL_LANG']['tl_championslists_items']['person2_legend'] = $GLOBALS['TL_LANG']['tl_championslists_items']['team_person2_legend'];
			//$GLOBALS['TL_LANG']['tl_championslists_items']['person3_legend'] = $GLOBALS['TL_LANG']['tl_championslists_items']['team_person3_legend'];
		}

	}

	public function getRegisterliste(DataContainer $dc)
	{

		return \Schachbulle\ContaoSpielerregisterBundle\Klassen\Helper::getRegister();

	}

}
