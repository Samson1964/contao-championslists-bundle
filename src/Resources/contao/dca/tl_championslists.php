<?php

declare(strict_types=1);

/*
 * Dieses Bundle stellt die DSB-Meisterlisten für Contao 4.13 und Contao 5 bereit.
 *
 * @license LGPL-3.0-or-later
 */

use Contao\DataContainer;
use Contao\DC_Table;

/**
 * Tabelle tl_championslists.
 */
$GLOBALS['TL_DCA']['tl_championslists'] = array
(
	// Config
	'config' => array
	(
		'dataContainer'               => DC_Table::class,
		'ctable'                      => array('tl_championslists_items'),
		'switchToEdit'                => true,
		'enableVersioning'            => true,
		'sql' => array
		(
			'keys' => array
			(
				'id'    => 'primary',
				'title' => 'index',
			),
		),
	),

	// List
	'list' => array
	(
		'sorting' => array
		(
			'mode'                    => DataContainer::MODE_SORTED,
			'fields'                  => array('title'),
			'flag'                    => DataContainer::SORT_INITIAL_LETTER_ASC,
			'panelLayout'             => 'filter;search,limit',
		),
		'label' => array
		(
			'fields'                  => array('title', 'typ'),
			'showColumns'             => true,
		),
		'global_operations' => array
		(
			'kategorien' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_championslists']['kategorien'],
				'href'                => 'table=tl_championslists_categories',
				'icon'                => 'bundles/contaochampionslists/images/kategorien.png',
				'attributes'          => 'data-action="contao--scroll-offset#store" onclick="Backend.getScrollOffset()"',
			),
			'all' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['MSC']['all'],
				'href'                => 'act=select',
				'class'               => 'header_edit_all',
				'attributes'          => 'data-action="contao--scroll-offset#store" onclick="Backend.getScrollOffset()" accesskey="e"',
			),
		),
		'operations' => array
		(
			'edit' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_championslists']['edit'],
				'href'                => 'table=tl_championslists_items',
				'icon'                => 'edit.svg',
			),
			'editheader' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_championslists']['editheader'],
				'href'                => 'act=edit',
				'icon'                => 'header.svg',
			),
			'copy' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_championslists']['copy'],
				'href'                => 'act=copy',
				'icon'                => 'copy.svg',
			),
			'delete' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_championslists']['delete'],
				'href'                => 'act=delete',
				'icon'                => 'delete.svg',
				'attributes'          => 'data-action="contao--scroll-offset#store" onclick="if(!confirm(\''.($GLOBALS['TL_LANG']['MSC']['deleteConfirm'] ?? '').'\'))return false;Backend.getScrollOffset()"',
			),
			'toggle' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_championslists']['toggle'],
				'href'                => 'act=toggle&amp;field=published',
				'icon'                => 'visible.svg',
				'showInHeader'        => true,
			),
			'show' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_championslists']['show'],
				'href'                => 'act=show',
				'icon'                => 'show.svg',
			),
		),
	),

	// Palettes
	'palettes' => array
	(
		'default'                     => '{title_legend},title;{options_legend},typ;{publish_legend},published',
	),

	// Fields
	'fields' => array
	(
		'id' => array
		(
			'sql'                     => "int(10) unsigned NOT NULL auto_increment",
		),
		'tstamp' => array
		(
			'sql'                     => "int(10) unsigned NOT NULL default '0'",
		),
		'title' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_championslists']['title'],
			'exclude'                 => true,
			'search'                  => true,
			'inputType'               => 'text',
			'eval'                    => array('mandatory'=>true, 'maxlength'=>255, 'tl_class'=>'long'),
			'sql'                     => "varchar(255) NOT NULL default ''",
		),
		'typ' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_championslists']['typ'],
			'exclude'                 => true,
			'filter'                  => true,
			'default'                 => 'E',
			'inputType'               => 'select',
			'options'                 => &$GLOBALS['TL_LANG']['tl_championslists']['typen'],
			'eval'                    => array
			(
				'doNotCopy'           => false,
				'tl_class'            => 'long',
			),
			'sql'                     => "char(1) NOT NULL default ''",
		),
		'published' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_championslists']['published'],
			'exclude'                 => true,
			'filter'                  => true,
			'flag'                    => DataContainer::SORT_INITIAL_LETTER_ASC,
			'default'                 => '1',
			'toggle'                  => true,
			'inputType'               => 'checkbox',
			'eval'                    => array
			(
				'doNotCopy'           => true,
				'isBoolean'           => true,
			),
			'sql'                     => "char(1) NOT NULL default ''",
		),
	),
);
