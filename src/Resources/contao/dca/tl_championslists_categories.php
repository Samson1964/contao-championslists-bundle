<?php

declare(strict_types=1);

/*
 * Dieses Bundle stellt die DSB-Meisterlisten für Contao 4.13 und Contao 5 bereit.
 *
 * @license LGPL-3.0-or-later
 */

use Contao\Backend;
use Contao\Database;
use Contao\DataContainer;
use Contao\DC_Table;
use Contao\StringUtil;

/**
 * Tabelle tl_championslists_categories.
 */
$GLOBALS['TL_DCA']['tl_championslists_categories'] = array
(
	// Config
	'config' => array
	(
		'dataContainer'               => DC_Table::class,
		'switchToEdit'                => true,
		'enableVersioning'            => true,
		'sql' => array
		(
			'keys' => array
			(
				'id'    => 'primary',
				'alias' => 'index',
			),
		),
	),

	// List
	'list' => array
	(
		'sorting' => array
		(
			'mode'                    => DataContainer::MODE_SORTABLE,
			'fields'                  => array('title', 'alias'),
			'flag'                    => DataContainer::SORT_INITIAL_LETTER_ASC,
			'panelLayout'             => 'filter;sort,search,limit',
		),
		'label' => array
		(
			'fields'                  => array('title', 'alias'),
			'showColumns'             => true,
		),
		'global_operations' => array
		(
			'listen' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_championslists_categories']['listen'],
				'href'                => 'table=tl_championslists',
				'icon'                => 'bundles/contaochampionslists/images/icon.png',
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
				'label'               => &$GLOBALS['TL_LANG']['tl_championslists_categories']['edit'],
				'href'                => 'act=edit',
				'icon'                => 'edit.svg',
			),
			'copy' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_championslists_categories']['copy'],
				'href'                => 'act=copy',
				'icon'                => 'copy.svg',
			),
			'delete' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_championslists_categories']['delete'],
				'href'                => 'act=delete',
				'icon'                => 'delete.svg',
				'attributes'          => 'data-action="contao--scroll-offset#store" onclick="if(!confirm(\''.($GLOBALS['TL_LANG']['MSC']['deleteConfirm'] ?? '').'\'))return false;Backend.getScrollOffset()"',
			),
			'toggle' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_championslists_categories']['toggle'],
				'href'                => 'act=toggle&amp;field=published',
				'icon'                => 'visible.svg',
				'showInHeader'        => true,
			),
			'show' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_championslists_categories']['show'],
				'href'                => 'act=show',
				'icon'                => 'show.svg',
			),
		),
	),

	// Palettes
	'palettes' => array
	(
		'default'                     => '{title_legend},title,alias;{publish_legend},published',
	),

	// Fields
	'fields' => array
	(
		'id' => array
		(
			'sql'                     => "int(10) unsigned NOT NULL auto_increment",
		),
		'sorting' => array
		(
			'sql'                     => "int(10) unsigned NOT NULL default '0'",
		),
		'tstamp' => array
		(
			'sql'                     => "int(10) unsigned NOT NULL default '0'",
		),
		'title' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_championslists_categories']['title'],
			'exclude'                 => true,
			'search'                  => true,
			'inputType'               => 'text',
			'eval'                    => array
			(
				'mandatory'           => true,
				'maxlength'           => 64,
				'tl_class'            => 'w50',
			),
			'sql'                     => "varchar(64) NOT NULL default ''",
		),
		'alias' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_championslists_categories']['alias'],
			'exclude'                 => true,
			'search'                  => true,
			'inputType'               => 'text',
			'eval'                    => array
			(
				'rgxp'                => 'alias',
				'unique'              => true,
				'maxlength'           => 64,
				'doNotCopy'           => true,
				'tl_class'            => 'w50',
			),
			'save_callback' => array
			(
				array('tl_championslists_categories', 'generateAlias'),
			),
			'sql'                     => "varbinary(64) NOT NULL default ''",
		),
		'published' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_championslists_categories']['published'],
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

/**
 * Stellt die Callbacks der Tabelle tl_championslists_categories bereit.
 */
class tl_championslists_categories extends Backend
{
	/**
	 * Erzeugt bei Bedarf automatisch ein Alias aus dem Titel und stellt sicher,
	 * dass es eindeutig ist.
	 *
	 * @param mixed $varValue
	 *
	 * @throws Exception wenn das Alias reserviert ist oder bereits existiert
	 */
	public function generateAlias($varValue, DataContainer $dc): string
	{
		$varValue = (string) $varValue;
		$blnAutoAlias = false;

		// Alias aus dem Titel erzeugen, wenn keines angegeben wurde
		if ('' === $varValue)
		{
			$blnAutoAlias = true;
			$varValue = str_replace('id-', '', StringUtil::standardize(StringUtil::restoreBasicEntities($this->getTitle($dc))));
		}

		// Das Alias "meister" ist für den Turniersieger reserviert. Die
		// Sprachvariablen werden mit ?? abgesichert, weil sprintf() unter
		// strict_types mit null einen TypeError auslöst, falls die Sprachdatei
		// noch nicht geladen wurde.
		if ('meister' === $varValue)
		{
			throw new Exception(sprintf($GLOBALS['TL_LANG']['tl_championslists_categories']['error_alias'] ?? 'Das Alias "%s" ist reserviert.', $varValue));
		}

		$objAlias = Database::getInstance()
			->prepare('SELECT id FROM tl_championslists_categories WHERE alias=? AND id!=?')
			->execute($varValue, (int) $dc->id);

		if ($objAlias->numRows)
		{
			if (!$blnAutoAlias)
			{
				throw new Exception(sprintf($GLOBALS['TL_LANG']['ERR']['aliasExists'] ?? 'Das Alias "%s" ist bereits vergeben.', $varValue));
			}

			// Beim automatischen Alias die ID anhängen, um es eindeutig zu machen
			$varValue .= '-'.$dc->id;
		}

		return $varValue;
	}

	/**
	 * Liest den Titel des aktuellen Datensatzes aus der Datenbank.
	 *
	 * Der Titel steht in der Palette vor dem Alias und ist daher bereits
	 * gespeichert, wenn dieser Callback ausgeführt wird.
	 */
	private function getTitle(DataContainer $dc): string
	{
		$objRecord = Database::getInstance()
			->prepare('SELECT title FROM tl_championslists_categories WHERE id=?')
			->limit(1)
			->execute((int) $dc->id);

		return $objRecord->numRows ? (string) $objRecord->title : '';
	}
}
