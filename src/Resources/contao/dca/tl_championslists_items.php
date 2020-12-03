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
			'fields'                  => array('year DESC'),
			'headerFields'            => array('title', 'typ', 'templatefile'),
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
		'__selector__'                => array('person2', 'person3', 'person4', 'person5', 'person6'),
		'default'                     => '{place_legend},year,number,place,url,target;{info_legend},info;{person1_legend},name,age,clubrating,cowinner,singleSRC,spielerregister_id;{person2_legend},person2;{person3_legend},person3;{person4_legend},person4;{person5_legend},person5;{person6_legend},person6;{publish_legend},published'
	),

	// Subpalettes
	'subpalettes' => array
	(
		'person2'                     => 'typ2,name2,age2,clubrating2,singleSRC2,spielerregister_id2',
		'person3'                     => 'typ3,name3,age3,clubrating3,singleSRC3,spielerregister_id3',
		'person4'                     => 'typ4,name4,age4,clubrating4,singleSRC4,spielerregister_id4',
		'person5'                     => 'typ5,name5,age5,clubrating5,singleSRC5,spielerregister_id5',
		'person6'                     => 'typ6,name6,age6,clubrating6,singleSRC6,spielerregister_id6'
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
			'eval'                    => array('mandatory'=>true, 'rgxp'=>'digit', 'tl_class'=>'w50', 'maxlength'=>5),
			'sql'                     => "varchar(5) NOT NULL default ''"
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
			'eval'                    => array('rgxp'=>'url', 'decodeEntities'=>true, 'maxlength'=>255, 'fieldType'=>'radio', 'tl_class'=>'clr w50 wizard'),
			'wizard' => array
			(
				array('tl_championslists_items', 'pagePicker')
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
		'clubrating' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_championslists_items']['clubrating'],
			'exclude'                 => true,
			'search'                  => true,
			'flag'                    => 1,
			'inputType'               => 'text',
			'eval'                    => array('maxlength'=>255, 'tl_class'=>'w50'),
			'sql'                     => "varchar(255) NOT NULL default ''"
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
		'person2' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_championslists_items']['person2'],
			'inputType'               => 'checkbox',
			'filter'                  => true,
			'eval'                    => array('submitOnChange'=>true, 'tl_class'=>'clr'),
			'sql'                     => "char(1) NOT NULL default ''"
		),
		'person3' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_championslists_items']['person3'],
			'inputType'               => 'checkbox',
			'filter'                  => true,
			'eval'                    => array('submitOnChange'=>true, 'tl_class'=>'clr'),
			'sql'                     => "char(1) NOT NULL default ''"
		),
		'person4' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_championslists_items']['person4'],
			'inputType'               => 'checkbox',
			'filter'                  => true,
			'eval'                    => array('submitOnChange'=>true, 'tl_class'=>'clr'),
			'sql'                     => "char(1) NOT NULL default ''"
		),
		'person5' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_championslists_items']['person5'],
			'inputType'               => 'checkbox',
			'filter'                  => true,
			'eval'                    => array('submitOnChange'=>true, 'tl_class'=>'clr'),
			'sql'                     => "char(1) NOT NULL default ''"
		),
		'person6' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_championslists_items']['person6'],
			'inputType'               => 'checkbox',
			'filter'                  => true,
			'eval'                    => array('submitOnChange'=>true, 'tl_class'=>'clr'),
			'sql'                     => "char(1) NOT NULL default ''"
		),
		'typ2' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_championslists_items']['typ'],
			'exclude'                 => true,
			'default'                 => 1,
			'search'                  => true,
			'filter'                  => true,
			'sorting'                 => true,
			'flag'                    => 12,
			'inputType'               => 'select',
			'eval'                    => array('includeBlankOption' => true),
			'options'                 => $GLOBALS['TL_LANG']['tl_championslists_item']['typen'],
			'sql'                     => "char(1) NOT NULL default '1'"
		),
		'name2' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_championslists_items']['name2'],
			'exclude'                 => true,
			'search'                  => true,
			'sorting'                 => true,
			'flag'                    => 1,
			'inputType'               => 'text',
			'eval'                    => array('maxlength'=>255, 'tl_class'=>'w50'),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'age2' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_championslists_items']['age'],
			'exclude'                 => true,
			'inputType'               => 'text',
			'eval'                    => array('rgxp'=>'digit', 'tl_class'=>'w50', 'maxlength'=>5),
			'sql'                     => "varchar(5) NOT NULL default ''"
		),
		'clubrating2' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_championslists_items']['clubrating'],
			'exclude'                 => true,
			'search'                  => true,
			'flag'                    => 1,
			'inputType'               => 'text',
			'eval'                    => array('maxlength'=>255, 'tl_class'=>'w50'),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'singleSRC2' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_championslists_items']['singleSRC'],
			'exclude'                 => true,
			'inputType'               => 'fileTree',
			'eval'                    => array('filesOnly'=>true, 'fieldType'=>'radio', 'tl_class'=>'clr'),
			'sql'                     => "binary(16) NULL",
		),
		'spielerregister_id2' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_championslists_items']['spielerregister_id'],
			'exclude'                 => true,
			'options_callback'        => array('tl_championslists_items', 'getRegisterliste'),
			'inputType'               => 'select',
			'eval'                    => array
			(
				'mandatory'           => false,
				'multiple'            => false,
				'chosen'              => true,
				'submitOnChange'      => false,
				'tl_class'            => 'long'
			),
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		),
		'typ3' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_championslists_items']['typ'],
			'exclude'                 => true,
			'default'                 => 1,
			'search'                  => true,
			'filter'                  => true,
			'sorting'                 => true,
			'flag'                    => 12,
			'eval'                    => array('includeBlankOption' => true),
			'inputType'               => 'select',
			'options'                 => $GLOBALS['TL_LANG']['tl_championslists_item']['typen'],
			'sql'                     => "char(1) NOT NULL default '1'"
		),
		'name3' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_championslists_items']['name3'],
			'exclude'                 => true,
			'search'                  => true,
			'sorting'                 => true,
			'flag'                    => 1,
			'inputType'               => 'text',
			'eval'                    => array('maxlength'=>255, 'tl_class'=>'w50'),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'age3' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_championslists_items']['age'],
			'exclude'                 => true,
			'inputType'               => 'text',
			'eval'                    => array('rgxp'=>'digit', 'tl_class'=>'w50', 'maxlength'=>5),
			'sql'                     => "varchar(5) NOT NULL default ''"
		),
		'clubrating3' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_championslists_items']['clubrating'],
			'exclude'                 => true,
			'search'                  => true,
			'flag'                    => 1,
			'inputType'               => 'text',
			'eval'                    => array('maxlength'=>255, 'tl_class'=>'w50'),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'singleSRC3' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_championslists_items']['singleSRC'],
			'exclude'                 => true,
			'inputType'               => 'fileTree',
			'eval'                    => array('filesOnly'=>true, 'fieldType'=>'radio', 'tl_class'=>'clr'),
			'sql'                     => "binary(16) NULL",
		),
		'spielerregister_id3' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_championslists_items']['spielerregister_id'],
			'exclude'                 => true,
			'options_callback'        => array('tl_championslists_items', 'getRegisterliste'),
			'inputType'               => 'select',
			'eval'                    => array
			(
				'mandatory'           => false,
				'multiple'            => false,
				'chosen'              => true,
				'submitOnChange'      => false,
				'tl_class'            => 'long'
			),
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		),
		'typ4' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_championslists_items']['typ'],
			'exclude'                 => true,
			'default'                 => 1,
			'search'                  => true,
			'filter'                  => true,
			'sorting'                 => true,
			'flag'                    => 12,
			'eval'                    => array('includeBlankOption' => true),
			'inputType'               => 'select',
			'options'                 => $GLOBALS['TL_LANG']['tl_championslists_item']['typen'],
			'sql'                     => "char(1) NOT NULL default '1'"
		),
		'name4' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_championslists_items']['name4'],
			'exclude'                 => true,
			'search'                  => true,
			'sorting'                 => true,
			'flag'                    => 1,
			'inputType'               => 'text',
			'eval'                    => array('maxlength'=>255, 'tl_class'=>'w50'),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'age4' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_championslists_items']['age'],
			'exclude'                 => true,
			'inputType'               => 'text',
			'eval'                    => array('rgxp'=>'digit', 'tl_class'=>'w50', 'maxlength'=>5),
			'sql'                     => "varchar(5) NOT NULL default ''"
		),
		'clubrating4' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_championslists_items']['clubrating'],
			'exclude'                 => true,
			'search'                  => true,
			'flag'                    => 1,
			'inputType'               => 'text',
			'eval'                    => array('maxlength'=>255, 'tl_class'=>'w50'),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'singleSRC4' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_championslists_items']['singleSRC'],
			'exclude'                 => true,
			'inputType'               => 'fileTree',
			'eval'                    => array('filesOnly'=>true, 'fieldType'=>'radio', 'tl_class'=>'clr'),
			'sql'                     => "binary(16) NULL",
		),
		'spielerregister_id4' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_championslists_items']['spielerregister_id'],
			'exclude'                 => true,
			'options_callback'        => array('tl_championslists_items', 'getRegisterliste'),
			'inputType'               => 'select',
			'eval'                    => array
			(
				'mandatory'           => false,
				'multiple'            => false,
				'chosen'              => true,
				'submitOnChange'      => false,
				'tl_class'            => 'long'
			),
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		),
		'typ5' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_championslists_items']['typ'],
			'exclude'                 => true,
			'default'                 => 1,
			'search'                  => true,
			'filter'                  => true,
			'sorting'                 => true,
			'flag'                    => 12,
			'eval'                    => array('includeBlankOption' => true),
			'inputType'               => 'select',
			'options'                 => $GLOBALS['TL_LANG']['tl_championslists_item']['typen'],
			'sql'                     => "char(1) NOT NULL default '1'"
		),
		'name5' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_championslists_items']['name5'],
			'exclude'                 => true,
			'search'                  => true,
			'sorting'                 => true,
			'flag'                    => 1,
			'inputType'               => 'text',
			'eval'                    => array('maxlength'=>255, 'tl_class'=>'w50'),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'age5' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_championslists_items']['age'],
			'exclude'                 => true,
			'inputType'               => 'text',
			'eval'                    => array('rgxp'=>'digit', 'tl_class'=>'w50', 'maxlength'=>5),
			'sql'                     => "varchar(5) NOT NULL default ''"
		),
		'clubrating5' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_championslists_items']['clubrating'],
			'exclude'                 => true,
			'search'                  => true,
			'flag'                    => 1,
			'inputType'               => 'text',
			'eval'                    => array('maxlength'=>255, 'tl_class'=>'w50'),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'singleSRC5' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_championslists_items']['singleSRC'],
			'exclude'                 => true,
			'inputType'               => 'fileTree',
			'eval'                    => array('filesOnly'=>true, 'fieldType'=>'radio', 'tl_class'=>'clr'),
			'sql'                     => "binary(16) NULL",
		),
		'spielerregister_id5' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_championslists_items']['spielerregister_id'],
			'exclude'                 => true,
			'options_callback'        => array('tl_championslists_items', 'getRegisterliste'),
			'inputType'               => 'select',
			'eval'                    => array
			(
				'mandatory'           => false,
				'multiple'            => false,
				'chosen'              => true,
				'submitOnChange'      => false,
				'tl_class'            => 'long'
			),
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		),
		'typ6' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_championslists_items']['typ'],
			'exclude'                 => true,
			'default'                 => 1,
			'search'                  => true,
			'filter'                  => true,
			'sorting'                 => true,
			'flag'                    => 12,
			'eval'                    => array('includeBlankOption' => true),
			'inputType'               => 'select',
			'options'                 => $GLOBALS['TL_LANG']['tl_championslists_item']['typen'],
			'sql'                     => "char(1) NOT NULL default '1'"
		),
		'name6' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_championslists_items']['name6'],
			'exclude'                 => true,
			'search'                  => true,
			'sorting'                 => true,
			'flag'                    => 1,
			'inputType'               => 'text',
			'eval'                    => array('maxlength'=>255, 'tl_class'=>'w50'),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'age6' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_championslists_items']['age'],
			'exclude'                 => true,
			'inputType'               => 'text',
			'eval'                    => array('rgxp'=>'digit', 'tl_class'=>'w50', 'maxlength'=>5),
			'sql'                     => "varchar(5) NOT NULL default ''"
		),
		'clubrating6' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_championslists_items']['clubrating'],
			'exclude'                 => true,
			'search'                  => true,
			'flag'                    => 1,
			'inputType'               => 'text',
			'eval'                    => array('maxlength'=>255, 'tl_class'=>'w50'),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'singleSRC6' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_championslists_items']['singleSRC'],
			'exclude'                 => true,
			'inputType'               => 'fileTree',
			'eval'                    => array('filesOnly'=>true, 'fieldType'=>'radio', 'tl_class'=>'clr'),
			'sql'                     => "binary(16) NULL",
		),
		'spielerregister_id6' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_championslists_items']['spielerregister_id'],
			'exclude'                 => true,
			'options_callback'        => array('tl_championslists_items', 'getRegisterliste'),
			'inputType'               => 'select',
			'eval'                    => array
			(
				'mandatory'           => false,
				'multiple'            => false,
				'chosen'              => true,
				'submitOnChange'      => false,
				'tl_class'            => 'long'
			),
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
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
		'nomination2' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_championslists_items']['nomination2'],
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
		'nomination3' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_championslists_items']['nomination3'],
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

	/**
	 * Return the link picker wizard
	 * @param \DataContainer
	 * @return string
	 */
	public function pagePicker(DataContainer $dc)
	{
		return ' <a href="contao/page.php?do=' . Input::get('do') . '&amp;table=' . $dc->table . '&amp;field=' . $dc->field . '&amp;value=' . str_replace(array('{{link_url::', '}}'), '', $dc->value) . '" title="' . specialchars($GLOBALS['TL_LANG']['MSC']['pagepicker']) . '" onclick="Backend.getScrollOffset();Backend.openModalSelector({\'width\':765,\'title\':\'' . specialchars(str_replace("'", "\\'", $GLOBALS['TL_LANG']['MOD']['page'][0])) . '\',\'url\':this.href,\'id\':\'' . $dc->field . '\',\'tag\':\'ctrl_'. $dc->field . ((Input::get('act') == 'editAll') ? '_' . $dc->id : '') . '\',\'self\':this});return false">' . Image::getHtml('pickpage.gif', $GLOBALS['TL_LANG']['MSC']['pagepicker'], 'style="vertical-align:top;cursor:pointer"') . '</a>';
	}

	public function listPersons($arrRow)
	{
		$temp = '<div class="tl_content_left"><b>'.$arrRow['year'].'</b> ';
		if($arrRow['number']) $temp .= '['.$arrRow['number'].'] ';
		if($arrRow['place']) $temp .= $arrRow['place'].' - ';
		if($arrRow['name']) $temp .= '1. <b style="color:#007500">'.$arrRow['name'].'</b> ';

		if($GLOBALS['championslist-typ'] == 'E' || $GLOBALS['championslist-typ'] == 'F')
		{
			if($arrRow['singleSRC']) $temp .= '<img src="bundles/contaochampionslists/images/user-icon.png" title="Bild vorhanden">';
			if($arrRow['spielerregister_id']) $temp .= '<img src="bundles/contaochampionslists/images/register-icon.png" title="mit Spielerregister verknüpft">';
			// Restliche Spieler ausgeben
			for($nr = 2; $nr <= 6; $nr++)
			{
				if($arrRow['person'.$nr])
				{
					$typname = $GLOBALS['TL_LANG']['tl_championslists_item']['typen'][$arrRow['typ'.$nr]];
					$temp .= ' | <b>'.$arrRow['name'.$nr].'</b> (<i>'.$typname.'</i>) ';
					if($arrRow['singleSRC'.$nr]) $temp .= '<img src="bundles/contaochampionslists/images/user-icon.png" title="Bild vorhanden">';
					if($arrRow['spielerregister_id'.$nr]) $temp .= '<img src="bundles/contaochampionslists/images/register-icon.png" title="mit Spielerregister verknüpft">';
				}
			}
		}
		else
		{
			if($arrRow['singleSRC']) $temp .= '<img src="bundles/contaochampionslists/images/team-icon.png" title="Bild vorhanden">';
			// Restliche Mannschaften ausgeben
			if($arrRow['name2']) $temp .= ' 2. <b>'.$arrRow['name2'].'</b> ';
			if($arrRow['singleSRC2']) $temp .= '<img src="bundles/contaochampionslists/images/team-icon.png" title="Bild vorhanden">';
			if($arrRow['name3']) $temp .= ' 3. <b>'.$arrRow['name3'].'</b> ';
			if($arrRow['singleSRC3']) $temp .= '<img src="bundles/contaochampionslists/images/team-icon.png" title="Bild vorhanden">';
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
			PaletteManipulator::create()
			    ->removeField('age', 'person1_legend')
			    ->removeField('clubrating', 'person1_legend')
			    ->removeField('cowinner', 'person1_legend')
			    ->removeField('spielerregister_id', 'person1_legend')
			    ->removeField('person2', 'person2_legend')
			    ->removeField('person3', 'person3_legend')
			    ->removeField('person4', 'person4_legend')
			    ->removeField('person5', 'person5_legend')
			    ->removeField('person6', 'person6_legend')
			    ->addField('nomination', 'singleSRC', PaletteManipulator::POSITION_AFTER)
			    ->addLegend('person2_legend', 'person_legend', PaletteManipulator::POSITION_AFTER)
			    ->addLegend('person3_legend', 'person2_legend', PaletteManipulator::POSITION_AFTER)
			    ->addField('name2', 'person2_legend', PaletteManipulator::POSITION_APPEND)
			    ->addField('singleSRC2', 'person2_legend', PaletteManipulator::POSITION_APPEND)
			    ->addField('nomination2', 'person2_legend', PaletteManipulator::POSITION_APPEND)
			    ->addField('name3', 'person3_legend', PaletteManipulator::POSITION_APPEND)
			    ->addField('singleSRC3', 'person3_legend', PaletteManipulator::POSITION_APPEND)
			    ->addField('nomination3', 'person3_legend', PaletteManipulator::POSITION_APPEND)
			    ->applyToPalette('default', 'tl_championslists_items');
			// Sprachvariablen anpassen
			$GLOBALS['TL_LANG']['tl_championslists_items']['name'] = $GLOBALS['TL_LANG']['tl_championslists_items']['team_name'];
			$GLOBALS['TL_LANG']['tl_championslists_items']['name2'] = $GLOBALS['TL_LANG']['tl_championslists_items']['team_name2'];
			$GLOBALS['TL_LANG']['tl_championslists_items']['name3'] = $GLOBALS['TL_LANG']['tl_championslists_items']['team_name3'];
			$GLOBALS['TL_LANG']['tl_championslists_items']['person2_legend'] = $GLOBALS['TL_LANG']['tl_championslists_items']['team_person2_legend'];
			$GLOBALS['TL_LANG']['tl_championslists_items']['person3_legend'] = $GLOBALS['TL_LANG']['tl_championslists_items']['team_person3_legend'];
		}

	}

	public function getRegisterliste(DataContainer $dc)
	{

		return \Schachbulle\ContaoSpielerregisterBundle\Klassen\Helper::getRegister();

	}

}
